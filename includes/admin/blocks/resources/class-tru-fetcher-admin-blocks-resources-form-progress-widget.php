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
class Tru_Fetcher_Admin_Blocks_Resources_Form_Progress_Widget extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'form_progress_widget_block';
    public const BLOCK_NAME = 'tru-fetcher/form-progress-widget-block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Form Progress Widget Block',
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            'heading' => [
                'id' => 'heading',
                'type' => 'string',
            ],
            'top_text' => [
                'id' => 'top_text',
                'type' => 'string',
            ],
            'bottom_text' => [
                'id' => 'bottom_text',
                'type' => 'string',
            ],
            'complete_text' => [
                'id' => 'complete_text',
                'type' => 'string',
                'default' => 'Completed',
            ],
            'not_complete_text' => [
                'id' => 'not_complete_text',
                'type' => 'string',
                'default' => 'Not Completed',
            ],
            'form_field_groups' => [
                'id' => 'form_field_groups',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
