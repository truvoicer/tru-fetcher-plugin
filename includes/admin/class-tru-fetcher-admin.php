<?php
namespace TruFetcher\Includes\Admin;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
use TruFetcher\Includes\Admin\Blocks\Tru_Fetcher_Admin_Blocks;
use TruFetcher\Includes\Admin\Meta\Tru_Fetcher_Admin_Meta;
use TruFetcher\Includes\Tru_Fetcher_Base;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/admin
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Admin extends Tru_Fetcher_Base {

    /**
     * Initialize the class and set its properties.
     *
     * @throws \Exception
     * @since    1.0.0
     */
	public function __construct() {
        parent::__construct();
	}

    public function init(): void
    {
        $this->loadAdminMenu();
        $this->defineAdminHooks();
        $this->addUserActions();
        $this->healthCheck();
        $this->loadMeta();
        $this->loadBlocks();
        (new Tru_Fetcher_Admin_Asset_loader())->init();
    }

    private function loadMeta() {
        (new Tru_Fetcher_Admin_Meta())->init();
    }
    private function loadBlocks() {
        (new Tru_Fetcher_Admin_Blocks())->init();
    }
    public function healthCheck()
    {
//        $activator = (new Tr_News_App_Activator())->activate();
//        var_dump($activator);
        $this->healthCheck->setIsNetworkWide(is_network_admin());
        $this->healthCheck->setIsMultiSite(is_multisite());
        $dbCheck = $this->healthCheck->runAdminHealthCheck();
//        if (is_wp_error($dbCheck)) {
//            $this->missingDbTables = $dbCheck->get_error_data();
//        }
//        $configCheck = Tr_News_App_Health_Check::firebaseConfigCheck();
    }
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks() {
//        add_action( 'activated_plugin', [$this, 'activate'], 10, 2 );
    }

    public function addUserActions()
    {
        add_action('deleted_user', [$this->userManager, 'deleteUserHandler'], 10, 3);
    }


    public function loadAdminMenu() {

        (new Tru_Fetcher_Admin_Menu())->init();
    }

}
