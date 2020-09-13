<?php
function TruFetcherGeneralTemplate()
{
	$labels = array(
		'name'                  => _x( 'General Templates', 'General Templates', 'text_domain' ),
		'singular_name'         => _x( 'General Template', 'General Template', 'text_domain' ),
		'menu_name'             => __( 'General Templates', 'text_domain' ),
		'name_admin_bar'        => __( 'General Templates', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'General Templates', 'text_domain' ),
		'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
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
		'graphql_single_name' => 'generalTemplate',
		'graphql_plural_name' => 'generalTemplates',
	);
	register_post_type( 'general_templates', $args );
}
add_action( 'init', "TruFetcherGeneralTemplate" );