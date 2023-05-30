<?php

namespace TruFetcher\Includes\Admin\Taxonomies;

class Tru_Fetcher_Categories_Taxonomy
{
    const TAXONOMY_TR_NEWS_APP_CATEGORIES = 'tr_news_app_categories';

    public function register() {
        add_action('init', [$this, 'TruFetcherCategoryTaxonomy'], 0);
    }

    public function TruFetcherCategoryTaxonomy()
    {
        $labels = array(
            'name' => _x('News App Categories', 'News App Categories', 'text_domain'),
            'singular_name' => _x('News App Category', 'News App Category', 'text_domain'),
            'menu_name' => __('News App Categories', 'text_domain'),
            'all_items' => __('All Categories', 'text_domain'),
            'parent_item' => __('Parent Category', 'text_domain'),
            'parent_item_colon' => __('Parent Category:', 'text_domain'),
            'new_item_name' => __('New Category Name', 'text_domain'),
            'add_new_item' => __('Add New Category', 'text_domain'),
            'edit_item' => __('Edit Category', 'text_domain'),
            'update_item' => __('Update Category', 'text_domain'),
            'view_item' => __('View Category', 'text_domain'),
            'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
            'add_or_remove_items' => __('Add or remove items', 'text_domain'),
            'choose_from_most_used' => __('Choose from the most used', 'text_domain'),
            'popular_items' => __('Popular Categories', 'text_domain'),
            'search_items' => __('Search Categories', 'text_domain'),
            'not_found' => __('Not Found', 'text_domain'),
            'no_terms' => __('No items', 'text_domain'),
            'items_list' => __('Categories list', 'text_domain'),
            'items_list_navigation' => __('Categories list navigation', 'text_domain'),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'trNewsAppCategory',
            'graphql_plural_name' => 'trNewsAppCategories',
        );
        register_taxonomy(self::TAXONOMY_TR_NEWS_APP_CATEGORIES, array('page', 'post'), $args);
    }
}
