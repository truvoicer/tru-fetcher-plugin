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
class Tru_Fetcher_Meta_Fields_Post_Options
{
    public const NAME = 'post_options';
    public const FIELDS = [
        [
            'post_type' => 'post',
            'meta_key' => 'post_options_post_template_category',
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'integer',
            ]
        ],
    ];
}
