<?php

namespace TruFetcher\Includes\Widgets;

class Tru_Fetcher_Widgets_Email_Optin extends Tru_Fetcher_Widgets_Base
{
    protected array $config = [
        'id' => 'tru_fetcher_email_optin',
        'name' => 'Tru Fetcher Email Optin',
        'classname' => 'tru-fetcher-email-optin',
        'description' => 'A widget that displays an email optin form',
        'fields' => [
            [
                'id' => 'title',
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Subscribe to our newsletter'
            ],
        ]
    ];
}
