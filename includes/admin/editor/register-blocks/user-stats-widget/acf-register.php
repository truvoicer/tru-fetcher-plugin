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
add_action('acf/init', 'acf_init_user_stats_widget');
function acf_init_user_stats_widget() {

    // Check function exists.
    if( function_exists('acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'tru-fetcher/user-stats-widget',
            'title'             => __('User Stats Widget'),
            'description'       => __('Tru Fetcher User Stats Widget.'),
            'render_template'   => plugin_dir_path( dirname( __FILE__ ) ) . 'user-stats-widget/template/block-template.php',
            'category'          => 'widget',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'user', 'stats', 'widget', 'block' ),
            'mode'				=> 'edit',
        ));
    }
}