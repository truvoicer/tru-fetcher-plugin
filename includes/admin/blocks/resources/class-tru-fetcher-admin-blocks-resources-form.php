<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\Api\Config\Tru_Fetcher_Api_Config_Endpoints;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Form_Presets;
use TruFetcher\Includes\Media\Tru_Fetcher_Media;
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
                'id' => 'presets',
                'type' => 'string',
                'default' => 'custom',
            ],
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
                'default' => 'custom',
            ],
            [
                'id' => 'endpoint_type',
                'type' => 'string',
            ],
            [
                'id' => 'fetch_user_data',
                'type' => 'boolean',
                'default' => false,
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
                'id' => 'external_providers',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
    public static function buildFileTypes(array $buildAttributes) {
        if (empty($buildAttributes['form_rows'])) {
            return $buildAttributes;
        }
        if (!is_array($buildAttributes['form_rows'])) {
            return $buildAttributes;
        }
        $allowedFileTypes = [];
        foreach ($buildAttributes['form_rows'] as $key => $row) {
            if (empty($row['form_items'])) {
                continue;
            }
            if (!is_array($row['form_items'])) {
                continue;
            }
            foreach ($row['form_items'] as $itemKey => $item) {
                if (empty($item['allowed_file_types'])) {
                    continue;
                }
                if (!is_array($item['allowed_file_types'])) {
                    continue;
                }
                foreach ($item['allowed_file_types'] as $fileTypeKey => $fileType) {
                    if (empty($fileType['type']['name'])) {
                        continue;
                    }
                    if ($fileType['type']['name'] !== 'custom') {
                        $getConfigs = Tru_Fetcher_Media::getConfigByName(
                            [$fileType['type']['name']],
                            $fileType['type']['parent'],
                        );
                        $allowedFileTypes = [...$allowedFileTypes, ...$getConfigs];
                        continue;
                    }
                    if (empty($fileType['mime_type'])) {
                        continue;
                    }
                    if (empty($fileType['extension'])) {
                        continue;
                    }
                    $allowedFileTypes[] = [
                        'mime_type' => $fileType['mime_type'],
                        'extension' => $fileType['extension'],
                    ];
                }
                $buildAttributes['form_rows'][$key]['form_items'][$itemKey]['allowed_file_types'] = $allowedFileTypes;
            }
        }
        return $buildAttributes;
    }

    public function buildBlockAttributes(array $attributes, ?bool $includeDefaults = true, ?string $content = null, $block = null)
    {
        $buildAttributes = parent::buildBlockAttributes($attributes);
        if (!empty($buildAttributes['presets']) && $buildAttributes['presets'] !== 'custom') {
            $preset = (new Tru_Fetcher_Api_Helpers_Form_Presets())
                ->getFormPresetsRepository()
                ->findById((int)$buildAttributes['presets']);
            if (!empty($preset['config_data'])) {
                $buildAttributes = $preset['config_data'];
            }
        }
        $buildAttributes = self::buildEndpoint($buildAttributes);
        $buildAttributes = self::buildFileTypes($buildAttributes);

        return $buildAttributes;
    }
    public static function buildEndpoint(array $buildAttributes) {
        if (empty($buildAttributes['endpoint'])) {
            return $buildAttributes;
        }
        $endpoint = $buildAttributes['endpoint'];
        switch ($endpoint) {
            case 'email':
                $configEndpoint = Tru_Fetcher_Api_Config_Endpoints::ENDPOINT_EMAIL;
                break;
            case 'user_meta':
                $configEndpoint = Tru_Fetcher_Api_Config_Endpoints::ENDPOINT_USER_META;
                break;
            case 'account_details':
                $configEndpoint = Tru_Fetcher_Api_Config_Endpoints::ENDPOINT_USER_UPDATE;
                break;
            case 'user_profile':
                $configEndpoint = Tru_Fetcher_Api_Config_Endpoints::ENDPOINT_USER_PROFILE;
                break;
            case 'redirect':
                $configEndpoint = Tru_Fetcher_Api_Config_Endpoints::ENDPOINT_REDIRECT;
                break;
            default:
                return $buildAttributes;
        }
        $buildAttributes['endpoint_url'] = (new Tru_Fetcher_Api_Config_Endpoints())->buildEndpoint(
            $configEndpoint
        );
        return $buildAttributes;
    }


}
