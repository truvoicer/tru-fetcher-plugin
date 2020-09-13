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
class Tru_Fetcher_Listings {

	const LISTINGS_FILTERS = [
		"NAME"           => "tru_fetcher_listings",
		"OVERRIDE"       => "show_filters",
		"OVERRIDE_ARRAY" => "filters",
		"FILTERS_LIST"   => "listings_filters",
	];

	public function buildListingsBlock( $blocksData, $isObject = true ) {
		$blocksArray = $this->getAcfBlockData( $blocksData, $isObject );
		if ( array_key_exists( self::LISTINGS_FILTERS['NAME'], $blocksArray ) ) {
			$listingsArray = $blocksArray[ self::LISTINGS_FILTERS['NAME'] ];
			if ( array_key_exists( self::LISTINGS_FILTERS['OVERRIDE'], $listingsArray ) &&
			     $listingsArray[ self::LISTINGS_FILTERS['OVERRIDE'] ] ) {
				$blocksArray[ self::LISTINGS_FILTERS['NAME'] ]
				[ self::LISTINGS_FILTERS['OVERRIDE_ARRAY'] ]
				[ self::LISTINGS_FILTERS['FILTERS_LIST'] ] =
					$this->buildListingFilters( $listingsArray[ self::LISTINGS_FILTERS['OVERRIDE_ARRAY'] ]
					[ self::LISTINGS_FILTERS['FILTERS_LIST'] ] );
			}
		}
		return $blocksArray;
	}

	private function buildListingFilters( $listingFiltersArray ) {
		return array_map( function ( $widgetItem ) {
			if ( $widgetItem['type'] == "list" ) {
				$selectedList       = $widgetItem['list'];
				$widgetItem['list'] = false;
				if ( $selectedList ) {
					$widgetItem['list'] = get_field( "list_items", $selectedList->ID );
				}

				return $widgetItem;
			}

			return $widgetItem;
		}, $listingFiltersArray );
	}

	private function getAcfBlockData( $blocksObject, $isObject = true ) {
		$blocksDataArray = [];
		foreach ( $blocksObject as $block ) {
			if ($isObject) {
				if ( ! isset($block->attributes) ) {
					continue;
				}
				$blockName = $block->name;
				$blockAttributeId = $block->attributes->id;
				acf_setup_meta( (array)$block->attributes->data, $block->attributes->id, true );
			} else {
				if ( ! array_key_exists( "data", $block['attrs'] ) ) {
					continue;
				}
				$blockName = $block['blockName'];
				$blockAttributeId = $block['attrs']['id'];
				acf_setup_meta( $block['attrs']['data'], $block['attrs']['id'], true );
			}
			$fields = get_fields();
			if ( $fields ) {
				$blockName                     = str_replace( "acf/", "", $blockName );
				$blockName                     = str_replace( "-", "_", $blockName );
				$blocksDataArray[ $blockName ] = $fields;
			}
			acf_reset_meta( $blockAttributeId );
		}

		return $blocksDataArray;
	}

	public function registerTypes() {

	}

	public function registerSidebarField() {

	}
}
