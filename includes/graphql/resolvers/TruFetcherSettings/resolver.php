<?php
$optionKeys = [
    "admin_email"      => get_option( "admin_email" ),
    "blog_name"         => get_option( "blogname" ),
    "blog_description"  => get_option( "blogdescription" ),
    "date_format"      => get_option( "date_format" ),
    "default_category" => get_option( "default_category" ),
    "home_url"             => get_option( "home" ),
    "site_url"          => get_option( "siteurl" ),
    "posts_per_page"   => get_option( "posts_per_page" ),
];
add_filter( 'graphql_resolve_field', function( $result, $source, $args, $context, $info, $type_name, $field_key, $field, $field_resolver ) use($optionKeys) {
    if ( $field_key === 'truFetcherSettings' ) {
        $optionFields = \get_fields_clone("option");

        return [
            "settings_json" => json_encode(array_merge($optionKeys, ($optionFields)? $optionFields : [])),
        ];
    }
    return $result;
}, 10, 9 );
