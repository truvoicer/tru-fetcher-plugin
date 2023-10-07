<?php

namespace TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Category_Tpl;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_View_Tpl;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Post_Tpl;

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
class Tru_Fetcher_Meta_Fields_Page_Options extends Tru_Fetcher_Meta_Fields_Base
{
    public const META_KEY_PAGE_TYPE = 'page_options_page_type';
    public const META_KEY_PAGE_TEMPLATE = 'page_options_page_template';
    public const META_KEY_HEADER_OVERRIDE = 'page_options_header_override';
    public const META_KEY_HEADER_SCRIPTS = 'page_options_header_scripts';
    public const META_KEY_FOOTER_OVERRIDE = 'page_options_footer_override';
    public const META_KEY_FOOTER_SCRIPTS = 'page_options_footer_scripts';

    protected string $name = 'page_options';
    protected array $fields = [
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Page::NAME,
                Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Category_Tpl::NAME
            ],
            'meta_key' => self::META_KEY_PAGE_TYPE,
            'default' => 'general',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ],
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Page::NAME,
                Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Category_Tpl::NAME
            ],
            'meta_key' => self::META_KEY_PAGE_TEMPLATE,
            'default' => 'full-width',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ],
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Page::NAME,
                Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Category_Tpl::NAME
            ],
            'meta_key' => self::META_KEY_HEADER_OVERRIDE,
            'default' => false,
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'boolean',
            ]
        ],
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Page::NAME,
                Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Category_Tpl::NAME
            ],
            'meta_key' => self::META_KEY_HEADER_SCRIPTS,
            'default' => false,
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ],
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Page::NAME,
                Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Category_Tpl::NAME
            ],
            'meta_key' => self::META_KEY_FOOTER_OVERRIDE,
            'default' => false,
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'boolean',
            ]
        ],
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Page::NAME,
                Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
                Tru_Fetcher_Post_Types_Trf_Category_Tpl::NAME
            ],
            'meta_key' => self::META_KEY_FOOTER_SCRIPTS,
            'default' => false,
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ]
    ];
}
