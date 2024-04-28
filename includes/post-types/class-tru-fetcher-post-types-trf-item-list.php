<?php

namespace TruFetcher\Includes\PostTypes;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
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
    public function renderSingleItem(\WP_Post $post, ?array $keymap = null) {
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
        if (empty($keymap)) {
            $keymap = $this->keymapHelpers->getKeymap((int)$data[self::SERVICE_ID]);
        }
        switch ($this->displayAs) {
            case 'post_list':
                $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID] = $this->keymapHelpers->mapDataKeysWithKeymap(
                    $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID],
                    $keymap
                );

                break;
        }
        $dataKeys = $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID];
        unset($data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID]);
        if (empty($dataKeys[self::PROVIDER])) {
            $dataKeys[self::PROVIDER] = 'internal';
        }
        return array_merge(
            $data,
            [
                self::ITEM_ID => $post->ID,
                'post_name' => $post->post_name,
            ],
            $dataKeys
        );
    }
    public function renderPost(\WP_Post $post)
    {
        $buildPost = parent::renderPost($post);

        $serviceIds = [];
        foreach ($buildPost->{self::API_ID_IDENTIFIER}[self::API_ID_IDENTIFIER] as $item) {

            switch ($item->type) {
                case 'single_item':
                    if (!property_exists($item, Tru_Fetcher_Post_Types_Trf_Single_Item::ID_IDENTIFIER)) {
                        break;
                    }
                    $postData = $item->{Tru_Fetcher_Post_Types_Trf_Single_Item::ID_IDENTIFIER};
                    if (!property_exists($postData, Tru_Fetcher_Post_Types_Trf_Single_Item::API_ID_IDENTIFIER)) {
                        break;
                    }
                    $data = $postData->{Tru_Fetcher_Post_Types_Trf_Single_Item::API_ID_IDENTIFIER};

                    if (empty($data[self::SERVICE_ID])) {
                        break;
                    }
                    $serviceId = (int)$data[self::SERVICE_ID];
                    if (!in_array($serviceId, $serviceIds)) {
                        $serviceIds[] = $serviceId;
                    }
                    break;
            }
        }

        $keymap = $this->keymapHelpers->getKeymap($serviceIds[array_key_first($serviceIds)]);
        $labelData = $this->keymapHelpers->getLabelData($keymap);

        $buildData = array_map(function ($item) use($keymap) {
            switch ($item->type) {
                case 'single_item':
                    if (!property_exists($item, Tru_Fetcher_Post_Types_Trf_Single_Item::ID_IDENTIFIER)) {
                        return null;
                    }
                    return $this->renderSingleItem($item->{Tru_Fetcher_Post_Types_Trf_Single_Item::ID_IDENTIFIER}, $keymap);
                default:
                    return $item;
            }
        }, $buildPost->{self::API_ID_IDENTIFIER}[self::API_ID_IDENTIFIER]);
        return [
            'labels' => $labelData,
            'data' => array_values(
                array_filter($buildData)
            ),
        ];
    }

}
