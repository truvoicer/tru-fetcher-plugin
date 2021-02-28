<?php
if( !function_exists('RegisterComparisonItemDataKeysField') ) {
    function RegisterComparisonItemDataKeysField()
    {
        register_graphql_field(
            'fetcherSingleComparison',
            'single_item_data_keys',
            [
                'type'        => 'String',
                'description' => 'A single item data keys json array'
            ]
        );
    }
}
add_action( 'graphql_register_types', "RegisterComparisonItemDataKeysField" );