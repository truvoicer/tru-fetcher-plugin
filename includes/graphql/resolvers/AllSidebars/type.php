<?php
if( !function_exists('RegisterAllSidebarsType') ) {
    function RegisterAllSidebarsType()
    {
        register_graphql_object_type( 'Sidebars', [
            'description' => __( "All Site sidebars", 'your-textdomain' ),
            'fields' => [
                'sidebars_json' => [
                    'type'        => "String",
                    'description' => __( 'Sidebars JSON', 'your-textdomain' ),
                ],
                'sidebar_error' => [
                    'type' => "String",
                    'description' => __( 'Sidebar error', 'your-textdomain' ),
                ],
            ],
        ] );
    }
}
add_action( 'graphql_register_types', "RegisterAllSidebarsType" );