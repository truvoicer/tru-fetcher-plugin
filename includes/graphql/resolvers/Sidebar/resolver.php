<?php
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
        if ($field_key === 'sidebar') {
            if (!array_key_exists("slug", $args)) {
                return [
                    "sidebar_error" => "Error fetching sidebar."
                ];
            }
            $getSidebar = $this->sidebarClass->getSidebar($args["slug"]);
            if (!$getSidebar) {
                return [
                    "sidebar_error" => "Error fetching sidebar."
                ];
            }
            return [
                "sidebar_name" => $args["slug"],
                "widgets_json" => json_encode($getSidebar),
            ];
        }
        return $result;
    }, 10, 9);