<?php

namespace TruFetcher\Includes\Widgets;

class Tru_Fetcher_Widgets_Saved_Items extends Tru_Fetcher_Widgets_Base
{
    protected array $config = [
        'id' => 'tru_fetcher_saved_items',
        'name' => 'Saved Items',
        'classname' => 'tru-fetcher-saved-items',
        'description' => 'Displays a list of saved items',
        'fields' => [
            [
                'id' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'default' => 'Saved Items',
            ],
        ],
    ];
}
