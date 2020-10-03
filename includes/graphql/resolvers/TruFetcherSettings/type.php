<?php
if( !function_exists('RegisterTruFetcherSettingsType') ) {
    function RegisterTruFetcherSettingsType()
    {
        register_graphql_object_type('TruFetcherSettings', [
            'description' => __("Tru Fetcher Plugin Settings", 'your-textdomain'),
            'fields' => [
                'settings_json' => [
                    'type' => "String",
                    'description' => __('Tru Fetcher settings JSON', 'your-textdomain'),
                ]
            ],
        ]);
    }
}
add_action( 'graphql_register_types', "RegisterTruFetcherSettingsType" );