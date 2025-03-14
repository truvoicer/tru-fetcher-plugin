<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;


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
class Tru_Fetcher_Admin_Blocks_Resources_Register extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'register_block';
    public const BLOCK_NAME = 'tru-fetcher/register-block';
    public const BLOCK_TITLE = 'Tf Register Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'form_type',
                'type' => 'string',
                'default' => 'default',
                'form_control' => 'select',
                'options' => [
                    [
                        'label' => 'Default',
                        'value' => 'default',
                    ],
                    [
                        'label' => 'Custom',
                        'value' => 'custom',
                    ],
                ],
            ],
            [
                'id' => 'form_data',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'email_label',
                'type' => 'string',
                'default' => 'Email',
            ],
            [
                'id' => 'email_placeholder',
                'type' => 'string',
                'default' => 'Email',
            ],
            [
                'id' => 'username_label',
                'type' => 'string',
                'default' => 'Username',
            ],
            [
                'id' => 'username_placeholder',
                'type' => 'string',
                'default' => 'Username',
            ],
            [
                'id' => 'password_label',
                'type' => 'string',
                'default' => 'Password',
            ],
            [
                'id' => 'password_placeholder',
                'type' => 'string',
                'default' => 'Password',
            ],
            [
                'id' => 'submit_text',
                'type' => 'string',
                'default' => 'Submit',
            ],
            [
                'id' => 'cancel_text',
                'type' => 'string',
                'default' => 'Cancel',
            ],
            [
                'id' => 'success_message',
                'type' => 'string',
                'default' => 'Registration successful',
            ],
        ]
    ];
}
