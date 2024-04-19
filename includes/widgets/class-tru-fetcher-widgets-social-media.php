<?php

namespace TruFetcher\Includes\Widgets;

class Tru_Fetcher_Widgets_Social_Media extends Tru_Fetcher_Widgets_Base
{
    protected array $config = [
        'id' => 'tru_fetcher_social_media',
        'name' => 'Social Media',
        'classname' => 'tru-fetcher-social-media',
        'description' => 'Displays a list of social media links',
        'fields' => [
            [
                'id' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'default' => 'Social Media',
            ],
        ],
    ];
}
