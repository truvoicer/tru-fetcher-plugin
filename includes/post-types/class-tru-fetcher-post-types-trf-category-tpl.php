<?php

namespace TruFetcher\Includes\PostTypes;

use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Category;

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
class Tru_Fetcher_Post_Types_Trf_Category_Tpl extends Tru_Fetcher_Post_Types_Base
{
    public const NAME = 'trf_category_tpl';
    public const ID_IDENTIFIER = 'category_template_id';
    public const API_ID_IDENTIFIER = 'category_template';
    protected string $apiIdIdentifier = self::API_ID_IDENTIFIER;
    protected string $idIdentifier = self::ID_IDENTIFIER;
    protected string $name = self::NAME;

    public function init()
    {
        $this->registerPostType(
            'Category Templates',
            'Category Template',
            [
                'supports' => ['title', 'editor', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats'],
                'taxonomies' => [
                    Tru_Fetcher_Taxonomy_Category::NAME
                ],
                'menu_position' => 5,
                'capability_type' => 'page',
            ]
        );
    }

}
