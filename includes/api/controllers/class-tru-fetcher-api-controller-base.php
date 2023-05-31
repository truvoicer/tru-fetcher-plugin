<?php
namespace TruFetcher\Includes\Api\Controllers;

use WP_Error;
use WP_User;

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
class Tru_Fetcher_Api_Controller_Base {

    const STATUS_SUCCESS = "success";

	protected string $publicNamespace = "wp/tru-fetcher-api/public";
	protected string $protectedNamespace = "wp/tru-fetcher-api/protected";

    private WP_User $user;

    protected function showError( $code, $message ) {
        return new WP_Error( $code,
            esc_html__( $message, 'my-text-domain' ),
            array( 'status' => 404 ) );
    }


    protected function isNotEmpty($item) {
        return (isset($item) && $item !== "");
    }

    /**
     * @return WP_User
     */
    public function getUser(): WP_User
    {
        return $this->user;
    }

    /**
     * @param WP_User $user
     */
    public function setUser(WP_User $user): void
    {
        $this->user = $user;
    }
}
