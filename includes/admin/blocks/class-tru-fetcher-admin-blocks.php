<?php
namespace TruFetcher\Includes\Admin\Blocks;

use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Listings;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Item_List;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
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
            $config = $blockClass::CONFIG;
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
                'render_callback' => function ($attributes, $content) use ($blockClass) {
                    return $this->renderBlock($attributes, $content, $blockClass);
                },
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

    public function renderBlock( $blockAttributes, $content, $blockClass ) {
        $config = $blockClass::CONFIG;
        $id = $config['id'];
        $props = [
            'id' => $id,
            'data' => json_encode($blockAttributes),
        ];
        $propsString = '';
        foreach ($props as $key => $value) {
            $propsString .= "$key='$value' ";
        }
        return "<div {$propsString}>&nbsp;</div>";
    }
    public function getBlocks() {
        $data = [];
        foreach ($this->blocks as $block) {
            $config = $block::CONFIG;
            $data[] = $config;
        }
        return $data;
    }

    public function getBlocksPostTypes() {
        $postTypes = [];
        foreach ($this->blocks as $block) {
            $config = $block::CONFIG;
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
            $config = $block::CONFIG;
            foreach ($config['taxonomies'] as $taxonomy) {
                $taxonomy['terms'] = Tru_Fetcher_Admin_Resources_Taxonomies::getTerms($taxonomy['name']);
                $taxonomies[] = $taxonomy;
            }
        }
        return $taxonomies;
    }
}
