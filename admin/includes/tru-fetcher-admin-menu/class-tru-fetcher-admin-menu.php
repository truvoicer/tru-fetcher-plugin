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
class Tru_Fetcher_Admin_Menu {

	private $parentMenuTitle = "Tru Fetcher";
	private $parentMenuPageTitle = "Tru Fetcher";
	private $parentMenuSlug = "tru-fetcher-main-menu";
	private $parentMenuCapability = "manage_options";
	private $postTypeSubmenuArray = [];


	public function __construct() {
	}

	public function admin_menu_init() {
		$this->define_post_types();
		$this->define_taxonomies();
		$this->define_admin_menus();
		$this->add_option_pages();
	}

	public function define_post_types() {
		$this->directoryIncludes(
		    'tru-fetcher-admin-menu/post-types',
            'register-post-type.php',
            'post_type'
        );
	}

	public function define_taxonomies() {
		$this->directoryIncludes(
		    'tru-fetcher-admin-menu/taxonomies',
            'register-taxonomy.php',
            'taxonomy'
        );
	}

	private function define_admin_menus() {
		add_action( 'admin_menu', [ $this, 'add_admin_menus' ] );
	}

	public function adminMenuDashboard() {
	    echo "<h1>TRU FETCHER API ADMIN</h1>";
        return "<h1>TRU FETCHER API ADMIN</h1>";
    }

	public function add_admin_menus() {
		add_menu_page(
			$this->parentMenuPageTitle,
			$this->parentMenuTitle,
			$this->parentMenuCapability,
			$this->parentMenuSlug,
			[$this, "adminMenuDashboard"],
			"",
			2
		);
		foreach ($this->postTypeSubmenuArray as $submenu) {
		    $menuSlug = "";
		    if ($submenu["type"] === "post_type") {
                $menuSlug = 'edit.php?post_type=' . $submenu["name"];
            } elseif ($submenu["type"] === "taxonomy") {
                $menuSlug = 'edit-tags.php?taxonomy=' . $submenu["name"];
            }
			add_submenu_page(
				$this->parentMenuSlug,
				$submenu["title"],
				$submenu["title"],
				'manage_options',
                $menuSlug
			);
		}
	}

    public function add_option_pages() {
        if( function_exists('acf_add_options_page') ) {
            acf_add_options_sub_page(array(
                'page_title' 	=> 'TruFetcher Settings',
                'menu_title'	=> 'TruFetcher Settings',
                'parent_slug'	=> $this->parentMenuSlug,
            ));
        }
    }

	private function directoryIncludes( $pathName, $fileName, $type ) {
		$dir = new DirectoryIterator( plugin_dir_path( dirname( __FILE__ ) ) . $pathName );
		foreach ( $dir as $fileinfo ) {
			if ( ! $fileinfo->isDot() ) {
				$submenuArray = [
					"name" => str_replace( "-", "_", $fileinfo->getFilename() ),
					"slug" => $fileinfo->getFilename(),
					"title" => ucwords(str_replace("-", " ", $fileinfo->getFilename())),
                    "type" => $type
				];
				array_push($this->postTypeSubmenuArray, $submenuArray );
				require_once( $fileinfo->getRealPath() . '/' . $fileName );
			}
		}
	}
}
