<?php

namespace TruFetcher\Includes\Firebase\Helpers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Device;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_User_Device_Access;
use TruFetcher\Includes\Firebase\Tru_Fetcher_Firebase_Messaging;
use TruFetcher\Includes\Tru_Fetcher_Helpers;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
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
class Tru_Fetcher_Firebase_Helpers
{
    use Tru_Fetcher_Traits_Errors, Tru_Fetcher_Traits_User;

    private Factory $factory;
    private Messaging $messaging;
    private Tru_Fetcher_DB_Engine $database;

    private Tru_Fetcher_DB_Repository_User_Device_Access $userDeviceAccessRepository;
    private Tru_Fetcher_DB_Repository_Device $deviceRepository;
    private Tru_Fetcher_DB_Repository_Topic $topicRepository;
    private Tru_Fetcher_DB_Model_Device $deviceModel;
    private Tru_Fetcher_DB_Model_Topic $topicModel;

    public function __construct()
    {
        $this->deviceModel = new Tru_Fetcher_DB_Model_Device();
        $this->topicModel = new Tru_Fetcher_DB_Model_Topic();
        $this->deviceRepository = new Tru_Fetcher_DB_Repository_Device();
        $this->topicRepository = new Tru_Fetcher_DB_Repository_Topic();
        $this->userDeviceAccessRepository = new Tru_Fetcher_DB_Repository_User_Device_Access();
    }

    public function validateUserDeviceFromRequest(\WP_REST_Request $request, \WP_User $user)
    {
        $deviceRegisterToken = $request->get_param(Tru_Fetcher_Firebase_Messaging::REGISTER_TOKEN);
        if (!$deviceRegisterToken) {
            $this->addError(
                new \WP_Error(
                    'tr_news_app_firebase_device_register_token_error',
                    'Device register token is required'
                )
            );
            return false;
        }
        $allowNotifications = Tru_Fetcher_Helpers::getRequestBooleanValue(
            $request->get_param(Tru_Fetcher_Firebase_Messaging::ALLOW_NOTIFICATIONS)
        );

        $device = $this->deviceRepository->fetchUserDevice($user);
        if (!$device) {
            $device = $this->deviceRepository->fetchDeviceByRegisterToken($deviceRegisterToken);
        }

        if (!$device) {
            $device = $this->registerDevice($deviceRegisterToken, $allowNotifications);
        }
        if (is_wp_error($device)) {
            $this->addError($device);
            return false;
        }
        if (!$device) {
            $this->addError(
                new \WP_Error(
                    'tr_news_app_firebase_device_register_error',
                    'Error registering device'
                )
            );
            return false;
        }
        $userId = $device[$this->deviceModel->getUserIdColumn()];

        if (empty($userId)) {
            $updateData = [
                $this->deviceModel->getUserIdColumn() => $user->ID,
            ];
            $updateDevice = $this->deviceRepository->updateDevice(
                $device[$this->deviceModel->getIdColumn()],
                $updateData
            );
            if ($updateDevice) {
                $device = $updateDevice;
            } else {
                $this->addError(
                    new \WP_Error(
                        'tr_news_app_firebase_device_update_error',
                        'Error updating device'
                    )
                );
            }
        }
        $insertUserDeviceAccess = $this->userDeviceAccessRepository->insertDeviceAccess(
            $user,
            $device[$this->deviceModel->getIdColumn()]
        );
        if ($insertUserDeviceAccess) {
            return true;
        }
        return false;
    }

    public function registerDevice(string $registerToken, bool $allowNotifications, ?string $topic = null)
    {
        $saveDevice = $this->deviceRepository->insertDevice($registerToken, (bool)$allowNotifications, $this->user);
        if (!$saveDevice) {
            return new \WP_Error(
                'tr_news_app_device_register_error',
                'Error registering device',
            );
        }
        if (!$topic) {
            $topic = Tru_Fetcher_DB_Model_Topic::DEFAULT_TOPIC;
        }
        $fetchTopic = $this->topicRepository->fetchTopicByName($topic);
        if (!$fetchTopic) {
            return new \WP_Error(
                'tr_news_app_device_register_error',
                'Error finding device topic',
                ['status' => 500]
            );
        }
        $saveTopic = $this->createDeviceTopic(
            $saveDevice[$this->deviceModel->getIdColumn()],
            $fetchTopic[$this->topicModel->getIdColumn()],
        );
        if (!$saveTopic) {
            return new \WP_Error(
                'tr_news_app_device_register_error',
                'Error saving device topic',
                ['status' => 500]
            );
        }
        return $saveDevice;
    }

    public function registerDeviceFromRequest(\WP_REST_Request $request)
    {
        $registerToken = $request->get_param('register_token');
        $allowNotifications = $request->get_param('allow_notifications');
        $requestTopic = $request->get_param('topic');
        return $this->registerDevice($registerToken, $allowNotifications, $requestTopic);
    }

    public function updateDevice(\WP_REST_Request $request)
    {
        $deviceId = $request->get_param('id');
        return $this->deviceRepository->updateDevice($deviceId, $request->get_params());
    }

    public function deleteDevice(\WP_REST_Request $request)
    {
        return $this->deviceRepository->deleteMany($request->get_params());
    }

    public function createDeviceTopic(int $deviceId, int $topicId)
    {
        return $this->deviceRepository->insertDeviceTopic(
            $deviceId,
            $topicId,
        );
    }

    public function createTopic(\WP_REST_Request $request)
    {
        $topicName = $request->get_param('topic_name');
        return $this->topicRepository->insertTopic($topicName);
    }

    public function updateTopic(\WP_REST_Request $request)
    {
        $topicId = $request->get_param('id');
        return $this->topicRepository->updateTopic($topicId, $request->get_params());
    }

    public function deleteTopic(\WP_REST_Request $request)
    {
        return $this->topicRepository->deleteMany($request->get_params());
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
