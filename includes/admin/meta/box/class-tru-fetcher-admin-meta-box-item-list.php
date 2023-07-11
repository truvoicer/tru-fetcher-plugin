<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Single_Item;
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
class Tru_Fetcher_Admin_Meta_Box_Item_List extends Tru_Fetcher_Admin_Meta_Box_Base
{
    protected string $id = 'item_list';
    protected string $title = 'Item Lists';

    protected array $config = [];

    public function __construct()
    {
        parent::__construct();
        $this->config = [
            'id' => $this->id,
            'title' => $this->title,
            'post_types' => [
                ['name' => Tru_Fetcher_Post_Types_Trf_Item_List::NAME],
                ['name' => Tru_Fetcher_Post_Types_Trf_Single_Item::NAME],
            ],
            'fields' => [
                [
                    'id' => 'item_list',
                    'type' => 'array',
                ],
            ]
        ];
    }
    public function renderPost(\WP_Post $post) {
        $post = parent::renderPost($post);
        if (empty($post->{$this->id}['item_list'])) {
            return $post;
        }
        if (!is_array($post->{$this->id}['item_list'])) {
            return $post;
        }
        $singleItemPostType = new Tru_Fetcher_Post_Types_Trf_Single_Item();
        $post->{$this->id}['item_list'] = array_map(function ($item) use($singleItemPostType) {
            $postTypeIdIdentifier = $singleItemPostType->getIdIdentifier();
            if (empty($postTypeIdIdentifier)) {
                return $item;
            }
            if (empty($item->{$postTypeIdIdentifier})) {
                return $item;
            }
            $postTypeId = $item->{$postTypeIdIdentifier};
            $args            = [
                'post_type'   => $singleItemPostType->getName(),
                'numberposts' => 1,
                'p' => (int)$postTypeId,
            ];
            $getItemListPosts = get_posts( $args );
            if (!count($getItemListPosts)) {
                return $item;
            }
            $item->{$postTypeIdIdentifier} = $singleItemPostType->renderPost(
                $getItemListPosts[array_key_first($getItemListPosts)]
            );
            return $item;
        }, $post->{$this->id}['item_list']);
        return $post;
    }
}
