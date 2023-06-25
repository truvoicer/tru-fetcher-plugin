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
class Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public array $config;
    public function renderBlock( $blockAttributes, $content ) {
//        var_dump($blockAttributes);
        $config = $this->getConfig();
        $id = $config['id'];
        $props = [
            'id' => $id,
            'data' => json_encode($blockAttributes),
        ];
        $propsString = '';
        foreach ($props as $key => $value) {
            $propsString .= "$key='$value' ";
        }
        return "<div {$propsString}>&nbsp;</div>";
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
