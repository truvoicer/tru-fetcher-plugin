<?php
use GraphQL\Error\UserError;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;

add_filter('graphql_resolve_field',
    function (
        $result,
        $source,
        $args,
        WPGraphQL\AppContext $context,
        GraphQL\Type\Definition\ResolveInfo $info,
        $type_name,
        $field_key,
        GraphQL\Type\Definition\FieldDefinition $field,
        $field_resolver
    ) {
        if ($field_key === 'postNavigation') {
            $prevPost = null;
            $nextPost = null;
            if (!array_key_exists("name", $args)) {
                throw new UserError( __( "Post name not specified.", 'wp-graphql' ) );
            }

            $postsClass = new Tru_Fetcher_Posts();

            $getCategoryPostNav = $postsClass->getCategoryPostNavigation($args["name"]);
            if (is_wp_error($getCategoryPostNav)) {
                return [
                    "prev_post" => $prevPost,
                    "next_post" => $nextPost
                ];
            }
            $prevPost = null;
            $nextPost = null;
            if ($getCategoryPostNav["prev_post"]) {
                $prevPost = new \WPGraphQL\Model\Post($getCategoryPostNav["prev_post"]);
            }
            if ($getCategoryPostNav["next_post"]) {
                $nextPost = new \WPGraphQL\Model\Post($getCategoryPostNav["next_post"]);
            }
            return [
                "prev_post" => $prevPost,
                "next_post" => $nextPost
            ];
        }
        return $result;
    }, 10, 9);
