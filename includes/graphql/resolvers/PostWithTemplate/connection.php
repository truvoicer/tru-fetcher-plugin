<?php

use GraphQL\Error\UserError;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;

add_action( 'graphql_register_types', 'register_post_with_template_connection', 99 );

function register_post_with_template_connection() {
    $config = [
        'fromType' => 'RootQuery',
        'toType' => 'Post',
        'fromFieldName' => 'PostWithTemplate',
        'connectionTypeName' => 'PostWithTemplateConnection',
        'connectionArgs'        => [
            'name' => [
                'type' => [
                    'non_null' => 'String',
                ],
            ],
        ],
        'resolve' => function( $id, $args, $context, $info ) {
            $postsClass = new Tru_Fetcher_Posts();
            $postTemplate = $postsClass->getPostTemplateByPostName($args["where"]["name"]);
            if (is_wp_error($postTemplate)) {
                throw new UserError( __( $postTemplate->get_error_message(), 'wp-graphql' ) );
            }
            $where = [
                "where" => [
                    "p" => $postTemplate->ID
                ]
            ];
            $resolver   = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver( $postTemplate, $where, $context, $info, "post_templates" );
            return $resolver->get_connection();
        },
    ];
    register_graphql_connection( $config );
};
