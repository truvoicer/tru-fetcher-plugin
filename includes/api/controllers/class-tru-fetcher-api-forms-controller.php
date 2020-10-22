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
	}

	private function loadResponseObjects() {
        $this->apiFormsResponse = new Tru_Fetcher_Api_Forms_Response();
	}

	public function register_routes() {
		register_rest_route( $this->publicEndpoint, '/email', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "emailFormEndpoint" ],
			'permission_callback' => '__return_true'
		) );
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

        $sendEmail = $this->emailManager->sendEmail(
            $this->replaceDataPlaceholders($request["recipient"], $request->get_params()),
            $this->replaceDataPlaceholders($request["subject"], $request->get_params()),
            "form-builder/email-endpoint-template",
            [

            ]
        );
        if (!$sendEmail) {
            return $this->showError(
                "send_email_error",
                "There was an error sending the password reset to your email. Please try again."
            );
        }
        return $this->sendResponse(
            "An email has been sent to your inbox (%s). Please follow the instructions.",
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

	private function showError( $code, $message ) {
		return new WP_Error( $code,
			esc_html__( $message, 'my-text-domain' ),
			array( 'status' => 404 ) );
	}

	private function isNotEmpty($item) {
	    return (isset($item) && $item !== null && $item !== "");
    }
}
