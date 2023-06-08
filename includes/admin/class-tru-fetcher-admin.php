<?php
namespace TruFetcher\Includes\Admin;
use TrNewsApp\Includes\Admin\AdminMenu\Tr_News_App_Admin_Menu;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
use TruFetcher\Includes\Admin\Meta\Tru_Fetcher_Admin_Meta;
use TruFetcher\Includes\Admin\OldAdminMenu\Tru_Fetcher_Old_Admin_Menu;
use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_Jwt;
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

    protected string $gutenbergReactScriptName = 'gutenberg';
    protected string $metaBoxesReactScriptName = 'meta-boxes';
    protected string $adminReactScriptName = 'tru-fetcher-settings';
    protected string $adminName = 'tru-fetcher-admin';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
        parent::__construct();
        $this->define_admin_hooks();
        $this->addUserActions();
        $this->addAjaxActions();
        $this->healthCheck();
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
    private function define_admin_hooks() {
        $this->registerAdminScripts();
        add_action( 'activated_plugin', [$this, 'activate'], 10, 2 );
    }
    public function registerAdminScripts()
    {
        add_action('admin_enqueue_scripts', [$this, "loadAssets"]);
    }

    public function addUserActions()
    {
        add_action('deleted_user', [$this->userManager, 'deleteUserHandler'], 10, 3);
    }
    public function addAjaxActions()
    {
        add_action('wp_ajax_tru_fetcher_database_install_action', [$this->healthCheck, 'databaseInstallAction']);
        add_action('wp_ajax_tru_fetcher_database_network_install_action', [$this->healthCheck, 'databaseNetworkInstallAction']);
        add_action('wp_ajax_tru_fetcher_db_update_columns', [$this->healthCheck, 'databaseMissingColumnsUpdateAction']);
        add_action('wp_ajax_tru_fetcher_db_network_update_columns', [$this->healthCheck, 'databaseNetworkMissingColumnsUpdateAction']);
        add_action('wp_ajax_tru_fetcher_db_req_data_install', [$this->healthCheck, 'databaseRequiredDataInstallAction']);
        add_action('wp_ajax_tru_fetcher_db_network_req_data_install', [$this->healthCheck, 'databaseNetworkRequiredDataInstallAction']);
    }
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function loadAssets()
    {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tru-fetcher-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tru-fetcher-admin.css', array(), $this->version, 'all' );


        $this->loadAssetsByCurrentScreenBase();
    }

    private function loadAssetsByCurrentScreenBase() {
        $currentScreen = get_current_screen();
        switch ($currentScreen['base']) {
            case 'post':
                $this->loadAssetsByCurrentScreeId($currentScreen['id']);
                break;
            case "toplevel_page_tru-fetcher":
                // Render the App component into the DOM
                render(<App  />, document.getElementById('tru_fetcher_admin'));
                break;
        }
    }

    private function loadAssetsByCurrentScreeId(array $currentScreen) {
        switch ($currentScreen['id']) {
            case 'post':
            case 'page':
                wp_enqueue_style(
                    "{$this->plugin_name}-{$this->gutenbergReactScriptName}",
                    TRU_FETCHER_PLUGIN_URL . "build/{$this->gutenbergReactScriptName}.css",
                );
                return;
        }

        $refClass = new \ReflectionClass(Tru_Fetcher_Admin_Post_Types::class);
        if (in_array($currentScreen['id'], $refClass->getConstants())) {
            wp_enqueue_style(
                "{$this->plugin_name}-{$this->metaBoxesReactScriptName}",
                TRU_FETCHER_PLUGIN_URL . "build/{$this->metaBoxesReactScriptName}.css",
            );
        }
    }
    public function enqueue_scripts()
    {



        wp_enqueue_script(
            "{$this->plugin_name}-{$this->gutenbergReactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->gutenbergReactScriptName}.js",
            array('wp-element'),
            $this->version,
            true
        );
        wp_localize_script(
            "{$this->plugin_name}-{$this->gutenbergReactScriptName}",
            str_replace('-', '_', "{$this->plugin_name}_react"),
            $this->buildReactLocalizedScriptData()
        );
        wp_enqueue_script(
            "{$this->plugin_name}-{$this->adminName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->adminName}.js",
            [],
            $this->version,
            true
        );
    }

    /**
     * @throws Exception
     */
    public function buildReactLocalizedScriptData()
    {
        global $post_type;
        $getCurrentUser = self::getCurrentUser();

        $authJwt = new Tru_Fetcher_Api_Auth_Jwt();
        $authJwt->setSecret($this->getReactSecretKey());

        $nonceActionName = $authJwt->getJwtKey('nonce', 'react', $getCurrentUser);

        $nonce = wp_create_nonce(md5($nonceActionName));
        $encodeNonce = $authJwt->jwtEncode('nonce', 'react', $getCurrentUser, ['nonce' => $nonce]);

        $saveMeta = update_user_meta(
            $getCurrentUser->ID,
            'nonce_jwt',
            $encodeNonce
        );
        if (!$saveMeta) {
            throw new \Exception('Error saving nonce user meta');
        }
        return [
            'api' => [
                'wp' => [
                    'baseUrl' => rest_url('tru-fetcher/admin'),
                    'app_name' => TRU_FETCHER_PLUGIN_NAME,
                    'nonce' => $nonce,
                ],
                'tru_fetcher' => [
                    'baseUrl' => $this->getEnv('TRU_FETCHER_API_URL'),
                    'token' => $this->getEnv('TRU_FETCHER_API_TOKEN'),
                    'app_name' => TRU_FETCHER_PLUGIN_NAME,
                ],
            ],
            'user' => [
                'id' => $getCurrentUser->ID
            ],
            'editor' => [
                'metaFields' => Tru_Fetcher_Admin_Meta::getMetaFieldConfig()
            ],
            'currentScreen' => get_current_screen(),
            'postTypes' => $this->getPostTypeData([
                Tru_Fetcher_Admin_Post_Types::FETCHER_SINGLE_ITEM_PT
            ]),
        ];
    }

    public function getPostTypeData(array $postTypes = []) {

        return array_map(function ($postType) {
            return [
                'post_type' => $postType,
                'posts' => get_posts([
                    'post_type' => $postType,
                    'posts_per_page' => -1,
                    'post_status' => 'any',
                ])
            ];
        }, $postTypes);
    }

    public function loadAdminMenu() {

        (new Tru_Fetcher_Admin_Menu())->init();
    }

    public function gb_gutenberg_admin_styles() {
        echo '
        <style>
            /* Main column width */
            .wp-block {
                max-width: 1080px;
            }
 
            /* Width of "wide" blocks */
            .wp-block[data-align="wide"] {
                max-width: 1080px;
            }
 
            /* Width of "full-wide" blocks */
            .wp-block[data-align="full"] {
                max-width: none;
            }	
        </style>
    ';
    }

    private function loadEditor() {
        (new Tru_Fetcher_Admin_Meta())->init();
    }
    public function init() {
        $this->loadAdminMenu();
        $this->loadEditor();
        add_action('admin_head', [$this, "gb_gutenberg_admin_styles"]);
    }
}
