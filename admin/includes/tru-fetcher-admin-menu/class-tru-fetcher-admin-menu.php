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

    private $adminMenus = [
          [
              "parentMenuTitle" => "Tru Fetcher Search Listings",
              "parentMenuPageTitle" => "Tru Fetcher Search Listings",
              "parentMenuSlug" => "tru-fetcher-search-listings-menu",
              "parentMenuCapability" => "manage_options",
              "parentMenuCallback" => "adminMenuDashboard",
              "parentMenuIcon" => "dashicons-menu",
              "postTypeSubmenuArray" => [],
          ],
          [
              "parentMenuTitle" => "Tru Fetcher Comparisons",
              "parentMenuPageTitle" => "Tru Fetcher Comparisons",
              "parentMenuSlug" => "tru-fetcher-comparisons-menu",
              "parentMenuCapability" => "manage_options",
              "parentMenuCallback" => "adminMenuDashboard",
              "parentMenuIcon" => "dashicons-menu",
              "postTypeSubmenuArray" => [],
          ],
          [
              "parentMenuTitle" => "Tru Fetcher Templates",
              "parentMenuPageTitle" => "Tru Fetcher Templates",
              "parentMenuSlug" => "tru-fetcher-templates-menu",
              "parentMenuCapability" => "manage_options",
              "parentMenuCallback" => "adminMenuDashboard",
              "parentMenuIcon" => "dashicons-menu",
              "postTypeSubmenuArray" => [],
          ],
          [
              "parentMenuTitle" => "Tru Fetcher Settings",
              "parentMenuPageTitle" => "Tru Fetcher Settings",
              "parentMenuSlug" => "tru-fetcher-settings-menu",
              "parentMenuCapability" => "manage_options",
              "parentMenuCallback" => "adminMenuDashboard",
              "parentMenuIcon" => "dashicons-menu",
              "postTypeSubmenuArray" => [],
          ],
    ];

	public function admin_menu_init() {
		$this->define_post_types();
		$this->define_taxonomies();
		$this->define_admin_menus();
		$this->add_option_pages();
	}

	public function define_post_types() {
        foreach ($this->adminMenus as $key => $adminMenu) {
            $this->directoryIncludes(
                "tru-fetcher-admin-menu/menus/{$adminMenu["parentMenuSlug"]}/post-types",
                'register-post-type.php',
                'post_type',
                $key
            );
        }
	}

	public function define_taxonomies() {
        foreach ($this->adminMenus as $key => $adminMenu) {
            $this->directoryIncludes(
                "tru-fetcher-admin-menu/menus/{$adminMenu["parentMenuSlug"]}/taxonomies",
                'register-taxonomy.php',
                'taxonomy',
                $key
            );
        }
	}

	private function define_admin_menus() {
		add_action( 'admin_menu', [ $this, 'add_admin_menus' ] );
	}

	public function adminMenuDashboard() {
	    echo "<h1>TRU FETCHER API ADMIN</h1>";
        return "<h1>TRU FETCHER API ADMIN</h1>";
    }

	public function add_admin_menus() {
	    foreach ($this->adminMenus as $key => $adminMenu) {
            add_menu_page(
                $adminMenu["parentMenuPageTitle"],
                $adminMenu["parentMenuTitle"],
                $adminMenu["parentMenuCapability"],
                $adminMenu["parentMenuSlug"],
                [$this, $adminMenu["parentMenuCallback"]],
                $adminMenu["parentMenuIcon"],
                $key + 100
            );
            foreach ($adminMenu["postTypeSubmenuArray"] as $submenu) {
                $menuSlug = "";
                if ($submenu["type"] === "post_type") {
                    $menuSlug = 'edit.php?post_type=' . $submenu["name"];
                } elseif ($submenu["type"] === "taxonomy") {
                    $menuSlug = 'edit-tags.php?taxonomy=' . $submenu["name"];
                }
                add_submenu_page(
                    $adminMenu["parentMenuSlug"],
                    $submenu["title"],
                    $submenu["title"],
                    'manage_options',
                    $menuSlug
                );
            }
        }
	}

    public function add_option_pages() {
        if( function_exists('acf_add_options_page') ) {
            acf_add_options_sub_page(array(
                'page_title' 	=> 'TruFetcher Settings',
                'menu_title'	=> 'TruFetcher Settings',
                'parent_slug'	=> "tru-fetcher-settings-menu",
            ));
        }
    }

	private function directoryIncludes( $pathName, $fileName, $type, $parentMenuIndex ) {
	    if (!is_dir(plugin_dir_path( dirname( __FILE__ ) ) . $pathName)) {
	        return false;
        }
		$dir = new DirectoryIterator( plugin_dir_path( dirname( __FILE__ ) ) . $pathName );
		foreach ( $dir as $fileinfo ) {
			if ( ! $fileinfo->isDot() ) {
			    if (file_exists($fileinfo->getRealPath() . '/' . $fileName)) {
                    $submenuArray = [
                        "name" => str_replace("-", "_", $fileinfo->getFilename()),
                        "slug" => $fileinfo->getFilename(),
                        "title" => ucwords(str_replace("-", " ", $fileinfo->getFilename())),
                        "type" => $type
                    ];
                    array_push($this->adminMenus[$parentMenuIndex]["postTypeSubmenuArray"], $submenuArray);
                    require_once($fileinfo->getRealPath() . '/' . $fileName);
                }
			}
		}
	}
}
