<?php
if( !function_exists('RegisterSingleItemDataKeysField') ) {
    function RegisterSingleItemDataKeysField()
    {
        register_graphql_field(
            'fetcherSingleItem',
            'single_item_data_keys',
            [
                'type'        => 'String',
                'description' => 'A single item data keys json array'
            ]
        );
    }
}
add_action( 'graphql_register_types', "RegisterSingleItemDataKeysField" );