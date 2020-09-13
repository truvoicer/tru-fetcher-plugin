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
class Tru_Fetcher_Menu {

	private $listingsClass;

	public function __construct() {
		$this->loadDependencies();
		$this->listingsClass = new Tru_Fetcher_Listings();
	}

	private function loadDependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'listings/class-tru-fetcher-listings.php';
	}

	public function getMenu( $menu ) {
		$getMenu = wp_get_nav_menu_items( $menu );

		if ( ! $getMenu ) {
			return null;
		}

		$menuArray = [];
		foreach ( $getMenu as $item ) {
		    $menuItems = [];
		    $menuItem = null;
			if ( (int) $item->menu_item_parent === 0 ) {
				$menuItem = $this->getPostFromMenuItem( $item );
			}
			$subItems = [];
			foreach ( $getMenu as $subItem ) {
				if ( (int) $subItem->menu_item_parent == (int) $item->ID ) {
					array_push($subItems, $this->getPostFromMenuItem( $subItem ));
				}
			}

            if ($menuItem !== null) {
                $menuItems["menu_item"] = $menuItem;
            }

            if (count($subItems) > 0) {
                $menuItems["menu_sub_items"] = $subItems;
            }
            if (count($menuItems) > 0) {
                array_push($menuArray, $menuItems);
            }
		}

		return $menuArray;
	}

	public function getPostFromMenuItem( $menuItem ) {
		$getPost = get_post( (int) get_post_meta( (int) $menuItem->ID, "_menu_item_object_id" )[0] );
		$pageUrl = rtrim(str_replace(get_site_url(), "", get_page_link($getPost)), "/");
		if ($getPost->ID === (int) get_option( 'page_on_front' )) {
			$pageUrl = str_replace(get_site_url(), "", get_page_link($getPost));
		}
		$post = new stdClass();
		$post->isfront = (int) get_option( 'page_on_front' );
		$post->menu_title = $menuItem->title;
		$post->post_title = $getPost->post_title;
		$post->post_name = $getPost->post_name;
		$post->post_content = $getPost->post_content;
		$post->post_url = $pageUrl;
		$post->post_type = get_field("page_type", $getPost->ID);
		$getBlocksData = $this->listingsClass->buildListingsBlock( parse_blocks($getPost->post_content), false );
		if (isset($getBlocksData["tru_fetcher_user_area"])) {
			$post->blocks_data = new stdClass();
			$post->blocks_data->tru_fetcher_user_area = $getBlocksData["tru_fetcher_user_area"];
		}
		unset($post->post_content);
		return $post;
	}
}
