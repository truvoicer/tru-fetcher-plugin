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
class Tru_Fetcher_Api_Form_Handler extends Tru_Fetcher_Api_Controller_Base {


	private Tru_Fetcher_Api_Forms_Response $apiFormsResponse;

    public function __construct() {
        $this->load_dependencies();
        $this->loadResponseObjects();
    }

	private function load_dependencies() {
        if (!class_exists("Tru_Fetcher_Api_Forms_Response")) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'response/ApiFormsResponse.php';
        }
	}

	private function loadResponseObjects() {
		$this->apiFormsResponse = new Tru_Fetcher_Api_Forms_Response();
	}

	public function saveUserMetaData(WP_REST_Request $request) {
        $data = $request->get_params();

        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError( "user_not_exist", "Sorry, this user does not exist." );
        }
        $this->saveUserProfileMeta(
            $getUser,
            $data
        );
        return $this->sendResponse(
            $this->buildResponseObject(
                self::STATUS_SUCCESS,
                sprintf( "User (%s) updated.", $data["user_nicename"] ),
                $data )
        );
    }

    public function saveUserProfileMeta(WP_User $user, array $profileData = []) {
        foreach ($profileData as $key => $value) {
            update_user_meta(
                $user->ID,
                $key,
                $value
            );
        }
    }

	private function buildResponseObject( $status, $message, $data ) {
		$this->apiFormsResponse->setStatus( $status );
		$this->apiFormsResponse->setMessage( $message );
		$this->apiFormsResponse->setData( $data );

		return $this->apiFormsResponse;
	}

	private function sendResponse( Tru_Fetcher_Api_Forms_Response $apiFormsResponse ) {
		return rest_ensure_response( $apiFormsResponse );
	}
}
