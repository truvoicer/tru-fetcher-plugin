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
class Tru_Fetcher_Api_User_Controller {

	const STATUS_SUCCESS = "success";
	const MAX_RATING = 5;

	const AUTH_TYPES = ["google", "facebook", "wordpress"];
	const AUTH_TYPE_META_KEY = "auth_type";
	const AUTH_TYPE_META_VALUE = "wordpress";

	private $namespace = "wp/v2/public/users";
	private $apiUserResponse;

	public function __construct() {
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'response/ApiUserResponse.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '../database/class-tru-fetcher-database.php';
	}

	private function loadResponseObjects() {
		$this->apiUserResponse = new Tru_Fetcher_Api_User_Response();
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/create', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createUser" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/update', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "updateUser" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/item/save', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "saveItem" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/item/rating/save', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "saveItemRating" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/item/list', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "getItemListData" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/item/list-by-user', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "getItemListDataByUser" ],
			'permission_callback' => '__return_true'
		) );
	}

	public function createUser( $request ) {
		$username = $request["username"];
		$email    = $request["email"];
		$password = $request["password"];

		$createUser = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $createUser ) ) {
			return $this->showError( $createUser->get_error_code(), $createUser->get_error_message() );
		}
		wp_new_user_notification( $createUser );
		update_user_meta($createUser, self::AUTH_TYPE_META_KEY, self::AUTH_TYPE_META_VALUE);

		$getUserData = [
			"username" => $username,
			"email"    => $email
		];

		return $this->sendResponse(
			$this->buildResponseObject( self::STATUS_SUCCESS,
				sprintf( "Confirmation email has been sent to (%s). Click on the confirmation link in the email to complete registration.", $email ),
				$getUserData )
		);
	}

	public function updateUser( $request ) {
		$userData                  = [];
		$userData["ID"]            = $request["ID"];
		$userData["user_nicename"] = $request["nicename"];
		$userData["nickname"]      = $request["nickname"];
		$userData["display_name"]  = $request["display_name"];
		$userData["user_email"]    = $request["email"];
		$userData["first_name"]    = $request["first_name"];
		$userData["last_name"]     = $request["last_name"];

		if ($request["auth_type"] === self::AUTH_TYPE_META_VALUE) {
			$authenticateUser = wp_authenticate( $userData["user_email"], $request["current_password"] );
			if ( is_wp_error( $authenticateUser ) ) {
				return $this->showError( $authenticateUser->get_error_code(), $authenticateUser->get_error_message() );
			}

			if ( isset( $request["change_password"] ) && $request["change_password"] ) {
				if ( $request["confirm_password"] === $request["new_password"] ) {
					$userData["user_pass"] = $request["new_password"];
				}
			}
		}
		if (!in_array($request["auth_type"], self::AUTH_TYPES)) {
			return $this->showError( "auth_type_invalid", "Invalid authentication type in request." );
		}

		$updateUser = wp_update_user( $userData );
		if ( is_wp_error( $updateUser ) ) {
			return $this->showError( $updateUser->get_error_code(), $updateUser->get_error_message() );
		}

		return $this->sendResponse(
			$this->buildResponseObject( self::STATUS_SUCCESS,
				sprintf( "User (%s) updated.", $userData["user_nicename"] ),
				$userData )
		);
	}

	private function getUserItemRequestData( $request) {
		$date                  = new DateTime();
		$data                  = [];
		$data["provider_name"] = $request["provider_name"];
		$data["user_id"]       = $request["user_id"];
		$data["category"]      = $request["category"];
		$data["item_id"]       = $request["item_id"];
		$data["date_created"]  = $date->format( "Y-m-d H:i:s" );
		return $data;
	}

	public function saveItem( $request ) {
		$data = $this->getUserItemRequestData($request);

		$dbClass = new Tru_Fetcher_Database();
		$getItem = $dbClass->getUserItemRow(
			Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME,
			$data["provider_name"], $data["category"], $data["item_id"], $data["user_id"]
		);
		if ($getItem === null) {
			$dbClass->insertData( Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME, $data );
		} else {
			$dbClass->delete(Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME, "item_id=%s", [$data["item_id"]]);
		}

		return $this->sendResponse(
			$this->buildResponseObject( self::STATUS_SUCCESS,
				"",
				true )
		);
	}

	public function saveItemRating( $request ) {
		$data = $this->getUserItemRequestData($request);
		$data["rating"]       = $request["rating"];

		$dbClass = new Tru_Fetcher_Database();
		$getItem = $dbClass->getUserItemRow(
			Tru_Fetcher_Database::RATINGS_TABLE_NAME,
			$data["provider_name"], $data["category"], $data["item_id"], $data["user_id"]
		);
		if ($getItem === null) {
			$dbClass->insertData( Tru_Fetcher_Database::RATINGS_TABLE_NAME, $data );
		} else {
			 $dbClass->updateData(
				Tru_Fetcher_Database::RATINGS_TABLE_NAME,
				["rating" => "%d"],
				["item_id" => "%s", "user_id" => "%d"],
				[(int)$data["rating"]],
				['"'.$data["item_id"].'"', $data["user_id"]]
			);
		}

		$getRatings = $this->getRatingsData(
			$request["provider_name"],
			$request["category"],
			[$data["item_id"]],
			$request["user_id"]
		);

		return $this->sendResponse(
			$this->buildResponseObject( self::STATUS_SUCCESS,
				"",
				$getRatings )
		);
	}

	private function getStringCount( $array, $string ) {
		$str = "";
		foreach ( $array as $value ) {
			$str .= sprintf( "'%s',", $string );
		}

		return rtrim( $str, ',' );
	}

	public function getItemListDataByUser( $request ) {
		$data            = [];
		$data["user_id"] = $request["user_id"];

		$dbClass = new Tru_Fetcher_Database();
		$where   = "user_id=%s";
		$getList = $dbClass->getResults(
			Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME,
			$where,
			$data["user_id"]
		);

		return $this->sendResponse(
			$this->buildResponseObject( self::STATUS_SUCCESS,
				"",
				$getList )
		);
	}

	public function getItemListData( $request ) {
		$getSavedItems  = $this->getSavedItemsData(
			$request["provider_name"],
			$request["category"],
			$request["id_list"],
			$request["user_id"]
		);
		$getRatings = $this->getRatingsData(
			$request["provider_name"],
			$request["category"],
			$request["id_list"],
			$request["user_id"]
		);

		return $this->sendResponse(
			$this->buildResponseObject( self::STATUS_SUCCESS,
				"",
				[
					"saved_items" => $getSavedItems,
					"item_ratings"   => $getRatings
				]
			)
		);
	}
	private function getSavedItemsData($providerName, $category, $idList, $user_id) {
		$dbClass      = new Tru_Fetcher_Database();
		$placeholders = "(" . $this->getStringCount( $idList, "%s" ) . ")";
		$where        = "provider_name=%s AND category=%s AND user_id=%s AND item_id IN $placeholders";

		return $dbClass->getResults(
			Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME,
			$where,
			$providerName, $category, $user_id, ...$idList
		);
	}
	private function getRatingsData($providerName, $category, $idList, $user_id) {
		$dbClass      = new Tru_Fetcher_Database();
		$getRatings = [];
		foreach ($idList as $item) {
			$rating = null;
			$getItemRating     = $dbClass->getRow(
				Tru_Fetcher_Database::RATINGS_TABLE_NAME,
				"provider_name=%s AND category=%s AND user_id=%s AND item_id=%s",
				$providerName, $category, $user_id, $item
			);
			if ($getItemRating === null) {
				$getItemRating     = $dbClass->getRow(
					Tru_Fetcher_Database::RATINGS_TABLE_NAME,
					"provider_name=%s AND category=%s AND item_id=%s",
					$providerName, $category, $item
				);
			} else {
				$rating = $getItemRating->rating;
			}
			if ($getItemRating !== null) {
				$overallRating = $this->getItemOverallRating($getItemRating);
				if ($overallRating !== null) {
					$getItemRating->overall_rating = $overallRating["overall_rating"];
					$getItemRating->total_users_rated = $overallRating["total_users_rated"];
				}

				$getItemRating->rating = $rating;
				$getItemRating->user_id = $user_id;
				array_push($getRatings, $getItemRating);
			}
		}
		return $getRatings;
	}

	private function getItemOverallRating($data) {
		$dbClass      = new Tru_Fetcher_Database();
		$where        = "provider_name=%s AND category=%s AND item_id=%s";
		$getTotal     = $dbClass->getTotal(
			Tru_Fetcher_Database::RATINGS_TABLE_NAME,
			"rating",
			$where,
			[$data->item_id, $data->provider_name, $data->category, $data->item_id]
		);
		if ($getTotal === null || !isset($getTotal->rating) || !isset($getTotal->total_users_rated)) {
			return null;
		}
		$maxUserRatingCount = (int)$getTotal->total_users_rated * self::MAX_RATING;
		$calculateRating = ((int)$getTotal->rating * self::MAX_RATING) / $maxUserRatingCount;
		$roundUpToInteger = ceil($calculateRating);
		return [
			"overall_rating" => $roundUpToInteger,
			"total_users_rated" => (int)$getTotal->total_users_rated
		];
	}

	private function buildResponseObject( $status, $message, $data ) {
		$this->apiUserResponse->setStatus( $status );
		$this->apiUserResponse->setMessage( $message );
		$this->apiUserResponse->setData( $data );

		return $this->apiUserResponse;
	}

	private function sendResponse( Tru_Fetcher_Api_User_Response $api_user_response ) {
		return rest_ensure_response( $api_user_response );
	}

	private function showError( $code, $message ) {
		return new WP_Error( $code,
			esc_html__( $message, 'my-text-domain' ),
			array( 'status' => 404 ) );
	}
}
