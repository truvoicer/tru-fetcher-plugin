<?php

namespace TruFetcher\Includes\Widgets;

class Tru_Fetcher_Widgets_Listings_Filter extends Tru_Fetcher_Widgets_Base
{
    protected array $config = [
        'id' => 'tru_fetcher_listings_filter',
        'name' => 'Listings Filter',
        'classname' => 'tru-fetcher-listings-filter',
        'description' => 'Listings Filter',
        'fields' => [
            [
                'id' => 'title',
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Filter Listings',
            ]
        ],
    ];
}
