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
add_action('acf/init', 'acf_init_optin_block');
function acf_init_optin_block() {

    // Check function exists.
    if( function_exists('acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'tru-fetcher/optin',
            'title'             => __('Opt In Block'),
            'description'       => __('Tru Fetcher Opt In block.'),
            'render_template'   => plugin_dir_path( dirname( __FILE__ ) ) . 'optin-block/template/block-template.php',
            'category'          => 'block',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'optin', 'block' ),
            'mode'				=> 'edit',
        ));
    }
}