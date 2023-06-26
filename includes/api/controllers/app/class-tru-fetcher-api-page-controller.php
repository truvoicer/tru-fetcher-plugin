<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Page_Response;
use TruFetcher\Includes\Listings\Tru_Fetcher_Listings;
use TruFetcher\Includes\Menus\Tru_Fetcher_Menu;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;
use TruFetcher\Includes\Sidebars\Tru_Fetcher_Sidebars;

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

    protected ?string $namespace = "/pages";

	private $listingsClass;
	private $sidebarClass;
	private $menuClass;
    private Tru_Fetcher_Posts $postsClass;

	private Tru_Fetcher_Api_Page_Response $apiPostResponse;
	private $templatePostType = "item_view_templates";
	private $listingsCategoriesTaxonomy = "listings_categories";

    public function __construct() {
        parent::__construct();
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
	}

	public function register_routes() {
		register_rest_route( $this->publicEndpoint, '/template/item-view/(?<category_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getItemViewTemplate" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->publicEndpoint, '/page', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getPageBySlug" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->publicEndpoint, '/menu/(?<menu_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getMenuByName" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->publicEndpoint, '/sidebar/(?<sidebar_name>[\w-]+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getSidebar" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
		register_rest_route( $this->publicEndpoint, '/site/config', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getSiteConfig" ],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
	}

	public function getSidebar( $request ) {
		return rest_ensure_response(
		    $this->sidebarClass->getSidebar(
		        (string) $request["sidebar_name"]
            )
        );
	}

	public function getMenuByName( $request ) {
		$menuName = (string) $request["menu_name"];
		if ( ! isset( $menuName ) ) {
			return $this->showError( 'request_missing_parameters', "Menu name doesn't exist in request" );
		}

		$menuArray = $this->menuClass->getMenu( $menuName );

		return rest_ensure_response( $menuArray );
	}

	public function getItemViewTemplate( $request ) {
		$categoryName = (string) $request['category_name'];
		if ( ! isset( $categoryName ) ) {
			return $this->showError( 'request_missing_parameters', "Category doesn't exist in request" );
		}

        $getPageTemplate = $this->postsClass->getTemplate($categoryName, "listings_categories", "item_view_templates");
        if (is_wp_error($getPageTemplate)) {
            return $this->showError($getPageTemplate->get_error_code(), $getPageTemplate->get_error_message());
        }
		$this->apiPostResponse = $this->buildApiResponse( $getPageTemplate );
		// Return the product as a response.
		return rest_ensure_response( $this->apiPostResponse );
	}

	public function getPageBySlug( $request ) {
		$pageName = $request->get_param("page");
		$getPage = $this->postsClass->getPageBySlug($pageName);

		if (is_wp_error($getPage)) {
            return $this->showError($getPage->get_error_code(), $getPage->get_error_message());
		}
        $pageData = [];
        $pageData['isFrontPage'] = Tru_Fetcher_Posts::isHomePage($getPage->ID);
        $pageData['status'] = $getPage->post_status;
        $pageData['id'] = $getPage->ID;
        $pageData['authorId'] = $getPage->post_author;
        $pageData['title'] = $getPage->post_title;
        $pageData['slug'] = $getPage->post_name;
        $pageData['uri'] = $getPage->guid;
        $pageData['content'] = apply_filters( 'the_content', $getPage->post_content);
        $pageData['date'] = $getPage->post_date;
        $pageData['dateGmt'] = $getPage->post_date_gmt;
        $pageData['modified'] = $getPage->post_modified;
        $pageData['modifiedGmt'] = $getPage->post_modified_gmt;
        $pageData['menuOrder'] = $getPage->menu_order;
        $pageData['page_options'] = [];
        $pageData['page_options']['fieldGroupName'] = null;
        $pageData['page_options']['footerScripts'] = null;
        $pageData['page_options']['footerScriptsOverride'] = null;
        $pageData['page_options']['headerScripts'] = null;
        $pageData['page_options']['headerScriptsOverride'] = null;
        $pageData['page_options']['pageType'] = null;

        $allSettings = [];
        $allSettings['generalSettingsTitle'] = get_bloginfo('name');
        $allSettings['generalSettingsUrl'] = get_bloginfo('url');
        $allSettings['home_url'] = get_option('home');
        $allSettings['site_url'] = get_option('siteurl');
        $allSettings['readingSettingsPostsPerPage'] = get_option('posts_per_page');
        $allSettings['generalSettingsDescription'] = get_bloginfo('description');
        $allSettings['generalSettingsDateFormat'] = get_option('date_format');
        $allSettings['generalSettingsLanguage'] = get_bloginfo('language');
        $allSettings['generalSettingsStartOfWeek'] = get_option('start_of_week');
        $allSettings['generalSettingsTimeFormat'] = get_option('time_format');
        $allSettings['generalSettingsTimezone'] = get_option('timezone_string');
        $allSettings['admin_email'] = get_option('admin_email');
        $allSettings['default_category'] = get_option('default_category');

        $this->apiPostResponse->setPage($pageData);
        $this->apiPostResponse->setAllSettings($allSettings);

        return $this->controllerHelpers->sendSuccessResponse('Page fetched successfully', $this->apiPostResponse);
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
}
