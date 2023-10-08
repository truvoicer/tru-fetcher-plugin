<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Form_Progress_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Forms_Response;
use TruFetcher\Includes\Email\Tru_Fetcher_Email;
use TruFetcher\Includes\Forms\Tru_Fetcher_Api_Form_Handler;
use TruFetcher\Includes\Forms\Tru_Fetcher_Forms_Progress;
use WP_REST_Request;
use WP_REST_Server;

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
class Tru_Fetcher_Api_Forms_Controller extends Tru_Fetcher_Api_Controller_Base {

    private Tru_Fetcher_Api_Forms_Response $apiFormsResponse;
    private Tru_Fetcher_Api_Form_Progress_Response $apiFormProgressResponse;
    private Tru_Fetcher_Email $emailManager;
    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;
    private Tru_Fetcher_Forms_Progress $formsProgress;

	public function __construct() {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/forms');
        $this->emailManager = new Tru_Fetcher_Email();
        $this->apiFormsResponse = new Tru_Fetcher_Api_Forms_Response();
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
        $this->apiFormProgressResponse = new Tru_Fetcher_Api_Form_Progress_Response();
        $this->formsProgress = new Tru_Fetcher_Forms_Progress();
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}


	public function register_routes() {
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/email', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "emailFormEndpoint" ],
			'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/external-providers', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "providerAction" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/redirect', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "providerAction"],
			'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
		) );
        register_rest_route( $this->apiConfigEndpoints->protectedEndpoint, '/user/metadata/save', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this, "userMetaEndpointHandler" ],
            'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
        ) );
        register_rest_route( $this->apiConfigEndpoints->protectedEndpoint, '/user/metadata/request', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this, "userMetaDataRequest" ],
            'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
        ) );
        register_rest_route( $this->apiConfigEndpoints->protectedEndpoint, '/progress/request', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this, "formsProgressRequest" ],
            'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
        ) );
	}

    public function formsProgressRequest($request)
    {
        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }
        $this->formsProgress->setUser($getUser);
        $this->formsProgress->getFormsProgressData($request);
        $this->apiFormProgressResponse->setGroups($this->formsProgress->getGroups());
        $this->apiFormProgressResponse->setOverallProgressPercentage($this->formsProgress->getOverallProgressPercentage());
        return $this->controllerHelpers->sendSuccessResponse(
            "User progress data fetched.",
            $this->apiFormProgressResponse
        );
    }

    public function userMetaDataRequest($request) {
        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }
	    $this->apiFormHandler->setUser($getUser);
        $userMetaData = $this->apiFormHandler->fetchUserMetaData($request);
        $this->apiFormsResponse->setMetaData($userMetaData);
        return $this->controllerHelpers->sendSuccessResponse(
            "User meta data fetched.",
            $this->apiFormsResponse
        );
    }

    public function userMetaEndpointHandler($request) {
        $this->apiFormHandler->saveUserMetaData($request);
        return $this->sendResponse(
            sprintf("User (%s) updated.", $request["user_nicename"]),
            [
                "redirect_url" => isset($request["redirect_url"])? $request["redirect_url"] : false
            ]
        );
    }

	private function replaceDataPlaceholders($dataValue, $replaceArray = []) {
        if (preg_match_all('~\{(.*?)\}~', $dataValue, $output)) {
            foreach ($output[1] as $key => $value) {
                $filterReservedParam = $this->getPlaceholderValue($output[1][$key]);
                if ($filterReservedParam) {
                    $dataValue = str_replace($output[0][$key], $filterReservedParam, $dataValue, $count);
                }
                if (isset($replaceArray[$output[1][$key]])) {
                    $dataValue = str_replace($output[0][$key], $replaceArray[$output[1][$key]], $dataValue);
                }
            }
        }
	    return $dataValue;
    }

    private function getPlaceholderValue($value)
    {
        switch ($value) {
            case "blog_email":
            case "admin_email":
            case "site_email":
                return get_option("admin_email");
            case "blog_name":
                return get_option("blogname");
            case "site_url":
                return get_option("siteurl");
            default:
                return false;
        }
    }

	public function providerAction(WP_REST_Request $request ) {
        $this->apiFormHandler->processEndpointProvidersByRequest($request);
        return $this->sendResponse(
            "The form has been successfully submitted.",
            [
                "redirect_url" => isset($request["redirect_url"])? $request["redirect_url"] : false
            ]
        );
	}

	public function emailFormEndpoint( WP_REST_Request $request ) {
        $requiredFields = ["from", "subject", "recipient"];
	    $validateRequest = $this->validateRequestFields($request, $requiredFields);
	    if (is_wp_error($validateRequest)) {
            return $validateRequest;
        }
	    $dataArray = $request->get_params();
        foreach ($dataArray as $key => $item) {
            if (in_array($key, $requiredFields)) {
                unset($dataArray[$key]);
            }
        }

        $sendEmail = $this->emailManager->sendEmail(
            $this->replaceDataPlaceholders($request["recipient"], $request->get_params()),
            $this->replaceDataPlaceholders($request["subject"], $request->get_params()),
            "form-builder/email-endpoint-template",
            [
                "DATA_ARRAY" => $dataArray
            ]
        );
        $this->apiFormHandler->processEndpointProvidersByRequest($request);
        if (!$sendEmail) {
            return $this->showError(
                "send_email_error",
                "Sorry, There was an error when submitting the form. Please try again."
            );
        }
        return $this->sendResponse(
            "The form has been successfully submitted.",
            [
                "redirect_url" => isset($request["redirect_url"])? $request["redirect_url"] : false
            ]
        );
	}


    private function validateRequestFields($request, $requiredFields) {
        foreach ($requiredFields as $field) {
            if (!$this->isNotEmpty($request[$field])) {
                return $this->showError("required_field_error", sprintf("Field (%s) not found in request", $field));
            }
        }
        return true;
    }

    private function sendResponse( $message, $data ) {
	    $this->apiFormsResponse->setStatus("success");
	    $this->apiFormsResponse->setMessage($message);
	    $this->apiFormsResponse->setData($data);
        return rest_ensure_response( $this->apiFormsResponse );
    }
}
