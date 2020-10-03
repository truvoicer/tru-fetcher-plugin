<?php
if( !function_exists('RegisterSidebarType') ) {
    function RegisterSidebarType()
    {
        register_graphql_object_type( 'Sidebar', [
            'description' => __( "Site sidebar", 'your-textdomain' ),
            'fields' => [
                'sidebar_name' => [
                    'type' => "String",
                    'description' => __( 'Sidebar name', 'your-textdomain' ),
                ],
                'widgets_json' => [
                    'type'        => "String",
                    'description' => __( 'Sidebar widgets', 'your-textdomain' ),
                ],
                'sidebar_error' => [
                    'type' => "String",
                    'description' => __( 'Sidebar error', 'your-textdomain' ),
                ],
            ],
        ] );
    }
}
add_action( 'graphql_register_types', "RegisterSidebarType" );