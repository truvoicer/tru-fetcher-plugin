<?php

namespace TruFetcher\Includes\PostTypes;

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
    public const NAME = 'trf_item_list';
    public const ID_IDENTIFIER = 'item_list_id';
    public const API_ID_IDENTIFIER = 'item_list';
    protected string $apiIdIdentifier = self::API_ID_IDENTIFIER;
    protected string $idIdentifier = self::ID_IDENTIFIER;
    protected string $name = self::NAME;


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

}
