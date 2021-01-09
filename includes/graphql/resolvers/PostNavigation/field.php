<?php
if( !function_exists('RegisterPostNavigationField') ) {
    function RegisterPostNavigationField()
    {
        register_graphql_field(
            'RootQuery',
            'PostNavigation',
            [
                'type'        => 'PostNavigation',
                'description' => 'Post navigation controls field',
                'args'        => [
                    'name' => [
                        'type' => [
                            'non_null' => 'String',
                        ],
                    ],
                ]
            ]
        );
    }
}
add_action( 'graphql_register_types', "RegisterPostNavigationField" );