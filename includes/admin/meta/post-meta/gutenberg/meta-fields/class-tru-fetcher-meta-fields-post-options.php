<?php

namespace TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Post;

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
class Tru_Fetcher_Meta_Fields_Post_Options extends Tru_Fetcher_Meta_Fields_Base
{
    public const META_KEY_POST_TEMPLATE_CATEGORY = 'post_options_post_template_category';

    protected string $name = 'post_options';
    protected string $postType = Tru_Fetcher_Post_Types_Post::NAME;
    protected array $fields = [
        [
            'post_type' => [
                Tru_Fetcher_Post_Types_Post::NAME
            ],
            'meta_key' => self::META_KEY_POST_TEMPLATE_CATEGORY,
            'default' => false,
            'args' => [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'integer',
            ]
        ],
    ];
}
