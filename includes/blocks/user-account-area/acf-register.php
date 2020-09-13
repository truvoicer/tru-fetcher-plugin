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
add_action('acf/init', 'acf_init_user_account_area_block');
function acf_init_user_account_area_block() {

    // Check function exists.
    if( function_exists('acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'tru-fetcher/user-area',
            'title'             => __('User Account Area Block'),
            'description'       => __('Tru Fetcher user account area block.'),
            'render_template'   => plugin_dir_path( dirname( __FILE__ ) ) . 'user-account-area/template/block-template.php',
            'category'          => 'widget',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'listings', 'block', 'user', 'account', 'area' ),
            'mode'				=> 'edit',
        ));
    }
}