<?php
function TruFetcherPostTemplate()
{
	$labels = array(
		'name'                  => _x( 'Post Templates', 'Post Templates', 'text_domain' ),
		'singular_name'         => _x( 'Post Template', 'Post Template', 'text_domain' ),
		'menu_name'             => __( 'Post Templates', 'text_domain' ),
		'name_admin_bar'        => __( 'Post Templates', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Post Templates', 'text_domain' ),
		'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
        'taxonomies'            => array( 'category' ),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'show_in_rest'          => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_graphql' => true,
        'hierarchical' => true,
		'graphql_single_name' => 'postTemplate',
		'graphql_plural_name' => 'postTemplates',
	);
	register_post_type( 'post_templates', $args );
}
add_action( 'init', "TruFetcherPostTemplate" );