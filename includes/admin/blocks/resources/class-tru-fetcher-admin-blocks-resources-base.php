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

    public function getAttributeDefaultValue(array $attribute) {
        $defaultValue = null;
        if (isset($attribute['default'])) {
            $defaultValue = $attribute['default'];
        }
        $type = null;
        if (isset($attribute['type']) ) {
            $type = $attribute['type'];
        }
        return match ($type) {
            'integer' => ($defaultValue)? (int)$defaultValue : null,
            'boolean' => (is_bool($defaultValue))? $defaultValue : null,
            'array' => ($defaultValue)?: [],
            default => $defaultValue,
        };
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
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

}
