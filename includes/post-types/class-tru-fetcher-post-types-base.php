<?php

namespace TruFetcher\Includes\PostTypes;

use TruFetcher\Includes\Admin\Blocks\Tru_Fetcher_Admin_Blocks;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields;
use TruFetcher\Includes\Admin\Meta\Tru_Fetcher_Admin_Meta;

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
class Tru_Fetcher_Post_Types_Base {
    protected Tru_Fetcher_Admin_Meta $meta;

    protected string $name;
    private string $menuName;
    private string $menuAdminBar;

    protected string $idIdentifier;
    protected string $apiIdIdentifier;

    public function __construct()
    {
        $this->meta = new Tru_Fetcher_Admin_Meta();
    }

    protected array $argDefaults = [
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'show_in_rest'          => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'hierarchical' => true,
    ];

    protected function registerPostType(string $name, string $singularName, ?array $args = []): void
    {
        $args = $this->getArgs($name, $singularName, $args);

        add_action( 'init', function () use ($name, $args) {
            register_post_type( $this->name, $args );
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
    public static function getPostTypeData(string $postType)
    {
        return get_posts([
            'post_type' => $postType,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);
    }

    public function renderPost(\WP_Post $post) {
        $metaBoxClasses = $this->meta->getMetaboxClasses([$this->name]);
        foreach ($metaBoxClasses as $metaBoxClass) {
            $metaBox = new $metaBoxClass();
            $post = $metaBox->renderPost($post);
        }

        $metaFieldClasses = Tru_Fetcher_Meta_Fields::META_FIELDS;
        foreach ($metaFieldClasses as $metaFieldClass) {
            $metaField = new $metaFieldClass();
            $post = $metaField->renderPost($post);
        }

        return $post;
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
    public function getIdIdentifier(): string
    {
        return $this->idIdentifier;
    }

    /**
     * @return string
     */
    public function getApiIdIdentifier(): string
    {
        return $this->apiIdIdentifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getConfig()
    {
        return [
            'name' => $this->name,
            'idIdentifier' => $this->idIdentifier,
        ];
    }

}
