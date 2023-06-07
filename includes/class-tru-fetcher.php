<?php
namespace TruFetcher\Includes;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

use DirectoryIterator;
use TruFetcher\Includes\Admin\Meta\Tru_Fetcher_Admin_Meta;
use TruFetcher\Includes\Admin\Tru_Fetcher_Admin;
use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_Jwt;
use TruFetcher\Includes\Api\Tru_Fetcher_Api;
use TruFetcher\Includes\Blocks\Tru_Fetcher_Blocks;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;
use TruFetcher\Includes\TruFetcherAcf\Tru_Fetcher_Acf;
use TruFetcher\Includes\User\Tru_Fetcher_User;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher extends Tru_Fetcher_Base {

    public const TAXONOMY_ENDPOINT = "edit-tags.php?taxonomy=%s";

    public const REACT_SCRIPT_NAME = "index";
    public const ADMIN_NAME = "tru-fetcher-admin";

    private Tru_Fetcher_Health_Check $healthCheck;
    private Tru_Fetcher_User $userManager;
    private Tru_Fetcher_Taxonomy $taxonomyManager;

    protected string $reactScriptName = self::REACT_SCRIPT_NAME;
    protected string $adminName = self::ADMIN_NAME;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        parent::__construct();
        $this->healthCheck = new Tru_Fetcher_Health_Check();
        $this->userManager = new Tru_Fetcher_User();
        $this->taxonomyManager = new Tru_Fetcher_Taxonomy();
		$this->set_locale();
		$this->loadAdmin();
		$this->define_admin_hooks();
        $this->addUserActions();
        $this->addAjaxActions();
//		$this->load_graphql();
		$this->loadApi();
        $this->loadAcf();
//        $this->loadEmail();
		$this->define_post_types();
		$this->define_blocks();
		$this->define_nav_menus();
		$this->define_sidebars();
		$this->define_widgets();
        $this->healthCheck();

	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tru_Fetcher_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tru_Fetcher_i18n();
		add_action( 'plugins_loaded', [$plugin_i18n, 'load_plugin_textdomain'] );

	}

    private function loadAdmin() {
        $admin = new Tru_Fetcher_Admin();
        $admin->init();
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
        add_action('admin_enqueue_scripts', [$this, "enqueue_styles"]);
        add_action('admin_enqueue_scripts', [$this, "enqueue_scripts"]);
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
    public function enqueue_styles()
    {
        wp_enqueue_style(
            "{$this->plugin_name}-{$this->reactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->reactScriptName}.css",
        );
    }

    public function enqueue_scripts()
    {
//      var_dump( str_replace('-', '_', "{$this->plugin_name}_react"));
        wp_enqueue_script(
            "{$this->plugin_name}-{$this->reactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->reactScriptName}.js",
            array('wp-element'),
            $this->version,
            true
        );
        wp_localize_script(
            "{$this->plugin_name}-{$this->reactScriptName}",
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
            'postType' => $post_type
        ];
    }

    public function getLoadSelector() {
        global $pagenow;
        return get_current_screen();
        switch ($pagenow) {
            case 'post.php':
                return 'post';
            case 'post-new.php':
                return 'post-new';
            case 'edit-tags.php':
                return 'edit-tags';
            case 'term.php':
                return 'term';
            case 'term-new.php':
                return 'term-new';
            case 'edit.php':
                return 'edit';
            default:
                return 'unknown';
        }
    }

	public static function getFrontendUrl() {
//		$options = \get_fields_clone( "option" );
		$frontendUrl = get_option( 'siteurl' );
		if ( isset( $options["frontend_url"] ) ) {
			$frontendUrl = $options["frontend_url"];
		}
		return $frontendUrl;
	}

	public static function getCountriesListArray() {
        return include plugin_dir_path( dirname( __FILE__ ) ) . 'config/country-list.php';
    }

	public static function getCountriesSelectArray() {
	    $selectList = [];
	    foreach (self::getCountriesListArray() as $code => $country) {
	        array_push($selectList, [
	           "value" =>  $code,
                "label" => $country
            ]);
        }
	    return $selectList;
	}

	public static function getTruFetcherSettings() {
		return \get_fields_clone( "option" );
	}

    public static function isNotEmpty(string $string = null) {
        if (isset( $string ) && $string !== "") {
            return true;
        }
        return false;
    }

	private function load_graphql() {
//		$truFetcherGraphql = new Tru_Fetcher_GraphQl();
//		$truFetcherGraphql->init();
	}

	private function loadApi() {
		$truFetcherEndpoints = new Tru_Fetcher_Api();
		$truFetcherEndpoints->init();
	}

    private function loadAcf() {
        $truFetcherAcf = new Tru_Fetcher_Acf();
        $truFetcherAcf->acf_init();
    }

    private function loadEmail() {
//        $truFetcherEmail = new Tru_Fetcher_Email();
//	    $truFetcherEmail->init();
    }

	private function define_post_types() {
        $this->directoryIncludes( 'includes/post-types', 'register-post-type.php' );
	}

	private function define_blocks() {
	    $blocksManager = new Tru_Fetcher_Blocks();
	    $blocksManager->blocks_init();
	}

	private function define_nav_menus() {
		$this->directoryIncludes( 'includes/menus/register', 'register-menu.php' );
	}

	private function define_widgets() {
		$this->directoryIncludes( 'includes/widgets', 'register-widget.php' );
	}

	private function define_sidebars() {
		$this->directoryIncludes( 'includes/sidebars/register', 'register-sidebar.php' );
	}

    public static function directoryIncludes($pathName, $fileName)
    {
        $dir = new DirectoryIterator(TRU_FETCHER_PLUGIN_DIR . $pathName);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot()) {
                continue;
            }
            $fileDir = $fileinfo->getRealPath() . '/' . $fileName;
            if (file_exists($fileDir)) {
                require_once($fileDir);
            }
        }
    }

    public function activate($plugin, $network_wide) {
        $this->healthCheck->setIsNetworkWide($network_wide);
        $run = $this->healthCheck->runHealthCheck();

        if ($run) {
            return true;
        }
        $install = $this->healthCheck->initialInstall();
        if (!$install) {
            error_log(json_encode([TRU_FETCHER_PLUGIN_NAME => 'Health check install failed']));
            return false;
        }
        return true;
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
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

	}
}
