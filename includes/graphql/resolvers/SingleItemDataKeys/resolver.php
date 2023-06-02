<?php
add_filter( 'graphql_resolve_field', function( $result, $source, $args, $context, $info, $type_name, $field_key, $field, $field_resolver ) {
    if ( $field_key === 'single_item_data_keys' ) {
        if (is_array($result)) {
            return json_encode($result);
        }
        return json_encode(\get_fields($source->ID));
    }
    return $result;
}, 10, 9 );
