<?php
namespace TruFetcher\Includes\Menus;

use stdClass;
use TruFetcher\Includes\Admin\Blocks\Tru_Fetcher_Admin_Blocks;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Listings\Tru_Fetcher_Listings;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;

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
		$this->listingsClass = new Tru_Fetcher_Listings();
	}

	public function getMenu(string $menu, ?array $blocks = []) {
		$getMenu = wp_get_nav_menu_items($menu);

		if ( ! $getMenu ) {
			return null;
		}

		$menuArray = [];
		foreach ( $getMenu as $item ) {
		    $menuItems = [];
		    $menuItem = null;
			if ( (int) $item->menu_item_parent === 0 ) {
				$menuItem = $this->getPostFromMenuItem($item, $blocks);
			}
			$subItems = [];
			foreach ( $getMenu as $subItem ) {
				if ( (int) $subItem->menu_item_parent == (int) $item->ID ) {
					$subItems[] = $this->getPostFromMenuItem($subItem, $blocks);
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

	public function getPostFromMenuItem(\WP_Post $menuItem, ?array $blocks = []) {
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

        $pageOptions = Tru_Fetcher_Posts::getPostMetaFields($getPost);

        $pageTypeId = Tru_Fetcher_Meta_Fields::getMetaFieldIdByMetaKey(
            Tru_Fetcher_Meta_Fields_Page_Options::META_KEY_PAGE_TYPE
        );
        foreach ($blocks as $block) {
            $blockClass = Tru_Fetcher_Admin_Blocks::findBlockClassById($block);
            if (!$blockClass) {
                continue;
            }
            $blockData = $blockClass->getBlockDataFromPost($getPost);
            if (!$blockData) {
                continue;
            }
            if (empty($blockData['attrs'])) {
                continue;
            }
            $blockAttributes = $blockClass->buildBlockAttributes($blockData['attrs']);
            if (!property_exists($post, 'blocks_data') || !is_array($post->blocks_data)) {
                $post->blocks_data = [];
            }
            $post->blocks_data[] = [
                'id' => $blockClass->getConfig()['id'],
                'attributes' => $blockAttributes,
            ];
        }
        if (isset($pageOptions[$pageTypeId])) {
            $post->post_type = $pageOptions[$pageTypeId];
        } else {
            $post->post_type = null;
        }

		unset($post->post_content);
		return $post;
	}
}
