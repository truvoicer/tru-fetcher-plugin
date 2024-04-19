<?php

namespace TruFetcher\Includes\Widgets;

use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Listings;

class Tru_Fetcher_Widgets_Listings extends Tru_Fetcher_Widgets_Base
{
    protected array $config = [
        'id' => 'tru_fetcher_listings',
        'name' => 'Listings',
        'classname' => 'tru-fetcher-listings',
        'description' => 'Displays a list of listings',
        'fields' => [
            [
                'id' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'default' => 'Listings',
            ],
            [
                'id' => 'listing',
                'label' => 'Listing',
                'type' => 'select',
                'default' => null,
            ]
        ],
    ];

    public function __construct()
    {

        $listingsHelpers = new Tru_Fetcher_Api_Helpers_Listings();
        $listings = $listingsHelpers->getListingsRepository()->findListings();
        $this->config = [
            'id' => 'tru_fetcher_listings',
            'name' => 'Listings',
            'classname' => 'tru-fetcher-listings',
            'description' => 'Displays a list of listings',
            'fields' => [
                [
                    'id' => 'title',
                    'label' => 'Title',
                    'type' => 'text',
                    'default' => 'Listings',
                ],
                [
                    'id' => 'listing',
                    'label' => 'Listing',
                    'type' => 'select',
                    'default' => null,
                    'options' => array_map(function ($listing) {
                        return [
                            'value' => $listing['id'],
                            'label' => $listing['name'],
                        ];
                    }, $listings)
                ]
            ],
        ];
        parent::__construct();
    }

    public function renderContent($args, $instance): void
    {
        $listingsHelpers = new Tru_Fetcher_Api_Helpers_Listings();
        $listing = $listingsHelpers->getListingsRepository()->findById((int)$instance['listing']);

        $content = '';
        parent::renderContent($args, $instance);
        if ( ! empty( $listing['name'] ) ) {
            $content .= "<h4>Selected listing: </h4>";
            $content .= "<ul><li>id: {$listing['id']}</li><li>Name: {$listing['name']}</li></ul>";
        } else {
            $content .= "<p>No listing selected/found</p>";
        }
        echo $content;
    }
}
