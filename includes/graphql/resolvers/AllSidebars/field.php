<?php
if( !function_exists('RegisterAllSidebarsField') ) {
    function RegisterAllSidebarsField()
    {
        register_graphql_field(
            'RootQuery',
            'Sidebars',
            [
                'type'        => 'Sidebars',
                'description' => 'All sidebars',
            ]
        );
    }
}
add_action( 'graphql_register_types', "RegisterAllSidebarsField" );