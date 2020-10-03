<?php
add_filter( 'graphql_resolve_field', function( $result, $source, $args, $context, $info, $type_name, $field_key, $field, $field_resolver ) {
    if ( $field_key === 'blocksJSON' ) {
        if (is_array($result)) {
            return json_encode($result);
        }
        $blocksObject = json_decode($result);
        $blocksJson = $this->listingsClass->buildListingsBlock($blocksObject, true);
        return $blocksJson;
    }
    return $result;
}, 10, 9 );