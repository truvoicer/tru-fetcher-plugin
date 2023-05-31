<?php

namespace TruFetcher\Includes\Firebase;

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Device;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Topic;
use TruFetcher\Includes\Tru_Fetcher_Helpers;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_User;

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Firebase_Messaging
{
    use Tru_Fetcher_Traits_Errors, Tru_Fetcher_Traits_User;
    const REGISTER_TOKEN = 'register_token';
    const ALLOW_NOTIFICATIONS = 'allow_notifications';
    const CONFIG_FILE_EXT = '.json';
    const CONFIG_FILE = 'google/firebase/client_credentials';
    const CONFIG_PATH = TRU_FETCHER_PLUGIN_DIR . 'config/';
    private Factory $factory;
    private Messaging $messaging;

    private Tru_Fetcher_DB_Repository_Device $deviceRepository;
    private Tru_Fetcher_DB_Repository_Topic $topicRepository;
    private Tru_Fetcher_DB_Model_Device $deviceModel;
    private Tru_Fetcher_DB_Model_Topic $topicModel;

    private array $messageData;

    public function __construct() {
        $this->deviceModel = new Tru_Fetcher_DB_Model_Device();
        $this->topicModel = new Tru_Fetcher_DB_Model_Topic();
        $this->deviceRepository = new Tru_Fetcher_DB_Repository_Device();
        $this->topicRepository = new Tru_Fetcher_DB_Repository_Topic();
        $this->initialiseFirebaseMessaging();
    }

    public function initialiseFirebaseMessaging()
    {
        $path = self::CONFIG_PATH;
        $config = self::CONFIG_FILE;
        $ext = self::CONFIG_FILE_EXT;
        if (!Tru_Fetcher_Helpers::getConfigContents("{$config}")) {
            return false;
        }
        $this->factory = (new Factory)->withServiceAccount("{$path}{$config}{$ext}");
        $this->messaging = $this->factory->createMessaging();
    }

    public function buildMessageData(string $title, string $body, string $uploadKey)
    {
        $this->messageData = [
            'title' => $title,
            'body' => $body,
        ];

        $uploadImage = $this->handleUpload($uploadKey);
        if (is_wp_error($uploadImage)) {
            $this->addError($uploadImage);
            return $this;
        }
        $this->messageData['image'] = $uploadImage;
        return $this;
    }
    private function validateMessageData() {
        if (!isset($this->messageData)) {
            return false;
        }
        if (!array_key_exists('title', $this->messageData)) {
            return false;
        }
        if (!array_key_exists('body', $this->messageData)) {
            return false;
        }
        if (array_key_exists('image', $this->messageData) && !$this->messageData['image']) {
            return false;
        }
        return true;
    }
    public function sendMessageToTopicFromRequest(\WP_REST_Request $request)
    {
        $allTopics =  Tru_Fetcher_Helpers::getRequestBooleanValue($request->get_param('all_topics'));
        $topics =  $request->get_param('topics');
        $title =  $request->get_param('title');
        $message =  $request->get_param('message');
        if (empty($topics)) {
            return false;
        }

        $topics = array_map(function ($value) {
            return (int) $value;
        }, array_map('trim', explode(',', $topics)));

        $sendMessage = $this->buildMessageData($title, $message, 'image')
            ->sendMessageToTopic($topics, $allTopics);
        return $sendMessage;
    }
    public function sendMessageToTopic(array $topics, bool $allTopics)
    {
        if ($allTopics) {
            $getAllTopics = $this->topicRepository->findMany();
            $topicNames = $this->topicRepository->getTopicNames($getAllTopics);

        } else {
            $this->topicRepository->addWhere('id', $topics, Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_IN);
            $getTopics = $this->topicRepository->findMany();
            $topicNames = array_map(function ($topic) {
                return $topic[$this->topicModel->getTopicNameColumn()];
            }, $getTopics);
        }
        if (!count($topicNames)) {
            return new \WP_Error('no_topics', 'No topics found');
        }
        $messages = [];
        foreach ($topicNames as $topicName) {
            if (!$this->validateMessageData()) {
                continue;
            }
            $messages[] = CloudMessage
                ::withTarget('topic', trim($topicName))
                ->withNotification($this->buildNotification($this->messageData));
        }
        return $this->multicastMessageResponseHandler(
            $this->messaging->sendAll($messages)
        );
    }
    public function sendMessageToDeviceFromRequest(\WP_REST_Request $request)
    {
        $allDevices =  Tru_Fetcher_Helpers::getRequestBooleanValue($request->get_param('all_devices'));
        $devices =  $request->get_param('devices');
        $title =  $request->get_param('title');
        $message =  $request->get_param('message');
        if (empty($devices)) {
            return false;
        }
        $devices = array_map(function ($value) {
            return (int) $value;
        }, array_map('trim', explode(',', $devices)));

        $sendMessage = $this->buildMessageData($title, $message, 'image')
            ->sendMessageToDevice($devices, $allDevices);
        return $sendMessage;
    }

    /**
     * @throws \Kreait\Firebase\Exception\MessagingException
     * @throws \Kreait\Firebase\Exception\FirebaseException
     */
    public function sendMessageToDevice(array $devices, bool $allDevices)
    {
        $deviceTokens = [];
        if ($allDevices) {
            $getAllDevices = $this->deviceRepository->findMany();
            $deviceTokens = $this->deviceRepository->getDeviceRegisterTokens($getAllDevices);
        } else {
            foreach ($devices as $device) {
                $findDevice = $this->deviceRepository->findById($device);
                if ($findDevice) {
                    $deviceTokens[] = $findDevice[$this->deviceModel->getRegisterTokenColumn()];
                }
            }
        }
        if (!$this->validateMessageData()) {
            return false;
        }
        $message = CloudMessage::new()
            ->withNotification($this->buildNotification($this->messageData));
        return $this->multicastMessageResponseHandler(
            $this->messaging->sendMulticast($message, $deviceTokens)
        );
    }

    private function buildNotification(array $buildData)
    {
        $notification = Notification::create($buildData['title'], $buildData['body']);
        $notification = $notification->withTitle($buildData['title']);
        $notification = $notification->withBody($buildData['body']);
        if ($buildData['image'] && is_string($buildData['image'])) {
            $notification = $notification->withImageUrl($buildData['image']);
        }
        return $notification;
    }

    private function multicastMessageResponseHandler(Messaging\MulticastSendReport $report): array
    {
        $response = [
            "count" => $report->count(),
            "validTokens" => $report->validTokens(),
            "unknownTokens" => $report->unknownTokens(),
            "invalidTokens" => $report->invalidTokens(),
            'failures' => [
                'count' => $report->failures()->count(),
                'data' => [],
            ],
            'successes' => [
                'count' => $report->successes()->count(),
                'data' => [],
            ],
        ];
        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $item) {
                $response["failures"]['data'][] = [
                    "error" => $item->error()->getMessage(),
                    "target" => [
                        "type" => $item->target()->type(),
                        "value" => $item->target()->value(),
                    ],
                    "message" => $item->message()->jsonSerialize(),
                    "messageWasInvalid" => $item->messageWasInvalid(),
                    "messageWasSentToUnknownToken" => $item->messageWasSentToUnknownToken(),
                    "messageTargetWasInvalid" => $item->messageTargetWasInvalid(),
                ];
            }
        }
        if ($report->successes()->count()) {
            foreach ($report->successes()->getItems() as $item) {
                $response["successes"]['data'][] = [
                    "result" => $item->result(),
                    "target" => [
                        "type" => $item->target()->type(),
                        "value" => $item->target()->value(),
                    ],
                    "message" => $item->message()->jsonSerialize(),
                ];
            }
        }
        return $response;
    }

    private function handleUpload(string $key)
    {
        if (!isset($_FILES[$key]) || !is_array($_FILES[$key])) {
            return new \WP_Error('tr_news_app_upload_no_data', __('No data supplied.'), ['key' => $key]);
        }
        $saveImage = \media_handle_upload(
            $key,
            0
        );

        if (is_wp_error($saveImage)) {
            return $saveImage;
        }
        $imageUrl = wp_get_attachment_image_url($saveImage);
        if (!$imageUrl) {
            return new \WP_Error('tr_news_app_no_url', __('Image URL cannot be determined.'));
        }
        return $imageUrl;
    }

    /**
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * @return Messaging
     */
    public function getMessaging(): Messaging
    {
        return $this->messaging;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Device
     */
    public function getDeviceRepository(): Tru_Fetcher_DB_Repository_Device
    {
        return $this->deviceRepository;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Topic
     */
    public function getTopicRepository(): Tru_Fetcher_DB_Repository_Topic
    {
        return $this->topicRepository;
    }

    /**
     * @return Tru_Fetcher_DB_Model_Device
     */
    public function getDeviceModel(): Tru_Fetcher_DB_Model_Device
    {
        return $this->deviceModel;
    }

    /**
     * @return Tru_Fetcher_DB_Model_Topic
     */
    public function getTopicModel(): Tru_Fetcher_DB_Model_Topic
    {
        return $this->topicModel;
    }

}
