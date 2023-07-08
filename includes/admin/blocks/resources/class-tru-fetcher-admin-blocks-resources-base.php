<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;

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
class Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public array $config;
    public function renderBlock( $blockAttributes, $content ) {
        $config = $this->getConfig();
        $id = $config['id'];
        $attributes = $config['attributes'];
        $attributeDefaults = [];
        foreach ($attributes as $attribute) {
            $attributeDefaults[$attribute['id']] = $this->getAttributeDefaultValue($attribute);
        }
        $blockAttributes = array_merge($attributeDefaults, $blockAttributes);
        $props = [
            'id' => $id,
            'data' => htmlspecialchars(json_encode($blockAttributes)),
        ];
        $propsString = '';
        foreach ($props as $key => $value) {
            $propsString .= "$key='$value' ";
        }
        return "<div {$propsString}>&nbsp;</div>";
    }

    public function getAttributeDefaultValue(array $attribute, ?bool $backend = false) {
        $defaultValue = null;
        if (isset($attribute['default'])) {
            $defaultValue = $attribute['default'];
        }
        $type = null;
        if (isset($attribute['type']) ) {
            $type = $attribute['type'];
        }
        switch ($type) {
            case 'integer':
                if (!$defaultValue && $backend) {
                    return '';
                }
                else if (!$defaultValue && !$backend) {
                    return null;
                }
                return (int)$defaultValue;
            case 'boolean':
                return (is_bool($defaultValue))? $defaultValue : null;
            case 'array':
                return ($defaultValue)?: [];
            default:
                if (!$defaultValue && $backend) {
                    return '';
                }
                else if (!$defaultValue && !$backend) {
                    return null;
                }
                return $defaultValue;
        }
    }
    protected function mergeConfigs(array $blockResources)
    {
        foreach ($blockResources as $blockResource) {
            $blockResourceInstance = new $blockResource();
            foreach ($blockResourceInstance->getConfig()['post_types'] as $postType) {
                if (!in_array($postType['name'], array_column($this->config['post_types'], 'name'))) {
                    $this->config['post_types'][] = $postType;
                }
            }
            foreach ($blockResourceInstance->getConfig()['taxonomies'] as $taxonomy) {
                if (!in_array($taxonomy['name'], array_column($this->config['taxonomies'], 'name'))) {
                    $this->config['taxonomies'][] = $taxonomy;
                }
            }
            $this->config['attributes'][] = [
                'id' => $blockResourceInstance::BLOCK_ID,
                'type' => 'object',
                'default' => null,
            ];
        }
    }

    public function getBlockDataFromPost(\WP_Post $post) {
        $blockData = parse_blocks($post->post_content);
        $findBlockDataIndex = array_search($this->config['name'], array_column($blockData, 'blockName'));
        if ($findBlockDataIndex === false) {
            return null;
        }
        return $blockData[$findBlockDataIndex];
    }
    public function buildPostBlockData(\WP_Post $post) {
        $blockData = $this->getBlockDataFromPost($post);
        if (empty($blockData)) {
            return $post;
        }
        if (!isset($blockData['attrs'])) {
            return $post;
        }
        $postTypes = new Tru_Fetcher_Post_Types();
        foreach ($postTypes->getPostTypes() as $postTypeClass) {
            $postType = new $postTypeClass();
            $postTypeIdIdentifier = $postType->getIdIdentifier();
            if (empty($postTypeIdIdentifier)) {
                continue;
            }
            if (!isset($blockData['attrs'][$postTypeIdIdentifier])) {
                continue;
            }
            $blockData['attrs'][$postTypeIdIdentifier] = $postType->getPostData($blockData['attrs'][$postTypeIdIdentifier]);
        }
        return $post;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

}
