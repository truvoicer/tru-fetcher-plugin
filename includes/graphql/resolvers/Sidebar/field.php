<?php
if( !function_exists('RegisterSidebarField') ) {
    function RegisterSidebarField()
    {
        register_graphql_field(
            'RootQuery',
            'Sidebar',
            [
                'type'        => 'Sidebar',
                'description' => 'a sidebar',
                'args'        => [
                    'slug' => [
                        'type' => [
                            'non_null' => 'String',
                        ],
                    ],
                ]
            ]
        );
    }
}
add_action( 'graphql_register_types', "RegisterSidebarField" );