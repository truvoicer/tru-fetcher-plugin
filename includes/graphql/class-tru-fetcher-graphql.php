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
class Tru_Fetcher_GraphQl {

	private $listingsClass;
	private $sidebarClass;

	public function __construct() {
		$this->loadDependencies();
		$this->listingsClass = new Tru_Fetcher_Listings();
		$this->sidebarClass = new Tru_Fetcher_Sidebars();
	}

	private function loadDependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'listings/class-tru-fetcher-listings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sidebars/class-tru-fetcher-sidebars.php';
	}

	public function init() {
		add_action( 'graphql_register_types', [$this, "registerTypes"] );
		add_action( 'graphql_register_types', [$this, "registerSidebarField"] );

		$this->blocksJsonResolver();
		$this->sidebarResolver();
	}

	private function blocksJsonResolver() {
		add_filter( 'graphql_resolve_field', function( $result, $source, $args, $context, $info, $type_name, $field_key, $field, $field_resolver ) {
			if ( $field_key === 'blocksJSON' ) {
				if (is_array($result)) {
					return json_encode($result);
				}
				$blocksObject = json_decode($result);
				$blocksJson = $this->listingsClass->buildListingsBlock($blocksObject, true);
				return $blocksJson;
			}
			return $result;
		}, 10, 9 );
	}

	private function sidebarResolver() {
		add_filter( 'graphql_resolve_field', function( $result, $source, $args, WPGraphQL\AppContext $context,
			GraphQL\Type\Definition\ResolveInfo $info, $type_name, $field_key, GraphQL\Type\Definition\FieldDefinition $field,
			$field_resolver ) {
			if ( $field_key === 'sidebar' ) {
				if (!array_key_exists("slug", $args)) {
					return [
						"sidebar_error" => "Error fetching sidebar."
					];
				}
				$getSidebar = $this->sidebarClass->getSidebar($args["slug"]);
				if (!$getSidebar) {
					return [
						"sidebar_error" => "Error fetching sidebar."
					];
				}
				return [
					"sidebar_name" => $args["slug"],
					"widgets_json" => json_encode($getSidebar),
				];
			}
			return $result;
		}, 10, 9 );
	}

	public function registerTypes() {
		register_graphql_object_type( 'Sidebar', [
			'description' => __( "Site sidebar", 'your-textdomain' ),
			'fields' => [
				'sidebar_name' => [
					'type' => "String",
					'description' => __( 'Sidebar name', 'your-textdomain' ),
				],
				'widgets_json' => [
					'type'        => "String",
					'description' => __( 'Sidebar widgets', 'your-textdomain' ),
				],
				'sidebar_error' => [
					'type' => "String",
					'description' => __( 'Sidebar error', 'your-textdomain' ),
				],
			],
		] );
	}

	public function registerSidebarField() {
		register_graphql_field(
			'RootQuery',
			'Sidebar',
			[
				'type'        => 'Sidebar',
				'description' => 'a sidebar',
				'args'        => [
					'slug' => [
						'type' => [
							'non_null' => 'String',
						],
					],
				]
			]
		);
	}
}
