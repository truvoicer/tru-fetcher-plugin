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
class Tru_Fetcher_Api_Comments_Controller extends Tru_Fetcher_Api_Controller_Base {

    private Tru_Fetcher_Api_Comments_Response $apiCommentsResponse;

    private string $namespace = "/comments";
	private string $publicEndpoint;
	private string $protectedEndpoint;

	public function __construct() {
	    $this->publicEndpoint = $this->publicNamespace . $this->namespace;
	    $this->protectedEndpoint = $this->protectedNamespace . $this->namespace;
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'response/ApiCommentsResponse.php';
	}

	private function loadResponseObjects() {
        $this->apiCommentsResponse = new Tru_Fetcher_Api_Comments_Response();
	}

	public function register_routes() {
		register_rest_route( $this->publicEndpoint, '/list/(?<category>[\w-]+)/(?<provider>[\w-]+)/(?<item_id>[\w-]+)', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, "getCommentsForItem" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->publicEndpoint, '/user/(?<user_id>[\w-]+)/list', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, "getCommentsForUser" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->protectedEndpoint, '/create', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createComment" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->protectedEndpoint, '/update', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "updateComment" ],
			'permission_callback' => '__return_true'
		) );
	}

    public function getCommentsForItem( $request ) {
        $requiredFields = ["provider", "category", "item_id"];
        $validateRequest = $this->validateRequestFields($request, $requiredFields);
        if (is_wp_error($validateRequest)) {
            return $validateRequest;
        }

        $data = $this->getCommentRequestData($request, $requiredFields);
        if (is_wp_error($data)) {
            return $data;
        }
        $args = [
            "meta_query" => [
                [
                    "key" => "provider",
                    "value" => $data["provider"]
                ],
                [
                    "key" => "category",
                    "value" => $data["category"]
                ],
                [
                    "key" => "item_id",
                    "value" => $data["item_id"]
                ],
            ]
        ];
        $getComments = get_comments($args);

        return $this->sendResponse("", $getComments);
    }

    public function getCommentsForUser( $request ) {
        $requiredFields = ["user_id"];
        $validateRequest = $this->validateRequestFields($request, $requiredFields);
        if (is_wp_error($validateRequest)) {
            return $validateRequest;
        }

        $data = $this->getCommentRequestData($request, $requiredFields);
        if (is_wp_error($data)) {
            return $data;
        }

        $args = [
            "user_id" => $data["user_id"]
        ];
        $getComments = get_comments($args);

        return $this->sendResponse("", $getComments);
    }

	public function createComment( $request ) {
        $requiredFields = ["user_id", "comment_content", "provider", "category", "item_id"];
	    $validateRequest = $this->validateRequestFields($request, $requiredFields);
	    if (is_wp_error($validateRequest)) {
            return $validateRequest;
        }
        $data = $this->getCommentRequestData($request, $requiredFields);
        if (is_wp_error($data)) {
            return $data;
        }

        $getUser = get_userdata($data["user_id"]);
        if (!$getUser) {
            return $this->showError("get_user_error", "User is not valid.");
        }
        $commentData = [
            "user_id" => $getUser->ID,
            "comment_content" => $data["comment_content"],
            "comment_author" => $getUser->display_name,
        ];

        if ($this->isNotEmpty($request["comment_parent"])) {
            $commentData["comment_parent"] = $request["comment_parent"];
        }

        $newComment = wp_new_comment( $commentData, true );

        if (is_wp_error($newComment)) {
            return $this->showError($newComment->get_error_code(), $newComment->get_error_message());
        }

        $this->addItemDetailsCommentMeta($newComment, $data["provider"], $data["category"], $data["item_id"]);

        return $this->sendResponse("Successfully created comment", get_comment($newComment));
	}

	public function updateComment($request) {
        $requiredFields = ["user_id", "comment_content", "provider", "category", "item_id", "comment_id"];
        $validateRequest = $this->validateRequestFields($request, $requiredFields);
        if (is_wp_error($validateRequest)) {
            return $validateRequest;
        }

        $data = $this->getCommentRequestData($request, $requiredFields);
        if (is_wp_error($data)) {
            return $data;
        }

        $getUser = get_userdata($data["user_id"]);
        if (!$getUser) {
            return $this->showError("get_user_error", "User is not valid.");
        }

        $updateComment = wp_update_comment( [
            "user_id" => $getUser->ID,
            "comment_ID" => $data["comment_id"],
            "comment_content" => $data["comment_content"],
        ], true );

        if (is_wp_error($updateComment)) {
            return $this->showError($updateComment->get_error_code(), $updateComment->get_error_message());
        }
        return $this->sendResponse("Successfully updated comment", get_comment($newComment));
    }

	private function addItemDetailsCommentMeta($commentId, $provider, $category, $itemId) {
        add_comment_meta($commentId, "provider", $provider, true);
        add_comment_meta($commentId, "category", $category, true);
        add_comment_meta($commentId, "item_id", $itemId, true);
    }

    private function validateRequestFields($request, $requiredFields) {
        foreach ($requiredFields as $field) {
            if (!$this->isNotEmpty($request[$field])) {
                return $this->showError("required_field_error", sprintf("Field (%s) not found in request", $field));
            }
        }
        return true;
    }

    private function getCommentRequestData( $request, $requiredFields ) {
        $data = [];
        foreach ($requiredFields as $field) {
            $data[$field] = $request[$field];
        }
        return $data;
    }

    private function sendResponse( $message, $data ) {
	    $this->apiCommentsResponse->setStatus("success");
	    $this->apiCommentsResponse->setMessage($message);
	    $this->apiCommentsResponse->setData($data);
        return rest_ensure_response( $this->apiCommentsResponse );
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
