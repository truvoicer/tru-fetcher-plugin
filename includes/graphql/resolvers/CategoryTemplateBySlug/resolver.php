<?php
use GraphQL\Error\UserError;

Tru_Fetcher_Class_Loader::loadClass('includes/posts/class-tru-fetcher-posts.php');

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
        if ($field_key === 'categoryTemplateBySlug') {
            if (!array_key_exists("slug", $args)) {
                throw new UserError( __( "Category slug not specified.", 'wp-graphql' ) );
            }

            $postsClass = new Tru_Fetcher_Posts();
            $getCategoryTemplate = $postsClass->getTemplate($args["slug"], "category", "category_templates");

            if (is_wp_error($getCategoryTemplate)) {
                throw new UserError( __( $getCategoryTemplate->get_error_message(), 'wp-graphql' ) );
            }

            $categoryTemplate = new \WPGraphQL\Model\Post($getCategoryTemplate);
            return [
                "category_template" => $categoryTemplate,
            ];
        }
        return $result;
    }, 10, 9);