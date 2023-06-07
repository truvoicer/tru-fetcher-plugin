<?php

namespace TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields;

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
class Tru_Fetcher_Meta_Fields_Page_Options
{
    public const NAME = 'page_options';
    public const FIELDS = [
        [
            'post_type' => 'page',
            'meta_key' => 'page_options_page_type',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ],
        [
            'post_type' => 'page',
            'meta_key' => 'page_options_header_override',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'boolean',
            ]
        ],
        [
            'post_type' => 'page',
            'meta_key' => 'page_options_header_scripts',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ],
        [
            'post_type' => 'page',
            'meta_key' => 'page_options_footer_override',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'boolean',
            ]
        ],
        [
            'post_type' => 'page',
            'meta_key' => 'page_options_footer_scripts',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ]
        ]
    ];
}
