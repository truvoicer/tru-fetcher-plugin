<?php

namespace TruFetcher\Includes\PostTypes;

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
class Tru_Fetcher_Post_Types {

    private array $commonPostTypes = [
        Tru_Fetcher_Post_Types_Trf_Post_Tpl::class,
        Tru_Fetcher_Post_Types_Trf_Filter_List::class,
        Tru_Fetcher_Post_Types_Trf_Category_Tpl::class,
        Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::class,
        Tru_Fetcher_Post_Types_Trf_Single_Item::class,
        Tru_Fetcher_Post_Types_Post::class,
        Tru_Fetcher_Post_Types_Page::class,
    ];
    private array $allPostTypes = [
        Tru_Fetcher_Post_Types_Trf_Post_Tpl::class,
        Tru_Fetcher_Post_Types_Trf_Single_Item::class,
        Tru_Fetcher_Post_Types_Trf_Filter_List::class,
        Tru_Fetcher_Post_Types_Trf_Item_List::class,
        Tru_Fetcher_Post_Types_Trf_Category_Tpl::class,
        Tru_Fetcher_Post_Types_Trf_Item_View_Tpl::class,
        Tru_Fetcher_Post_Types_Post::class,
        Tru_Fetcher_Post_Types_Page::class,
    ];

    public function buildPostTypeData(\WP_Post $post) {
        foreach ($this->getCommonPostTypes() as $postType) {
            if ($post->post_type !== $postType::NAME) {
                continue;
            }
            $postTypeInstance = new $postType();
            if (!method_exists($postTypeInstance, 'renderPost')) {
                continue;
            }
            return $postTypeInstance->renderPost($post);
        }

        return $post;
    }

    /**
     * @return array
     */
    public function getCommonPostTypes(): array
    {
        return $this->commonPostTypes;
    }

    public function getAllPostTypes(): array
    {
        return $this->allPostTypes;
    }

    public function findPostTypeByName(string $name) {
        foreach ($this->getAllPostTypes() as $postType) {
            $postTypeName = (new $postType())->getName();
            if ($postTypeName === $name) {
                return $postType;
            }
        }
        return null;
    }
}
