<?php
namespace TruFetcher\Includes\Taxonomy;

use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;

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
class Tru_Fetcher_Taxonomy_Base {

    protected string $name;
    private string $menuName;
    private string $menuAdminBar;

    protected string $idIdentifier;
    protected array $argDefaults = [
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    ];

    public function __construct()
    {
        require_once ABSPATH . 'wp-admin/includes' . '/taxonomy.php';
    }

    protected function registerTaxonomy(string $name, string $singularName, array $objectTypes = [], ?array $args = []): void
    {
        $args = $this->getArgs($name, $singularName, $args);

        add_action( 'init', function () use ($name, $args, $objectTypes) {
            register_taxonomy( $this->name, $objectTypes, $args );
        }, 0 );

    }
    protected function getArgs(string $name, string $singularName, ?array $args = []): array
    {
        $labels = $this->getLabels($name, $singularName);
        $args = array_merge($this->argDefaults, $args);
        $args['label'] = $labels['name'];
        $args['labels'] = $labels;
        return $args;
    }

    protected function getLabels(string $name, string $singularName): array
    {
        $labels = [
            'name'                  => _x( $name, $name, 'text_domain' ),
            'singular_name'         => _x( $singularName, $singularName, 'text_domain' ),
        ];
        $menuName = $name;
        $menuAdminBar = $name;
        if (!empty($this->menuName)) {
            $menuName = $this->menuName;
        }
        if (!empty($this->menuAdminBar)) {
            $menuAdminBar = $this->menuAdminBar;
        }
        $labels['menu_name'] = __( $menuName, 'text_domain' );
        $labels['name_admin_bar'] = __( $menuAdminBar, 'text_domain' );
        return $labels;
    }

    /**
     * @param string $menuName
     */
    public function setMenuName(string $menuName): void
    {
        $this->menuName = $menuName;
    }

    /**
     * @param string $menuAdminBar
     */
    public function setMenuAdminBar(string $menuAdminBar): void
    {
        $this->menuAdminBar = $menuAdminBar;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdIdentifier(): string
    {
        return $this->idIdentifier;
    }
}
