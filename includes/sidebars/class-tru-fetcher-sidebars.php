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
class Tru_Fetcher_Sidebars {

	private $menuClass;

	public function __construct() {
		$this->loadDependencies();
		$this->menuClass = new Tru_Fetcher_Menu();
	}

	private function loadDependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'menus/class-tru-fetcher-menu.php';
	}

	public function getSidebar( $sidebarName ) {
		if ( ! isset( $sidebarName ) ) {
			return false;
		}

		$sidebarWidgets = wp_get_sidebars_widgets();
		if ( ! array_key_exists( $sidebarName, $sidebarWidgets ) ) {
			return false;
		}

		$sidebarArray = array_map( function ( $item ) {
			$array              = [];
			$instanceNumber     = substr( $item, strpos( $item, "-" ) + 1 );
			$widgetInstanceName = str_replace( substr( $item, strpos( $item, "-" ) ), "", $item );

			$widget_instances             = get_option( 'widget_' . $widgetInstanceName );
			$widgetData                   = $widget_instances[ $instanceNumber ];
			$array[ $widgetInstanceName ] = $widgetData;
			if ( $widgetInstanceName === "nav_menu" ) {
				if ( array_key_exists( "nav_menu", $widgetData ) ) {
					$menuObject = wp_get_nav_menu_object($widgetData['nav_menu']);
					$array[ $widgetInstanceName ]["menu_slug"] = $menuObject->slug;
					$array[ $widgetInstanceName ]["menu_items"] = $this->menuClass->getMenu( $menuObject );
				}
			}
			if ( $widgetInstanceName === "social_media_widget" ) {
				$widgetFields                 = get_fields( 'widget_' . $item );
				$array[ $widgetInstanceName ] = $widgetFields;
			}

			return $array;

		}, $sidebarWidgets[ $sidebarName ] );

		return $sidebarArray;
	}
}
