<?php

namespace TruFetcher\Includes\Widgets;

class Tru_Fetcher_Widgets_Button extends Tru_Fetcher_Widgets_Base
{
    protected array $config = [
        'id' => 'tru_fetcher_button',
        'name' => 'Button',
        'classname' => 'tru-fetcher-button',
        'description' => 'Displays a button',
        'fields' => [
            [
                'id' => 'text',
                'label' => 'Button Text',
                'type' => 'text',
                'default' => 'Button',
            ],
        ]
    ];
}
