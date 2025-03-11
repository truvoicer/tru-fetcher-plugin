<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
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
    protected array $defaultAttributes = [
        [
            'id' => 'sidebar_layout_position',
            'type' => 'string',
            'default' => 'default',
            'form_control' => 'select',
            'options' => [
                [
                    'label' => 'Default',
                    'value' => 'default'
                ],
                [
                    'label' => 'Outside Sidebar Top',
                    'value' => 'outside_top'
                ],
                [
                    'label' => 'Outside Sidebar Bottom',
                    'value' => 'outside_bottom'
                ],
            ],
        ],
        [
            'id' => 'block_width',
            'type' => 'integer',
            'default' => 12,
        ],
        [
            'id' => 'access_control',
            'type' => 'string',
            'default' => 'public',
            'form_control' => 'select',
            'options' => [
                [
                    'label' => 'Public',
                    'value' => 'public'
                ],
                [
                    'label' => 'Protected',
                    'value' => 'protected'
                ],
            ],
        ],
        [
            'id' => 'title',
            'type' => 'string',
            'default' => null,
        ],
        [
            'id' => 'additional_styles',
            'type' => 'string',
            'default' => null,
        ],
    ];

    public static function getSidebarConfig() {
        return [
            [
                'id' => 'show_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'select_sidebar',
                'type' => 'array',
                'default' => [],
            ],
        ];
    }

    public function renderBlock( $blockAttributes, ?string $content = null, $block = null) {
        $config = $this->getConfig();
        $id = $config['id'];
        $blockAttributes = $this->buildBlockAttributes($blockAttributes, true, $content, $block);
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
    public function buildBlockAttributes(array $attributes, ?bool $includeDefaults = true, ?string $content = null, $block = null) {
        $config = $this->getConfig();
        $configAttributes = $config['attributes'];
        $attributeDefaults = [];

        foreach ($configAttributes as $configAttribute) {
            $children = $this->getClassAttributeChildren($configAttribute);
            $classAttribute = $this->buildClassAttribute($configAttribute);

            if ($classAttribute && $children && !empty($classAttribute['id']) && !empty($attributes[$classAttribute['id']])) {
                foreach ($children as $attChild) {
                    if (empty($attChild)) {
                        continue;
                    }
                    if (!class_exists($attChild)) {
                        continue;
                    }

                    $attChildClass = new $attChild();
                    $findIndex = array_search($attChildClass::BLOCK_ID, array_column($attributes[$classAttribute['id']], 'id'));
                    if ($findIndex === false) {
                        continue;
                    }
                    $classAttributeData = $attributes[$classAttribute['id']][$findIndex];
                    if (is_array($classAttributeData)) {
                        $attributes[$classAttribute['id']][$findIndex] = $attChildClass->buildBlockAttributes($classAttributeData);
                    }
                }
            }


            $attributeDefaults[$configAttribute['id']] = $this->getAttributeDefaultValue($configAttribute);
        }
        return $this->buildAttributePostData($attributes, $includeDefaults, $attributeDefaults);
    }

    private function buildAttributePostData(array $attributes, ?bool $includeDefaults = true, ?array $attributeDefaults = []) {

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
                    'include' => array_map('intval', $termId),
                    'hide_empty' => false,
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

    public function getClassAttributeChildren(array $attribute): bool|array {
        if (empty($attribute['class'])) {
            return false;
        }
        if (!class_exists($attribute['class'])) {
            return false;
        }
        $attClass = new $attribute['class']();
        $attConfig = $attClass->getConfig();
        if (!isset($attConfig['children'])) {
            return false;
        }
        if (!is_array($attConfig['children'])) {
            return false;
        }
        return $attConfig['children'];
    }

    public function buildClassAttribute(array $attribute) {
        $children = $this->getClassAttributeChildren($attribute);
        if (!$children) {
            return false;
        }
        foreach ($children as $attChild) {
            if (empty($attChild)) {
                continue;
            }
            if (!class_exists($attChild)) {
                continue;
            }
            $attChildClass = new $attChild();
            $attChildConfig = $attChildClass->getConfig();
            if (empty($attribute['child_configs']) || !is_array($attribute['child_configs'])) {
                $attribute['child_configs'] = [];
            }
            $attribute['child_configs'][$attChildConfig['id']] = $attChildConfig['attributes'];
            $attribute['default'] = [];
            unset($attribute['class']);
        }
        return $attribute;
    }

    public function buildClassAttributes(array $attributes)
    {
        foreach ($attributes as $index => $attribute) {
                if (!empty($attribute['form_control'])) {
                switch ($attribute['form_control']) {
                    case 'select':
                        if (empty($attribute['options'])) {
                            break;
                        }
                        if ($attribute['options'] instanceof \Closure) {
                            $attribute['options'] = $attribute['options']();
                        }
                        if (!is_array($attribute['options'])) {
                            break;
                        }
                        break;
                }
            }
            $classAttribute = $this->buildClassAttribute($attribute);
            if (!$classAttribute) {
                $attributes[$index] = $attribute;
                continue;
            }
            $attributes[$index] = $classAttribute;
        }
        return $attributes;
    }

    /**
     * @return array
     */
    public function getConfig(?array $exclude = []): array
    {
        $config = $this->config;
        if (isset($config['attributes']) && is_array($config['attributes'])) {
            $config['attributes'] = array_merge($this->defaultAttributes, $config['attributes']);
        } else {
            $config['attributes'] = $this->defaultAttributes;
        }
        if (count($exclude)) {
            return array_filter($config, function($key) use($exclude) {
                return !in_array($key, $exclude);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $config;
    }

}
