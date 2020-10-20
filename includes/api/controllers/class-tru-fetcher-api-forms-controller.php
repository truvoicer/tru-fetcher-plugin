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
		register_rest_route( $this->protectedEndpoint, '/contact-us', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "contactForm" ],
			'permission_callback' => '__return_true'
		) );
	}


	public function contactForm( $request ) {
        $requiredFields = ["name", "email", "message"];
	    $validateRequest = $this->validateRequestFields($request, $requiredFields);
	    if (is_wp_error($validateRequest)) {
            return $validateRequest;
        }

        $this->emailManager->sendEmail(

        );

        return $this->sendResponse("Successfully created comment", get_comment($newComment));
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
