<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_User_Profile_Response;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Skill;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_User_Skill;
use TruFetcher\Includes\Forms\Tru_Fetcher_Api_Form_Handler;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Skill;

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
class Tru_Fetcher_Api_User_Profile_Controller extends Tru_Fetcher_Api_Controller_Base
{

    private Tru_Fetcher_Api_User_Profile_Response $apiUserProfileResponse;
    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;
    private Tru_Fetcher_DB_Repository_Skill $skillsRepository;
    private Tru_Fetcher_DB_Repository_User_Skill $userSkillsRepository;
    private Tru_Fetcher_Api_Helpers_Skill $skillHelpers;

    const REQUEST_FORM_ARRAY_FIELDS = [
        "experiences", "education"
    ];

    const REQUEST_LIST_FIELDS = [
        "skills"
    ];

    const REQUEST_TEXT_FIELDS = [
        "user_email", "display_name", "first_name", "surname", "telephone", "town", "short_description", "personal_statement", "country"
    ];

    const REQUEST_FILE_UPLOAD_FIELDS = [
        "profile_picture", "cv_file"
    ];

    public function __construct()
    {
        parent::__construct();
        $this->apiUserProfileResponse = new Tru_Fetcher_Api_User_Profile_Response();
        $this->skillsRepository = new Tru_Fetcher_DB_Repository_Skill();
        $this->userSkillsRepository = new Tru_Fetcher_DB_Repository_User_Skill();
        $this->skillHelpers = new Tru_Fetcher_Api_Helpers_Skill();
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
        $this->apiConfigEndpoints->endpointsInit('/user/profile');
    }

    public function init()
    {
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->protectedEndpoint, '/update', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "updateUserProfile"],
            'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
        ));
    }

    public function updateUserProfile(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $getUser = $this->apiAuthApp->getUser();
        if ($getUser === null) {
            return $this->controllerHelpers->sendErrorResponse(
                'user_not_found_error',
                "User not found.",
                $this->apiUserProfileResponse
            );
        }

        $this->saveUserProfileTextFields($getUser, $data);
        $this->saveUserProfileArrayFields($getUser, $data);

        if (array_key_exists("skills", $data)) {
            $updateSkillBatch = $this->skillHelpers->updateUserProfileSkillsBatch($getUser, $data["skills"]);
        }

        if (count($request->get_file_params()) > 0) {
            $saveFiles = $this->apiFormHandler->saveUserProfileFileUploads($getUser, $request->get_file_params());
        }

        if ($this->skillHelpers->hasErrors()) {
            $this->apiUserProfileResponse->setErrors(
                array_merge(
                    $this->apiUserProfileResponse->getErrors(),
                    $this->skillHelpers->getErrors()
                )
            );
        }
        if (!count($this->apiFormHandler->getErrors())) {
            $this->apiUserProfileResponse->setErrors(
                array_merge(
                    $this->apiUserProfileResponse->getErrors(),
                    $this->apiFormHandler->getErrors()
                )
            );
        }
        return $this->controllerHelpers->sendSuccessResponse(
            sprintf("User (%s) updated.", $getUser->display_name),
            $this->apiUserProfileResponse
        );
    }

    private function saveUserProfileArrayFields(\WP_User $user, array $data = [])
    {
        return $this->apiFormHandler->saveUserProfileMeta(
            $user,
            array_filter($data, function ($key) {
                return in_array($key, self::REQUEST_FORM_ARRAY_FIELDS);
            }, ARRAY_FILTER_USE_KEY)
        );
    }

    private function saveUserProfileTextFields(\WP_User $user, array $data = [])
    {
        return $this->apiFormHandler->saveUserProfileMeta(
            $user,
            array_filter($data, function ($key) {
                return in_array($key, self::REQUEST_TEXT_FIELDS);
            }, ARRAY_FILTER_USE_KEY)
        );
    }
}
