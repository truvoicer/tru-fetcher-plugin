<?php

namespace TruFetcher\Includes\Admin;

use Exception;
use TruFetcher\Includes\Admin\Blocks\Tru_Fetcher_Admin_Blocks;
use TruFetcher\Includes\Admin\Meta\Tru_Fetcher_Admin_Meta;
use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_Jwt;
use TruFetcher\Includes\Api\Tru_Fetcher_Api_Request;
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
class Tru_Fetcher_Admin_Asset_loader extends Tru_Fetcher_Base
{

    protected string $gutenbergReactScriptName = 'gutenberg';
    protected string $metaBoxesReactScriptName = 'meta-boxes';
    protected string $adminReactScriptName = 'tru-fetcher-settings';
    protected string $adminAssetName = 'tru-fetcher-admin';

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        add_action('admin_head', [$this, "gb_gutenberg_admin_styles"]);
        $this->registerAdminScripts();
        $this->addAjaxActions();
    }

    public function registerAdminScripts()
    {
        add_action('admin_enqueue_scripts', [$this, "loadAssets"]);
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
        wp_enqueue_script(
            $this->plugin_name,
            TRU_FETCHER_PLUGIN_URL . "build/{$this->adminAssetName}.js",
            array('jquery'),
            $this->version,
            false
        );
        wp_enqueue_style(
            $this->plugin_name,
            TRU_FETCHER_PLUGIN_URL . "build/{$this->adminAssetName}.scss.css",
            array(),
            $this->version,
            'all'
        );


        $this->loadAssetsByCurrentScreenBase();
    }

    private function loadAssetsByCurrentScreenBase()
    {
        $currentScreen = get_current_screen();
        switch ($currentScreen->base) {
            case 'post':
                $this->loadAssetsByCurrentScreeId($currentScreen);
                break;
            case "toplevel_page_tru-fetcher":
                $this->loadAdminReactAssets();
                break;
        }
    }

    private function loadAssetsByCurrentScreeId(\WP_Screen $currentScreen)
    {
        switch ($currentScreen->id) {
            case 'post':
            case 'page':
                $this->loadGutenbergAssets();
                return;
            default:
                $this->loadMetaBoxesAssets($currentScreen);
        }
    }

    public function loadMetaBoxesAssets(\WP_Screen $currentScreen)
    {
        $handle = "{$this->plugin_name}-{$this->metaBoxesReactScriptName}";
        $metaBoxesPostTypes = (new Tru_Fetcher_Admin_Meta())->getMetaboxPostTypes();
        if (in_array($currentScreen->id, $metaBoxesPostTypes)) {
            wp_enqueue_style(
                $handle,
                TRU_FETCHER_PLUGIN_URL . "build/{$this->metaBoxesReactScriptName}.css",
            );
            wp_enqueue_script(
                $handle,
                TRU_FETCHER_PLUGIN_URL . "build/{$this->metaBoxesReactScriptName}.js",
                array('wp-element'),
                $this->version,
                true
            );
            $localizedScriptData = $this->buildDefaultLocalizedScriptData();
            $localizedScriptData['api'] = [];
            $localizedScriptData['api'] = array_merge($localizedScriptData['api'], $this->buildTruFetcherApiLocalizedScriptData());
            $localizedScriptData['api'] = array_merge($localizedScriptData['api'], $this->buildWordpressApiLocalizedScriptData());
            $localizedScriptData = array_merge($localizedScriptData, $this->buildMetaBoxLocalizedScriptData([$currentScreen->id]));
            wp_localize_script(
                $handle,
                str_replace('-', '_', "{$this->plugin_name}_react"),
                $localizedScriptData
            );
        }
    }

    public function loadGutenbergAssets()
    {
        // Automatically load imported dependencies and assets version.
        $asset_file = include TRU_FETCHER_PLUGIN_DIR . "build/{$this->gutenbergReactScriptName}.asset.php";

        wp_enqueue_style(
            "{$this->plugin_name}-{$this->gutenbergReactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->gutenbergReactScriptName}.css",
        );
        wp_enqueue_script(
            "{$this->plugin_name}-{$this->gutenbergReactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->gutenbergReactScriptName}.js",
            $asset_file['dependencies'],
            $asset_file['version'],
        );
        $localizedScriptData = $this->buildDefaultLocalizedScriptData();
        $localizedScriptData['api'] = [];
        $localizedScriptData['api'] = array_merge($localizedScriptData['api'], $this->buildTruFetcherApiLocalizedScriptData());
        $localizedScriptData = array_merge($localizedScriptData, $this->buildMetaFieldsLocalizedScriptData());
        $localizedScriptData = array_merge($localizedScriptData, $this->buildBlocksLocalizedScriptData());
        wp_localize_script(
            "{$this->plugin_name}-{$this->gutenbergReactScriptName}",
            str_replace('-', '_', "{$this->plugin_name}_react"),
            $localizedScriptData
        );
    }

    public function loadAdminReactAssets()
    {
        wp_enqueue_style(
            "{$this->plugin_name}-{$this->adminReactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->adminReactScriptName}.css",
        );
        wp_enqueue_script(
            "{$this->plugin_name}-{$this->adminReactScriptName}",
            TRU_FETCHER_PLUGIN_URL . "build/{$this->adminReactScriptName}.js",
            array('wp-element'),
            $this->version,
            true
        );
        $localizedScriptData = $this->buildDefaultLocalizedScriptData();
        $localizedScriptData['api'] = [];
        $localizedScriptData['api'] = array_merge($localizedScriptData['api'], $this->buildWordpressApiLocalizedScriptData());
        wp_localize_script(
            "{$this->plugin_name}-{$this->adminReactScriptName}",
            str_replace('-', '_', "{$this->plugin_name}_react"),
            $localizedScriptData
        );
    }

    /**
     * @throws Exception
     */
    public function buildMetaBoxLocalizedScriptData(array $postTypes)
    {
        return [
            'meta' => [
                'metaBoxes' => (new Tru_Fetcher_Admin_Meta())->getMetaboxConfig($postTypes)
            ],
        ];
    }
    /**
     * @throws Exception
     */
    public function buildMetaFieldsLocalizedScriptData(): array
    {
        return [
            'meta' => [
                'metaFields' => Tru_Fetcher_Admin_Meta::getMetaFieldConfig()
            ],
        ];
    }
    /**
     * @throws Exception
     */
    public function buildBlocksLocalizedScriptData(): array
    {
        $blocks = new Tru_Fetcher_Admin_Blocks();
        return [
            'post_types' => $blocks->getBlocksPostTypes(),
            'taxonomies' => $blocks->getBlocksTaxonomies(),
            'blocks' => $blocks->getBlocks(),
        ];
    }

    /**
     * @throws Exception
     */
    public function buildWordpressApiLocalizedScriptData()
    {
        $getCurrentUser = self::getCurrentUser();

        $appKey = 'wp_react';
        $authJwt = new Tru_Fetcher_Api_Auth_Jwt();
        $authJwt->setSecret($this->getReactSecretKey());

        $nonceActionName = $authJwt->getJwtKey('nonce', $appKey, $getCurrentUser);

        $nonce = wp_create_nonce(md5($nonceActionName));
        $encodeNonce = $authJwt->jwtEncode('nonce', $appKey, $getCurrentUser, ['nonce' => $nonce]);

        $saveMeta = update_user_meta(
            $getCurrentUser->ID,
            'nonce_jwt',
            $encodeNonce
        );
        if (!$saveMeta) {
            throw new Exception('Error saving nonce user meta');
        }
        return [
            'wp' => [
                'baseUrl' => rest_url('tru-fetcher-api/admin'),
                'app_key' => $appKey,
                'nonce' => $nonce,
                'secret' => $this->getEnv('TRU_FETCHER_REACT_SECRET'),
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function buildTruFetcherApiLocalizedScriptData()
    {
        $fetcherApi = new Tru_Fetcher_Api_Request();
        $categories = $fetcherApi->getApiDataList("categoryList");
        $providers = $fetcherApi->getApiDataList("providerList");
        $services = $fetcherApi->getApiDataList("serviceList");
        if (is_wp_error($categories)) {
            return false;
        }
        $appKey = 'tru_fetcher_react';
        return [
            'tru_fetcher' => [
                'baseUrl' => $this->getEnv('TRU_FETCHER_API_URL'),
                'token' => $this->getEnv('TRU_FETCHER_API_TOKEN'),
                'app_key' => $appKey,
                'categories' => $categories,
                'providers' => $providers,
                'services' => $services,
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function buildDefaultLocalizedScriptData()
    {
        $getCurrentUser = self::getCurrentUser();
        return [
            'app_name' => TRU_FETCHER_PLUGIN_NAME,
            'user' => [
                'id' => $getCurrentUser->ID
            ],
            'currentScreen' => get_current_screen(),
        ];
    }


    public function gb_gutenberg_admin_styles()
    {
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

}
