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
                'default' => 'Newsletter'
            ],
            [
                'id' => 'heading',
                'type' => 'text',
                'label' => 'Heading',
                'default' => 'Get Updates'
            ],
            [
                'id' => 'description',
                'type' => 'textarea',
                'label' => 'Description',
                'default' => 'Get the latest news and updates from Tru Fetcher'
            ],
            [
                'id' => 'button_text',
                'type' => 'text',
                'label' => 'Button Text',
                'default' => 'Subscribe'
            ],
            [
                'id' => 'placeholder',
                'type' => 'text',
                'label' => 'Email Placeholder',
                'default' => 'Enter your email address'
            ],
            [
                'id' => 'endpoint_providers',
                'type' => 'multi-select',
                'label' => 'Endpoint Providers',
                'default' => null,
                'options' => [
                    [
                        'value' => 'hubspot',
                        'label' => 'Hubspot'
                    ],
                ]
            ],
            [
                'id' => 'response_type',
                'type' => 'select',
                'label' => 'Response Type',
                'default' => 'message',
                'options' => [
                    [
                        'value' => 'message',
                        'label' => 'Message'
                    ],
                    [
                        'value' => 'redirect',
                        'label' => 'Redirect'
                    ]
                ]
            ],
            [
                'id' => 'success_message',
                'type' => 'text',
                'label' => 'Success Message',
                'default' => 'Thank you for subscribing!'
            ],
            [
                'id' => 'error_message',
                'type' => 'text',
                'label' => 'Error Message',
                'default' => 'An error occurred. Please try again.'
            ],
            [
                'id' => 'redirect_url',
                'type' => 'text',
                'label' => 'Redirect URL',
                'default' => ''
            ]
        ]
    ];
}
