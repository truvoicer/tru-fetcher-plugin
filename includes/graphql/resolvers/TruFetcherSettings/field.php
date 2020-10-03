<?php
if( !function_exists('RegisterTruFetcherSettingsField') ) {
    function RegisterTruFetcherSettingsField()
    {
        register_graphql_field(
            'RootQuery',
            'TruFetcherSettings',
            [
                'type' => 'TruFetcherSettings',
                'description' => 'All sidebars',
            ]
        );
    }
}
add_action( 'graphql_register_types', "RegisterTruFetcherSettingsField" );