<?php
namespace TruFetcher\Includes\Admin\Blocks;

use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Carousel;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Content_Widgets;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Form;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Form_Progress_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Hero;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Listings;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Opt_In;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Posts;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Sidebar_Widgets;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Account;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Profile_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Social_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_User_Stats_Widget;
use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Widget_Board;
use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Post_Types;
use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Taxonomies;
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

    private array $blocks = [
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
        foreach ($this->blocks as $block) {
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

    public function getBlocks() {
        $data = [];
        foreach ($this->blocks as $block) {
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
//                        'default' => [],
                    ];
                }
            }
            $data[] = $config;
        }
        return $data;
    }

    public function getBlocksPostTypes() {
        $postTypes = [];
        foreach ($this->blocks as $block) {
            $blockClass = new $block();
            $config = $blockClass->getConfig();
            foreach ($config['post_types'] as $postType) {
                $postType['posts'] = Tru_Fetcher_Admin_Resources_Post_Types::getPostTypeData($postType['name']);
                $postTypes[] = $postType;
            }
        }
        return $postTypes;
    }
    public function getBlocksTaxonomies() {
        $taxonomies = [];
        foreach ($this->blocks as $block) {
            $blockClass = new $block();
            $config = $blockClass->getConfig();
            foreach ($config['taxonomies'] as $taxonomy) {
                $taxonomy['terms'] = Tru_Fetcher_Admin_Resources_Taxonomies::getTerms($taxonomy['name']);
                $taxonomies[] = $taxonomy;
            }
        }
        return $taxonomies;
    }
}
