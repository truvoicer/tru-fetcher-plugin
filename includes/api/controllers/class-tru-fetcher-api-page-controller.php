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
class Tru_Fetcher_Api_Page_Controller {

	const LISTINGS_FILTERS = [
		"NAME"           => "tru_fetcher_listings",
		"OVERRIDE"       => "show_filters",
		"OVERRIDE_ARRAY" => "filters",
		"FILTERS_LIST"   => "listings_filters",
	];

	private $listingsClass;
	private $sidebarClass;
	private $menuClass;

	private $namespace = "wp/v2/public";
	private $apiPostResponse;
	private $templatePostType = "item_view_templates";
	private $listingsCategoriesTaxonomy = "listings_categories";

	public function __construct() {
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		$this->listingsClass = new Tru_Fetcher_Listings();
		$this->sidebarClass = new Tru_Fetcher_Sidebars();
		$this->menuClass = new Tru_Fetcher_Menu();
		add_action( 'rest_api_init', [$this, "register_routes"] );
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'response/ApiPostResponse.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '../listings/class-tru-fetcher-listings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '../sidebars/class-tru-fetcher-sidebars.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '../menus/class-tru-fetcher-menu.php';
	}

	private function loadResponseObjects() {
		$this->apiPostResponse = new Tru_Fetcher_Api_Post_Response();
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/template/item-view/(?<category_name>[\w-]+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => [ $this, "getTemplate" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/page', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => [ $this, "getPageBySlug" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/menu/(?<menu_name>[\w-]+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => [ $this, "getMenuByName" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/sidebar/(?<sidebar_name>[\w-]+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => [ $this, "getSidebar" ],
			'permission_callback' => '__return_true'
		) );
		register_rest_route( $this->namespace, '/site/config', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => [ $this, "getSiteConfig" ],
			'permission_callback' => '__return_true'
		) );
	}

	public function getSidebar( $request ) {
		$sidebarName = (string) $request["sidebar_name"];
		$getSidebar = $this->sidebarClass->getSidebar($sidebarName);
		if (!$getSidebar) {
			return $this->showError("sidebar_error", "Error fetching sidebar");
		}
		return rest_ensure_response( $getSidebar );
	}

	public function getMenuByName( $request ) {
		$menuName = (string) $request["menu_name"];
		if ( ! isset( $menuName ) ) {
			return $this->showError( 'request_missing_parameters', "Menu name doesn't exist in request" );
		}

		$menuArray = $this->menuClass->getMenu( $menuName );

		return rest_ensure_response( $menuArray );
	}

	public function getTemplate( $request ) {
		$categoryName = (string) $request['category_name'];
		if ( ! isset( $categoryName ) ) {
			return $this->showError( 'request_missing_parameters', "Category doesn't exist in request" );
		}

		$category = get_term_by( "slug", $categoryName, $this->listingsCategoriesTaxonomy );
		if ( ! $category ) {
			return $this->showError( 'request_invalid_parameters', "Category not found." );
		}

		$args            = [
			'post_type'   => $this->templatePostType,
			'numberposts' => 1,
			'tax_query'   => [
				[
					'taxonomy' => $this->listingsCategoriesTaxonomy,
					'field'    => 'term_id',
					'terms'    => $category->term_id,
				]
			]
		];
		$getPageTemplate = get_posts( $args );
		if (count($getPageTemplate) ===  0) {
			return $this->showError( 'page_not_found',
				sprintf("Page template not found for [%s] - [%s].", $this->listingsCategoriesTaxonomy, $category->name) );
		}

		$this->apiPostResponse = $this->buildApiResponse( $getPageTemplate[0] );
		// Return the product as a response.
		return rest_ensure_response( $this->apiPostResponse );
	}

	public function getPageBySlug( $request ) {
		$pageName = (string) $request->get_param("page");
		if ( ! isset( $pageName ) ) {
			return $this->showError( 'request_missing_parameters', "Page name doesn't exist in request" );
		}
		if ( $pageName === "home" ) {
			$pageId  = get_option( "page_on_front" );
			$getPage = get_post( $pageId );
		} else {
			$getPage = get_page_by_path($request->get_param("page"));
		}
		$this->apiPostResponse = $this->buildApiResponse( $getPage );

		// Return the product as a response.
		return rest_ensure_response( $this->apiPostResponse );
	}

	private function buildApiResponse( $page ) {
		//Blocks data must be set first
		$blocksData = $this->listingsClass->buildListingsBlock( parse_blocks($page->post_content), false );
		$pageObject = $this->buildPageObject( $page );
		$this->apiPostResponse->setPost( $pageObject );
		$this->apiPostResponse->setSiteConfig( $this->getSiteConfig() );
		if ( count( $blocksData ) !== 0 ) {
			$this->apiPostResponse->setBlocksData( $blocksData );
		}

		return $this->apiPostResponse;
	}

	private function buildPageObject( $page ) {
		$page->seo_title    = $page->post_title . " - " . get_bloginfo( 'name' );
		$page->post_content = apply_filters( "the_content", $page->post_content );

		return $page;
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


	private function showError( $code, $message ) {
		return new WP_Error( $code,
			esc_html__( $message, 'my-text-domain' ),
			array( 'status' => 404 ) );
	}
}
