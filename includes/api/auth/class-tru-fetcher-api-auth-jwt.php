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
class Tru_Fetcher_Api_Auth_Jwt {

	const API_WHITELIST = [
		'/wp-json/wp/v2/public/*'
	];

	const API_HEADERS = [
		"google"   => 'X-GOOGLE-AUTH-KEY',
		"facebook" => 'X-FACEBOOK-AUTH-KEY',
	];

	const AUTH_TYPE_META_KEY = "auth_type";

	private $googleValidateUrl = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=%s";

	public function init() {
		$this->configureHeaders();
		$this->configureCustomAuth();
		$this->configureWhitelist();
		$this->configureValidTokenResponse();
		$this->configureValidCredentialsResponse();
	}

	private function getConfig() {
		$config = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . '/config/config.json' );

		return json_decode( $config );
	}

	private function getHeader($key) {
		$getHeaders = ( new WP_REST_Server() )->get_headers( $_SERVER );
		$headerKey = strtoupper(WP_REST_Request::canonicalize_header_name($key));

		if ( ! array_key_exists( $headerKey, $getHeaders ) ||  $getHeaders[$headerKey] === "" || $getHeaders[$headerKey] === null) {
			return new WP_Error( 'jwt_auth_custom_auth_failed', sprintf('Invalid [%s] header.', $key) );
		}

		return $getHeaders[$headerKey];
	}

	private function configureHeaders() {
		add_filter(
			'jwt_auth_cors_allow_headers',
			function ( $headers ) {
				// Modify the headers here.
				foreach ( self::API_HEADERS as $header ) {
					$headers .= sprintf( ", %s", $header );
				}

				return $headers;
			}
		);
	}

	private function createUser( $authType, $email, $firstName = null, $lastName = null, $picture = null ) {
		$createUser = wp_create_user( $email, wp_generate_password( 16, true ), $email );
		if ( is_wp_error( $createUser ) ) {
			return new WP_Error( 'jwt_auth_custom_auth_failed', __( 'Error creating new user.', 'jwt-auth' ) );
		}
		$userData = [
			"ID"            => $createUser,
			"nickname"      => $firstName,
			"user_nicename" => $firstName,
			"display_name"  => $firstName,
			"first_name"    => $firstName,
			"last_name"     => $lastName,
		];
		wp_update_user( $userData );
		update_user_meta($createUser, self::AUTH_TYPE_META_KEY, $authType);
		$getUser = get_userdata( $createUser );
		if ( is_wp_error( $getUser ) ) {
			return new WP_Error( 'jwt_auth_custom_auth_failed', __( 'Error retrieving new user.', 'jwt-auth' ) );
		}

		return $getUser;
	}

	private function validateGoogleToken( $username, $password ) {
		$token = $password;
		$validateKey = file_get_contents( sprintf( $this->googleValidateUrl, $token ) );
		if ( ! $validateKey ) {
			return new WP_Error( 'jwt_auth_custom_auth_failed', __( 'Validation failed, invalid Google auth key.', 'jwt-auth' ) );
		}

		$validateObject = json_decode( $validateKey );
		$getUser        = get_user_by_email( $validateObject->email );

		if ( ! $getUser ) {
			$getUser = $this->createUser(
				"google",
				$validateObject->email,
				$validateObject->given_name,
				$validateObject->family_name,
				$validateObject->picture
			);
			if ( is_wp_error( $getUser ) ) {
				return $getUser;
			}
		}

		return $getUser;
	}

	private function validateFacebookToken( $username, $password ) {
		$token = $password;
		$config = $this->getConfig();
		try {
			$fb = new \Facebook\Facebook( [
				'app_id'                => $config->facebook_sdk->app_id,
				'app_secret'            => $config->facebook_sdk->app_secret,
				'default_graph_version' => $config->facebook_sdk->graph_version,
			] );
			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $fb->getOAuth2Client();

			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken( $token );
			if ( ! $tokenMetadata->getIsValid() ) {
				return new WP_Error( 'jwt_auth_custom_auth_failed', $tokenMetadata->getErrorMessage() );
			}

			$getFbUser = $fb->get( '/me?fields=id,name,first_name,last_name,email,picture', $token );
			if ( ! array_key_exists( "email", $getFbUser->getDecodedBody() ) ) {
				return new WP_Error( 'jwt_auth_custom_auth_failed', __( 'Error pulling email address from facebook.', 'jwt-auth' ) );
			}
			$getUser = get_user_by_email( $getFbUser->getDecodedBody()["email"] );
			if ( ! $getUser ) {
				$getUser = $this->createUser(
					"facebook",
					$getFbUser->getDecodedBody()["email"],
					( array_key_exists( "first_name", $getFbUser->getDecodedBody() ) ) ? $getFbUser->getDecodedBody()["first_name"] : null,
					( array_key_exists( "first_name", $getFbUser->getDecodedBody() ) ) ? $getFbUser->getDecodedBody()["last_name"] : null,
					( array_key_exists( "picture", $getFbUser->getDecodedBody() ) ) ? $getFbUser->getDecodedBody()["picture"]["data"]["url"] : null
				);
				if ( is_wp_error( $getUser ) ) {
					return $getUser;
				}
			}
		} catch ( \Facebook\Exceptions\FacebookSDKException $e ) {
			return new WP_Error( 'jwt_auth_custom_auth_failed', $e->getMessage() );
		}

		return $getUser;
	}

	private function configureCustomAuth() {
		add_filter( 'jwt_auth_do_custom_auth', function ( $custom_auth_error, $username, $password, $custom_auth ) {
			switch ( $custom_auth ) {
				case "google":
					return $this->validateGoogleToken( $username, $password );
				case "facebook":
					return $this->validateFacebookToken( $username, $password );
				default:
					return new WP_Error(
						'jwt_auth_custom_auth_failed',
						__( 'Invalid custom_auth value.', 'jwt-auth' )
					);
			}
		},
			10,
			4 );
	}

	private function configureWhitelist() {
		add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
			return self::API_WHITELIST;
		} );
	}

	private function configureValidTokenResponse() {
		add_filter(
			'jwt_auth_valid_token_response',
			function ( $response, $user, $token, $payload ) {
				$authType = get_user_meta($user->ID, self::AUTH_TYPE_META_KEY, true);
				// Modify the response here.
				$response = array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_valid_token',
					'message'    => __( 'Token is valid', 'jwt-auth' ),
					'data'       => array(
						'token'         => $token,
						'auth_type'     => $authType,
						'id'            => $user->ID,
						'user_email'    => $user->user_email,
						'user_nicename' => $user->user_nicename,
						'first_name'    => $user->first_name,
						'last_name'     => $user->last_name,
						'display_name'  => $user->display_name,
						'nickname'      => $user->nickname,
					),
				);

				return $response;
			},
			10,
			4
		);
	}

	private function configureValidCredentialsResponse() {
		add_filter(
			'jwt_auth_valid_credential_response',
			function ( $response, $user ) {
				$authType = get_user_meta($user->ID, self::AUTH_TYPE_META_KEY, true);
				$response = array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_valid_credential',
					'message'    => __( 'Credential is valid', 'jwt-auth' ),
					'data'       => array(
						'token'         => $response["data"]["token"],
						'auth_type'     => $authType,
						'id'            => $user->ID,
						'user_email'    => $user->user_email,
						'user_nicename' => $user->user_nicename,
						'first_name'    => $user->first_name,
						'last_name'     => $user->last_name,
						'display_name'  => $user->display_name,
						'nickname'      => $user->nickname,
					),
				);

				return $response;
			},
			10,
			2
		);
	}
}
