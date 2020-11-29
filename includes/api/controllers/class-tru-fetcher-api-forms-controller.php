<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'controllers/class-tru-fetcher-api-controller-base.php';

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
    private Tru_Fetcher_Email $emailManager;
    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;

    private string $namespace = "/forms";
	private string $publicEndpoint;
	private string $protectedEndpoint;

	public function __construct() {
	    $this->publicEndpoint = $this->publicNamespace . $this->namespace;
	    $this->protectedEndpoint = $this->protectedNamespace . $this->namespace;
        $this->emailManager = new Tru_Fetcher_Email();
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'response/ApiFormsResponse.php';

        if (!class_exists("Tru_Fetcher_Api_Form_Handler")) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'forms/class-tru-fetcher-api-form-handler.php';
        }
	}

	private function loadResponseObjects() {
        $this->apiFormsResponse = new Tru_Fetcher_Api_Forms_Response();
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
	}

	public function register_routes() {
		register_rest_route( $this->publicEndpoint, '/email', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "emailFormEndpoint" ],
			'permission_callback' => '__return_true'
		) );
        register_rest_route( $this->protectedEndpoint, '/user/metadata/save', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this, "userMetaEndpointHandler" ],
            'permission_callback' => '__return_true'
        ) );
        register_rest_route( $this->protectedEndpoint, '/user/metadata/request', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this, "userMetaDataRequest" ],
            'permission_callback' => '__return_true'
        ) );
        register_rest_route( $this->protectedEndpoint, '/progress/request', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this, "formsProgressRequest" ],
            'permission_callback' => '__return_true'
        ) );
	}

    public function formsProgressRequest($request)
    {
        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }
        $this->apiFormHandler->setUser($getUser);
        return $this->apiFormHandler->getFormsProgressData($request);
    }

    public function userMetaDataRequest($request) {
        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }
	    $this->apiFormHandler->setUser($getUser);
        return $this->apiFormHandler->fetchUserMetaData($request);
    }

    public function userMetaEndpointHandler($request) {
        return $this->apiFormHandler->saveUserMetaData($request);
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
        if (!$sendEmail) {
            return $this->showError(
                "send_email_error",
                "Sorry, There was an error when submitting the form. Please try again."
            );
        }
        return $this->sendResponse(
            "The form has been successfully submitted.",
                []
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
