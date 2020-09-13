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
class Tru_Fetcher_Post_Types {

	private $parentMenuTitle = "Tru Fetcher";
	private $parentMenuPageTitle = "Tru Fetcher";
	private $parentMenuSlug = "tru-fetcher-main-menu";
	private $parentMenuCapability = "manage_options";
	private $postTypeSubmenuArray = [];


	public function __construct() {
	}

	public function post_types_init() {
		$this->define_post_types();
		$this->define_admin_menus();
	}

	public function define_post_types() {
		$this->directoryIncludes( 'includes/post-types', 'register-post-type.php' );
	}

	private function define_admin_menus() {
		add_action( 'admin_menu', [ $this, 'add_admin_menus' ] );
	}

	public function add_admin_menus() {
		add_menu_page(
			$this->parentMenuPageTitle,
			$this->parentMenuTitle,
			$this->parentMenuCapability,
			$this->parentMenuSlug,
			'my_menu_function',
			"",
			2
		);
		foreach ($this->postTypeSubmenuArray as $submenu) {
			add_submenu_page(
				$this->parentMenuSlug,
				$submenu["title"],
				$submenu["title"],
				'manage_options',
				'edit.php?post_type=' . $submenu["name"]
			);
		}
	}

	private function directoryIncludes( $pathName, $fileName ) {
		$dir = new DirectoryIterator( plugin_dir_path( dirname( __FILE__ ) ) . $pathName );
		foreach ( $dir as $fileinfo ) {
			if ( ! $fileinfo->isDot() ) {
				$submenuArray = [
					"name" => str_replace( "-", "_", $fileinfo->getFilename() ),
					"slug" => $fileinfo->getFilename(),
					"title" => ucwords(str_replace("-", " ", $fileinfo->getFilename()))
				];
				array_push($this->postTypeSubmenuArray, $submenuArray );
				require_once( $fileinfo->getRealPath() . '/' . $fileName );
			}
		}
	}
}
