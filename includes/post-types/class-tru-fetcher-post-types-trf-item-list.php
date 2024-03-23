<?php

namespace TruFetcher\Includes\PostTypes;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Keymaps;

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
class Tru_Fetcher_Post_Types_Trf_Item_List extends Tru_Fetcher_Post_Types_Base
{
    public const SERVICE_ID = 'service';
    public const ITEM_ID = 'item_id';
    public const PROVIDER = 'provider';
    public const NAME = 'trf_item_list';
    public const ID_IDENTIFIER = 'item_list_id';
    public const API_ID_IDENTIFIER = 'item_list';

    private Tru_Fetcher_Api_Helpers_Keymaps $keymapHelpers;
    protected string $apiIdIdentifier = self::API_ID_IDENTIFIER;
    protected string $idIdentifier = self::ID_IDENTIFIER;
    protected string $name = self::NAME;
    protected ?string $displayAs = 'list';


    public function __construct()
    {
        parent::__construct();
        $this->keymapHelpers = new Tru_Fetcher_Api_Helpers_Keymaps();
    }
    public function setDisplayAs(?string $displayAs = 'list'): self
    {
        $this->displayAs = $displayAs;
        return $this;
    }


    public function init()
    {
        $this->registerPostType(
            'Item Lists',
            'Item List',
            [
                'supports' => array('title'),
                'taxonomies' => array(),
                'menu_position' => 5,
                'capability_type' => 'page',
            ]
        );
    }
    public function renderSingleItem(\WP_Post $post) {
        if (!property_exists($post, Tru_Fetcher_Post_Types_Trf_Single_Item::API_ID_IDENTIFIER)) {
            return null;
        }
        $data = $post->{Tru_Fetcher_Post_Types_Trf_Single_Item::API_ID_IDENTIFIER};
        if (!is_array($data)) {
            return null;
        }
        if (empty($data[self::SERVICE_ID]) ||
            empty($data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID])
        ) {
            return null;
        }
        if (!is_array($data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID])) {
            return null;
        }
        switch ($this->displayAs) {
            case 'post_list':
                $keymap = $this->keymapHelpers->getKeymap((int)$data[self::SERVICE_ID]);
                $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID] = $this->keymapHelpers->mapDataKeysWithKeymap(
                    $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID],
                    $keymap
                );

                break;
        }
        $dataKeys = $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID];
        unset($data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID]);
        return array_merge(
            $data,
            [
                self::ITEM_ID => $post->ID,
                'post_name' => $post->post_name,
                self::PROVIDER => 'internal',
            ],
            $dataKeys
        );
    }
    public function renderPost(\WP_Post $post)
    {
        $buildPost = parent::renderPost($post);

        $buildData = array_map(function ($item) {
            switch ($item->type) {
                case 'single_item':
                    if (!property_exists($item, Tru_Fetcher_Post_Types_Trf_Single_Item::ID_IDENTIFIER)) {
                        return null;
                    }
                    return $this->renderSingleItem($item->{Tru_Fetcher_Post_Types_Trf_Single_Item::ID_IDENTIFIER});
                default:
                    return $item;
            }
        }, $buildPost->{self::API_ID_IDENTIFIER}[self::API_ID_IDENTIFIER]);
        return array_values(
            array_filter($buildData)
        );
    }

}
