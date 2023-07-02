<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;

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
class Tru_Fetcher_Admin_Blocks_Resources_Form extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'form_block';
    public const BLOCK_NAME = 'tru-fetcher/form-block';
    public const BLOCK_TITLE = 'Tf Form Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
//        'ancestor' => [
//            Tru_Fetcher_Admin_Blocks_Resources_Opt_In::BLOCK_NAME,
//        ],
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'form_type',
                'type' => 'string',
                'default' => 'single',
            ],
            [
                'id' => 'method',
                'type' => 'string',
            ],
            [
                'id' => 'submit_button_label',
                'type' => 'string',
                'default' => 'Submit',
            ],
            [
                'id' => 'add_item_button_label',
                'type' => 'string',
                'default' => 'Add Item',
            ],
            [
                'id' => 'form_id',
                'type' => 'string',
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'sub_heading',
                'type' => 'string',
            ],
            [
                'id' => 'endpoint',
                'type' => 'string',
            ],
            [
                'id' => 'endpoint_type',
                'type' => 'string',
            ],
            [
                'id' => 'custom_endpoint',
                'type' => 'string',
            ],
            [
                'id' => 'redirect',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'redirect_url',
                'type' => 'string',
            ],
            [
                'id' => 'email_recipient',
                'type' => 'string',
            ],
            [
                'id' => 'email_subject',
                'type' => 'string',
            ],
            [
                'id' => 'email_from',
                'type' => 'string',
            ],
            [
                'id' => 'layout_style',
                'type' => 'string',
                'default' => 'full_width',
            ],
            [
                'id' => 'classes',
                'type' => 'string',
            ],
            [
                'id' => 'column_size',
                'type' => 'integer',
                'default' => 12,
            ],
            [
                'id' => 'align',
                'type' => 'string',
                'default' => 'left',
            ],
            [
                'id' => 'form_rows',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'endpoint_providers',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
