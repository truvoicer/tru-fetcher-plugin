<?php

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
class Tru_Fetcher_Api_Comments_Controller {

    const REQUIRED_FIELDS = ["user_id", "comment_content", "provider", "category", "item_id"];
	private $namespace = "wp/v2/public/comments";
	private $apiUserResponse;

	public function __construct() {
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	private function load_dependencies() {

	}

	private function loadResponseObjects() {

	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/create', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createComment" ],
			'permission_callback' => '__return_true'
		) );
	}

	public function createComment( $request ) {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!$this->isNotEmpty($request[$field])) {
                return $this->showError("required_field_error", sprintf("Field (%s) not found in request", $field));
            }
	    }
	    $data = [
            "user_id" => $request["user_id"],
            "comment_content" => $request["user_id"],
            "provider" => $request["provider"],
            "item_category" => $request["category"],
            "item_id" => $request["item_id"],
        ];

        $getUser = get_userdata($data["user_id"]);
        if (!$getUser) {
            return $this->showError("get_user_error", "User is not valid.");
        }

        $newComment = wp_new_comment( [
            "user_id" => $getUser->ID,
            "comment_content" => $data["comment_content"],
        ], true );

        if (is_wp_error($newComment)) {
            return $this->showError($newComment->get_error_code(), $newComment->get_error_message());
        }

        add_comment_meta($newComment, "provider", $data["provider"], true);
        add_comment_meta($newComment, "item_category", $data["category"], true);
        add_comment_meta($newComment, "item_id", $data["item_id"], true);

        return rest_ensure_response( ["status" => "success"] );
	}

//    private function sendResponse( Tru_Fetcher_Api_User_Response $api_user_response ) {
//        return rest_ensure_response( $api_user_response );
//    }
	private function showError( $code, $message ) {
		return new WP_Error( $code,
			esc_html__( $message, 'my-text-domain' ),
			array( 'status' => 404 ) );
	}

	private function isNotEmpty($item) {
	    return (isset($item) && $item !== null && $item !== "");
    }
}
