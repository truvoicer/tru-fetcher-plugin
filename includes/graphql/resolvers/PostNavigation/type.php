<?php
if( !function_exists('RegisterPostNavigationType') ) {
    function RegisterPostNavigationType()
    {
        register_graphql_object_type( 'PostNavigation', [
            'description' => __( "Post navigation controls", 'your-textdomain' ),
            'fields' => [
                'prev_post' => [
                    'type' => "Post",
                    'description' => __( 'Sidebar name', 'your-textdomain' ),
                ],
                'next_post' => [
                    'type'        => "Post",
                    'description' => __( 'Sidebar widgets', 'your-textdomain' ),
                ],
            ],
        ] );
    }
}
add_action( 'graphql_register_types', "RegisterPostNavigationType" );