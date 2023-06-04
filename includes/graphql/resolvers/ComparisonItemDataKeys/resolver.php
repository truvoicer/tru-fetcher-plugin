<?php
use GraphQL\Error\UserError;

add_filter( 'graphql_resolve_field', function($result, $source, $args, $context, $info, $type_name, $field_key, $field, $field_resolver ) {
    if ( $field_key === 'comparison_item_data_keys' ) {
        if (is_array($result)) {
            return json_encode($result);
        }
        $getPost = get_posts([
            'numberposts'      => 1,
            'post_type'        => 'ft_single_comparison',
            "slug" => $source->slug
        ]);
        if (count($getPost) === 0) {
            throw new UserError( __( "Comparison post not found", 'wp-graphql' ) );
        }
        return json_encode(\get_fields_clone($source->ID));
    }
    return $result;
}, 10, 9 );
