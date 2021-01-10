<?php
if( !function_exists('RegisterCategoryTemplateBySlugField') ) {
    function RegisterCategoryTemplateBySlugField()
    {
        register_graphql_field(
            'RootQuery',
            'CategoryTemplateBySlug',
            [
                'type'        => 'CategoryTemplateBySlug',
                'description' => 'Category by category slug template field',
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
add_action( 'graphql_register_types', "RegisterCategoryTemplateBySlugField" );