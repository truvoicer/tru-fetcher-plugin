<?php
namespace TruFetcher\Includes\Admin\Blocks;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Item_List;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
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

    private string $metaBoxIdPrefix = 'trf_mb';
    public static array $fieldGroups = [
        Tru_Fetcher_Meta_Fields_Page_Options::class,
        Tru_Fetcher_Meta_Fields_Post_Options::class,
    ];

    private array $metaBoxes = [
        Tru_Fetcher_Admin_Meta_Box_Single_Item::class,
        Tru_Fetcher_Admin_Meta_Box_Item_List::class,
    ];

    public function init()
    {
        add_action('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks()
    {
        $blockTypeName = 'tru-fetcher/listings-block';
        $args = [
            'title' => __('Tru Fetcher Listings Block', 'tru-fetcher'),
            'description' => __('A block to display listings', 'tru-fetcher'),
            'textdomain' => 'tru-fetcher',
            'keywords' => [
                __('Tru Fetcher', 'tru-fetcher'),
                __('Listings', 'tru-fetcher'),
                __('Tru Fetcher Listings', 'tru-fetcher'),
            ],
            'icon' => 'list-view',
            'category' => 'widgets',
        ];
        $path = TRU_FETCHER_PLUGIN_DIR . "src/wp/blocks/listings/listings.json";
        if (!file_exists($path)) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_block_file_not_found',
                    __('The block file was not found', 'tru-fetcher'),
                    ['path' => $path]
                )
            );
            return;
        }
        $registerBlock = register_block_type(new \WP_Block_Type($blockTypeName, $args));
        var_dump($registerBlock);
    }

}
