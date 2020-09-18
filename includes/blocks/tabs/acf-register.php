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
add_action('acf/init', 'acf_init_tabs_block');
function acf_init_tabs_block() {

    // Check function exists.
    if( function_exists('acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'tru-fetcher/tabs',
            'title'             => __('Tabs Block'),
            'description'       => __('Tru Fetcher Tabs block.'),
            'render_template'   => plugin_dir_path( dirname( __FILE__ ) ) . 'tabs/template/block-template.php',
            'category'          => 'widget',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'tabs', 'block' ),
            'mode'				=> 'edit',
        ));
    }
}