<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\Admin\Blocks\Tru_Fetcher_Admin_Blocks;
use TruFetcher\Includes\Api\Config\Tru_Fetcher_Api_Config_Endpoints;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;

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
        $blockAttributes = $this->buildBlockAttributes($blockAttributes);
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

    private function findAttributeKeysByString(string $string, array $attributes, ?bool $isIdIdentifier = false) {
        $keys = [];
        foreach ($attributes as $key => $attribute) {
            if ($key === $string) {
                $keys[] = $key;
            } elseif ($isIdIdentifier &&str_starts_with($key, "{$string}__")) {
                $keys[] = $key;
            }
        }
        return $keys;
    }
    public function buildBlockAttributes(array $attributes, ?bool $includeDefaults = true) {
        $config = $this->getConfig();
        $configAttributes = $config['attributes'];
        $attributeDefaults = [];
        foreach ($configAttributes as $configAttribute) {
            $attributeDefaults[$configAttribute['id']] = $this->getAttributeDefaultValue($configAttribute);
        }
       $attributes = $this->buildTaxonomyBlockAttributes($attributes);
       $attributes = $this->buildPostTypeBlockAttributes($attributes);
       if ($includeDefaults) {
           $attributes = array_merge($attributeDefaults, $attributes);
       }
        return $attributes;
    }

    public function buildTaxonomyBlockAttributes(array $attributes) {
        $taxonomyManager = new Tru_Fetcher_Taxonomy();
        foreach ($taxonomyManager->getTaxonomies() as $taxonomyClass) {
            $taxonomy = new $taxonomyClass();
            $taxonomyIdIdentifier = $taxonomy->getIdIdentifier();
            if (empty($taxonomyIdIdentifier)) {
                continue;
            }

            $findAttributeKeys = $this->findAttributeKeysByString($taxonomyIdIdentifier, $attributes, true);
            if (!count($findAttributeKeys)) {
                continue;
            }
            foreach ($findAttributeKeys as $findAttributeKey) {
                $termId = $attributes[$findAttributeKey];
                if (!is_array($termId)) {
                    $termId = [$termId];
                }
                $attributes[$findAttributeKey] = get_terms([
                    'taxonomy' => $taxonomy->getName(),
                    'include' => $termId,
                ]);
            }
        }
        return $attributes;
    }
    public function buildPostTypeBlockAttributes(array $attributes) {
        $postTypesManager = new Tru_Fetcher_Post_Types();
        foreach ($postTypesManager->getCommonPostTypes() as $postTypeClass) {
            $postType = new $postTypeClass();
            $postTypeIdIdentifier = $postType->getIdIdentifier();
            if (empty($postTypeIdIdentifier)) {
                continue;
            }
            $findAttributeKeys = $this->findAttributeKeysByString($postTypeIdIdentifier, $attributes, true);
            if (!count($findAttributeKeys)) {
                continue;
            }
            foreach ($findAttributeKeys as $findAttributeKey) {
                $attrPostTypeId = $attributes[$findAttributeKey];
                if (empty($attrPostTypeId)) {
                    continue;
                }
                if (!is_array($attrPostTypeId)) {
                    $post = $postType->getPostTypeDataById($postType->getName(), (int)$attrPostTypeId);
                    if ($post) {
                        $attributes[$findAttributeKey] = $postTypesManager->buildPostTypeData($post);
                    } else {
                        $attributes[$findAttributeKey] = null;
                    }
                } else {
                    $posts = array_map(function($postTypeId) use($postTypesManager, $postType) {
                        $post = $postType->getPostTypeDataById($postType->getName(), (int)$postTypeId);
                        if ($post) {
                            return $postTypesManager->buildPostTypeData($post);
                        }
                        return null;
                    }, $attrPostTypeId);
                    $attributes[$findAttributeKey] = array_filter($posts, function($post) {
                        return $post instanceof \WP_Post;
                    });
                }
            }
        }
        return $attributes;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

}
