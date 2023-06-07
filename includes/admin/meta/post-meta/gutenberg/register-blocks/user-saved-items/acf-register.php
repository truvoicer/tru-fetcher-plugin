<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package tru-fetcher
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
add_action('acf/init', 'acf_init_user_saved_items_block');
function acf_init_user_saved_items_block() {

    // Check function exists.
    if( function_exists('acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'tru-fetcher/user-saved-items',
            'title'             => __('Saved Items Block'),
            'description'       => __('Tru Fetcher Saved Items block.'),
            'render_template'   => plugin_dir_path( dirname( __FILE__ ) ) . 'user-saved-items/template/block-template.php',
            'category'          => 'widget',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'saved', 'items', 'block' ),
            'mode'				=> 'edit',
        ));
    }
}