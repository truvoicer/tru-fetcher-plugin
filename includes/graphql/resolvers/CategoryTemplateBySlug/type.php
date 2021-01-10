<?php
if( !function_exists('RegisterCategoryTemplateBySlugType') ) {
    function RegisterCategoryTemplateBySlugType()
    {
        register_graphql_object_type( 'CategoryTemplateBySlug', [
            'description' => __( "Category template by category slug", 'your-textdomain' ),
            'fields' => [
                'category_template' => [
                    'type' => "Post",
                    'description' => __( 'The category template post', 'your-textdomain' ),
                ]
            ],
        ] );
    }
}
add_action( 'graphql_register_types', "RegisterCategoryTemplateBySlugType" );