<?php
namespace TruFetcher\Includes\Sidebars;

use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Listings;
use TruFetcher\Includes\Menus\Tru_Fetcher_Menu;

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
class Tru_Fetcher_Sidebars {

    const WIDGETS = [
      "social_media_widget", "button_widget", "email_optin_widget"
    ];

	private $menuClass;
    private Tru_Fetcher_Api_Helpers_Listings $listingsHelpers;

	public function __construct() {
		$this->menuClass = new Tru_Fetcher_Menu();
        $this->listingsHelpers = new Tru_Fetcher_Api_Helpers_Listings();
	}

	public function getAllSidebars() {
        $sidebarArray = array_map(function($sidebar) {
            return $this->buildSidebarArray($sidebar);
        }, wp_get_sidebars_widgets());

        if (array_key_exists("wp_inactive_widgets", $sidebarArray)) {
            unset($sidebarArray["wp_inactive_widgets"]);
        }
        return $sidebarArray;
    }


	public function getSidebar( $sidebarName ) {
		if ( ! isset( $sidebarName ) ) {
			return false;
		}

		$sidebarWidgets = wp_get_sidebars_widgets();
		if ( ! array_key_exists( $sidebarName, $sidebarWidgets ) ) {
			return false;
		}

        return $this->buildSidebarArray($sidebarWidgets[ $sidebarName ]);
	}

	public function buildSidebarArray($sidebarArray) {
//        var_dump($sidebarArray); die;
	    return array_map( function ( $item ) {
            $array              = [];
            $splitItem = explode("-", $item);
            $instanceNumber     = $splitItem[array_key_last($splitItem)];
            unset($splitItem[array_key_last($splitItem)]);
            $widgetInstanceName = implode("-", $splitItem);
            $widget_instances             = get_option( 'widget_' . $widgetInstanceName );
            if (!$widget_instances) {
                return false;
            }
            $widgetData                   = $widget_instances[ $instanceNumber ];
            $menuObject = wp_get_nav_menu_object($widgetData['nav_menu']);
            if ($menuObject) {
                $array[ $widgetInstanceName ]["menu_slug"] = $menuObject->slug;
                $array[ $widgetInstanceName ]["menu_items"] = $this->menuClass->getMenu( $menuObject );
            } else if ($widgetInstanceName === 'tru_fetcher_listings') {
                $listingId = $widgetData['listing'];
                $listing = $this->listingsHelpers->getListingById((int)$listingId);
                if ($listing) {
                    $widgetData['data'] = $listing['config_data'];
                } else {
                    $widgetData['data'] = [];
                }
                $array[ $widgetInstanceName ] = $widgetData;
            } else if ($widgetInstanceName === 'block') {
                $blockItem = $this->buildSidebarBlockItem($widgetData['content']);
                $array[ $blockItem['name'] ] = $blockItem['data'];
            } else {
                $array[ $widgetInstanceName ] = $widgetData;
            }

            return $array;

        }, $sidebarArray );
    }

    private function buildSidebarBlockItem(string $content) {
        $blockData = parse_blocks($content);
        $blockName = $blockData[0]['blockName'];
        return [
            'name' => $blockName,
            'data' => $blockData
        ];
    }
}
