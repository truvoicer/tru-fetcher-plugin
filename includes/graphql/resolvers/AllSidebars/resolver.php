<?php
add_filter( 'graphql_resolve_field', function( $result, $source, $args, WPGraphQL\AppContext $context,
                                               GraphQL\Type\Definition\ResolveInfo $info, $type_name, $field_key, GraphQL\Type\Definition\FieldDefinition $field,
                                               $field_resolver ) {
    if ( $field_key === 'sidebars' ) {

        $getSidebar = $this->sidebarClass->getAllSidebars();
        if (!$getSidebar) {
            return [
                "sidebar_error" => "Error fetching sidebar."
            ];
        }
        return [
            "sidebars_json" => json_encode($getSidebar),
        ];
    }
    return $result;
}, 10, 9 );