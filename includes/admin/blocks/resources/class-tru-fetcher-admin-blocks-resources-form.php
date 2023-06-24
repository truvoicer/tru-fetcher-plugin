<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Post_Types;
use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Taxonomies;
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
class Tru_Fetcher_Admin_Blocks_Resources_Form extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'form_block';
    public const BLOCK_NAME = 'tru-fetcher/form-block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Form Block',
        'ancestor' => [
            Tru_Fetcher_Admin_Blocks_Resources_Opt_In::BLOCK_NAME,
        ],
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            'form_type' => [
                'id' => 'form_type',
                'type' => 'string',
                'default' => 'single',
            ],
            'method' => [
                'id' => 'method',
                'type' => 'string',
            ],
            'submit_button_label' => [
                'id' => 'submit_button_label',
                'type' => 'string',
                'default' => 'Submit',
            ],
            'add_item_button_label' => [
                'id' => 'add_item_button_label',
                'type' => 'string',
                'default' => 'Add Item',
            ],
            'form_id' => [
                'id' => 'form_id',
                'type' => 'string',
            ],
            'heading' => [
                'id' => 'heading',
                'type' => 'string',
            ],
            'sub_heading' => [
                'id' => 'sub_heading',
                'type' => 'string',
            ],
            'endpoint' => [
                'id' => 'endpoint',
                'type' => 'string',
            ],
            'endpoint_type' => [
                'id' => 'endpoint_type',
                'type' => 'string',
            ],
            'custom_endpoint' => [
                'id' => 'custom_endpoint',
                'type' => 'string',
            ],
            'redirect' => [
                'id' => 'redirect',
                'type' => 'boolean',
                'default' => false,
            ],
            'redirect_url' => [
                'id' => 'redirect_url',
                'type' => 'string',
            ],
            'email_recipient' => [
                'id' => 'email_recipient',
                'type' => 'string',
            ],
            'email_subject' => [
                'id' => 'email_subject',
                'type' => 'string',
            ],
            'email_from' => [
                'id' => 'email_from',
                'type' => 'string',
            ],
            'layout_style' => [
                'id' => 'layout_style',
                'type' => 'string',
                'default' => 'full_width',
            ],
            'classes' => [
                'id' => 'classes',
                'type' => 'string',
            ],
            'column_size' => [
                'id' => 'column_size',
                'type' => 'integer',
                'default' => 12,
            ],
            'align' => [
                'id' => 'align',
                'type' => 'string',
                'default' => 'left',
            ],
            'form_rows' => [
                'id' => 'form_rows',
                'type' => 'array',
                'default' => [],
            ],
            'endpoint_providers' => [
                'id' => 'endpoint_providers',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
