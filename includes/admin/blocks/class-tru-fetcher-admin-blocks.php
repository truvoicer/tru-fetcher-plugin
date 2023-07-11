<?php

namespace TruFetcher\Includes\Admin\Blocks;

use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Carousel;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Filters;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Form;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Form_Progress_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Hero;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Item_View;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Listings;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Opt_In;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Posts;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Tabs;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Account;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Profile_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Social_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Stats_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Widget_Board;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Base;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Tru_Fetcher_Base;

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
class Tru_Fetcher_Admin_Blocks extends Tru_Fetcher_Base
{
    use Tru_Fetcher_Traits_Errors;

    private string $blockAssetsPrefix = TRU_FETCHER_PLUGIN_DIR . "src/wp/blocks";

    public const BLOCKS = [
        Tru_Fetcher_Admin_Blocks_Resources_Listings::class,
        Tru_Fetcher_Admin_Blocks_Resources_Hero::class,
        Tru_Fetcher_Admin_Blocks_Resources_User_Account::class,
        Tru_Fetcher_Admin_Blocks_Resources_Form::class,
        Tru_Fetcher_Admin_Blocks_Resources_Carousel::class,
        Tru_Fetcher_Admin_Blocks_Resources_Opt_In::class,
        Tru_Fetcher_Admin_Blocks_Resources_Posts::class,
        Tru_Fetcher_Admin_Blocks_Resources_Form_Progress_Widget::class,
        Tru_Fetcher_Admin_Blocks_Resources_User_Stats_Widget::class,
        Tru_Fetcher_Admin_Blocks_Resources_User_Profile_Widget::class,
        Tru_Fetcher_Admin_Blocks_Resources_User_Social_Widget::class,
        Tru_Fetcher_Admin_Blocks_Resources_Widget_Board::class,
        Tru_Fetcher_Admin_Blocks_Resources_Tabs::class,
        Tru_Fetcher_Admin_Blocks_Resources_Item_View::class,
//        Tru_Fetcher_Admin_Blocks_Resources_Sidebar_Widgets::class,
//        Tru_Fetcher_Admin_Blocks_Resources_Content_Widgets::class,
    ];

    public function init()
    {
        add_action('init', [$this, 'registerBlocks']);
    }

    public function buildBlockAssetsPath(string $blockName): string
    {
        return $this->blockAssetsPrefix . DIRECTORY_SEPARATOR . $blockName;
    }

    public function registerBlocks()
    {
        foreach (self::BLOCKS as $block) {
            $blockClass = new $block();
            $config = $blockClass->getConfig();
            $id = $config['id'];
            $name = $config['name'];
            if (!method_exists($blockClass, 'renderBlock')) {
                $this->addError(
                    new \WP_Error(
                        'tru_fetcher_block_error',
                        __('The block class does not have a renderBlock method', 'tru-fetcher'),
                        ['blockClass' => $blockClass]
                    )
                );
                return;
            }
            $registerBlock = register_block_type($name, [
                'api_version' => 3,
                'editor_script' => 'gutenberg',
                'render_callback' => [$blockClass, 'renderBlock'],
            ]);
            if (!$registerBlock) {
                $this->addError(
                    new \WP_Error(
                        'tru_fetcher_block_error',
                        __('Error registering block type', 'tru-fetcher'),
                        ['id' => $id, 'name' => $name]
                    )
                );
                return;
            }
        }
    }

    public function getBlocks()
    {
        $data = [];
        foreach (self::BLOCKS as $block) {
            $blockClass = new $block();
            $config = $blockClass->getConfig();
            if (isset($config['children'])) {
                foreach ($config['children'] as $index => $child) {
                    $config['children'][$index] = [
                        'id' => $child::BLOCK_ID,
                        'name' => $child::BLOCK_NAME,
                        'title' => $child::BLOCK_TITLE,
                    ];
                    $config['attributes'][] = [
                        'id' => $child::BLOCK_ID,
                        'type' => 'object',
                    ];
                }
            }
//            if (isset($config['attributes'])) {
//                foreach ($config['attributes'] as $index => $attribute) {
//                    if (isset($attribute['default'])) {
//                        continue;
//                    }
//                    $config['attributes'][$index]['default'] = $blockClass->getAttributeDefaultValue($attribute, true);
//                }
//            }
            $data[] = $config;
        }
        return $data;
    }

    public function getBlocksPostTypes()
    {
        $postTypeManager = new Tru_Fetcher_Post_Types();
        $postTypes = [];
        foreach (self::BLOCKS as $block) {
            $blockClass = new $block();
            $config = $blockClass->getConfig();
            foreach ($config['post_types'] as $postType) {
                if (in_array($postType['name'], array_column($postTypes, 'name'))) {
                    continue;
                }
                $postTypeClass = $postTypeManager->findPostTypeByName($postType['name']);
                if (!$postTypeClass) {
                    continue;
                }
                $postTypeInstance = new $postTypeClass();
                $postType['id_identifier'] = $postTypeInstance->getIdIdentifier();
                $postType['posts'] = Tru_Fetcher_Post_Types_Base::getPostTypeData($postType['name']);
                $postTypes[] = $postType;
            }
        }
        return $postTypes;
    }

    public function getBlocksTaxonomies()
    {
        $taxonomyManager = new Tru_Fetcher_Taxonomy();
        $taxonomies = [];
        foreach (self::BLOCKS as $block) {
            $blockClass = new $block();
            $config = $blockClass->getConfig();
            foreach ($config['taxonomies'] as $taxonomy) {
                if (in_array($taxonomy['name'], array_column($taxonomy, 'name'))) {
                    continue;
                }
                $taxonomyClass = $taxonomyManager->findTaxonomyClassByName($taxonomy['name']);
                if (!$taxonomyClass) {
                    continue;
                }
                $taxonomyInstance = new $taxonomyClass();
                $taxonomy['id_identifier'] = $taxonomyInstance->getIdIdentifier();
                $taxonomy['terms'] = $taxonomyManager->getTerms($taxonomy['name']);
                $taxonomies[] = $taxonomy;
            }
        }
        return $taxonomies;
    }
}
