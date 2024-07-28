<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Page_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Page_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Sidebar_Response;
use TruFetcher\Includes\Listings\Tru_Fetcher_Listings;
use TruFetcher\Includes\Menus\Tru_Fetcher_Menu;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\Sidebars\Tru_Fetcher_Sidebars;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Category;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Trf_Listings_Category;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

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
class Tru_Fetcher_Api_Page_Controller extends Tru_Fetcher_Api_Controller_Base {
	private Tru_Fetcher_Listings $listingsClass;
	private Tru_Fetcher_Sidebars $sidebarClass;
	private Tru_Fetcher_Menu $menuClass;
    private Tru_Fetcher_Posts $postsClass;
	private Tru_Fetcher_Api_Page_Response $apiPostResponse;
	private Tru_Fetcher_Api_Page_List_Response $apiPageListResponse;
	private Tru_Fetcher_Api_Sidebar_Response $apiSidebarResponse;

    public function __construct() {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/pages');
    }

	public function init() {
		$this->loadResponseObjects();
		$this->listingsClass = new Tru_Fetcher_Listings();
		$this->sidebarClass = new Tru_Fetcher_Sidebars();
		$this->menuClass = new Tru_Fetcher_Menu();
        $this->postsClass = new Tru_Fetcher_Posts();
		add_action( 'rest_api_init', [$this, "register_routes"] );
	}

	private function loadResponseObjects() {
		$this->apiPostResponse = new Tru_Fetcher_Api_Page_Response();
		$this->apiPageListResponse = new Tru_Fetcher_Api_Page_List_Response();
		$this->apiSidebarResponse = new Tru_Fetcher_Api_Sidebar_Response();
	}

	public function register_routes() {
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/template/(?<template_post_type>[\w-]+)/(?<taxonomy>[\w-]+)/(?<category_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getPageTemplate"],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/list', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getPages" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/page', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getPageBySlug" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/menu/(?<menu_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getMenuByName" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/sidebar/(?<sidebar_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getSidebar" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->apiConfigEndpoints->protectedEndpoint, '/menu/(?<menu_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getMenuByName" ],
			'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
		) );
		register_rest_route( $this->apiConfigEndpoints->protectedEndpoint, '/sidebar/(?<sidebar_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getSidebar" ],
			'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
		) );
		register_rest_route( $this->apiConfigEndpoints->publicEndpoint, '/site/config', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getSiteConfig" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
	}

	public function getSidebar( $request ) {
        $sidebarName = $request->get_param('sidebar_name');
        if ( empty($sidebarName) ) {
            return $this->controllerHelpers->sendErrorResponse(
                'request_missing_parameters',
                "Sidebar name doesn't exist in request",
                $this->apiSidebarResponse
            );
        }
        $sidebar = $this->sidebarClass->getSidebar($sidebarName);
        $this->apiSidebarResponse->setSidebar($sidebar);
        return $this->controllerHelpers->sendSuccessResponse(
            'Sidebar fetch',
            $this->apiSidebarResponse
        );
	}

	public function getMenuByName(\WP_REST_Request $request) {
		$menuName = (string) $request["menu_name"];
		if ( ! isset( $menuName ) ) {
			return $this->showError( 'request_missing_parameters', "Menu name doesn't exist in request" );
		}
        $blocks = $request->get_param('blocks');
        if (!empty($blocks)) {
            $blocks = array_map(function ($val) {
                return strtolower(trim($val));
            }, explode(',', $blocks));
        } else {
            $blocks = [];
        }
		$menuArray = $this->menuClass->getMenu($menuName, $blocks);

		return rest_ensure_response( $menuArray );
	}

	public function getPageTemplate(\WP_REST_Request $request) {
		$templatePostType = $request->get_param('template_post_type');
		$categoryName = $request->get_param('category_name');
		$taxonomy = $request->get_param('taxonomy');
        if ( empty($templatePostType) ) {
            return $this->showError( 'request_missing_parameters', "Template post type doesn't exist in request" );
        }
		if ( empty( $categoryName ) ) {
			return $this->showError( 'request_missing_parameters', "Category doesn't exist in request" );
		}
		if ( empty( $taxonomy ) ) {
			return $this->showError( 'request_missing_parameters', "Taxonomy doesn't exist in request" );
		}

        $getPageTemplate = $this->postsClass->getTemplate(
            $categoryName,
            $taxonomy,
            $templatePostType
        );
        if (is_wp_error($getPageTemplate)) {
            return $this->showError($getPageTemplate->get_error_code(), $getPageTemplate->get_error_message());
        }

        $pageObject = $this->postsClass::buildPostObject($getPageTemplate);
        $this->apiPostResponse->setPage( $pageObject );
        $this->apiPostResponse->setPageOptions( $this->postsClass::getPostMetaFields($getPageTemplate) );
		// Return the product as a response.
        return $this->controllerHelpers->sendSuccessResponse(
            'Page template fetch',
            $this->apiPostResponse
        );
	}

	public function getPages(\WP_REST_Request $request ) {
        $args = [
            'post_type' => Tru_Fetcher_Post_Types_Page::NAME,
            'numberposts' => -1,
        ];
        $this->apiPageListResponse->setPageList(
            $this->postsClass->buildPostsArray(
                get_posts($args),
                ['ID', 'url', 'post_name']
            )
        );
        return $this->controllerHelpers->sendSuccessResponse(
            'Page fetched successfully',
            $this->apiPageListResponse
        );
	}

	public function getPageBySlug( $request ) {
		$pageName = $request->get_param("page");
		$getPage = $this->postsClass->getPageBySlug($pageName);

		if (is_wp_error($getPage)) {
            return $this->controllerHelpers->sendErrorResponse(
                $getPage->get_error_code(),
                $getPage->get_error_message(),
                $this->apiPostResponse
            );
		}
        $pageObject = $this->postsClass::buildPostObject($getPage);

        $this->apiPostResponse->setPage($pageObject);
        $this->apiPostResponse->setPageOptions( $this->postsClass::getPostMetaFields($pageObject) );
        return $this->controllerHelpers->sendSuccessResponse(
            'Page fetched successfully',
            $this->apiPostResponse
        );
	}

	private function getSiteConfig() {
		return [
			"admin_email"      => get_option( "admin_email" ),
			"blogname"         => get_option( "blogname" ),
			"blogdescription"  => get_option( "blogdescription" ),
			"blog_charset"     => get_option( "blog_charset" ),
			"date_format"      => get_option( "date_format" ),
			"default_category" => get_option( "default_category" ),
			"home"             => get_option( "home" ),
			"siteurl"          => get_option( "siteurl" ),
			"posts_per_page"   => get_option( "posts_per_page" ),
		];
	}
}
