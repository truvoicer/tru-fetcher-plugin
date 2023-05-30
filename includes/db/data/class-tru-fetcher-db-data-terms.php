<?php

namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Category;
use TruFetcher\Includes\DB\Model\WP\Tru_Fetcher_DB_Model_WP_Term;
use TruFetcher\Includes\Taxonomies\Tru_Fetcher_Taxonomy;

class Tru_Fetcher_DB_Data_Terms extends Tru_Fetcher_DB_Data
{

    private Tru_Fetcher_Taxonomy $taxonomyManager;

    public function __construct()
    {
        $this->setWpModel(new Tru_Fetcher_DB_Model_WP_Term());
        $this->taxonomyManager = new Tru_Fetcher_Taxonomy();
    }

    private array $data = [
        [
            'name' => 'Business',
            'slug' => 'business'
        ],
        [
            'name' => 'Local',
            'slug' => 'local'
        ],
        [
            'name' => 'Headlines',
            'slug' => 'headlines'
        ],
        [
            'name' => 'Gaming',
            'slug' => 'gaming'
        ],
        [
            'name' => 'Sports',
            'slug' => 'sports'
        ],
        [
            'name' => 'Fashion',
            'slug' => 'fashion'
        ],
    ];

    public function install()
    {
        if ($this->site instanceof \WP_Site) {
            $this->taxonomyManager->setSite($this->site);
        }
        foreach ($this->data as $index => $term) {
            $createTerm = $this->taxonomyManager->createTerm(
                Tru_Fetcher_Api_Helpers_Category::TR_NEWS_APP_CATEGORY_TAXONOMY,
                $term
            );
            if (is_wp_error($createTerm)) {
                $this->errors[] = $createTerm->get_error_message();
            }
        }
        if (count($this->errors)) {
            return [
                'success' => false,
                'errors' => $this->errors
            ];
        }
        return [
            'success' => true,
        ];
    }

    public function check()
    {
        return [
            'success' => true,
        ];
    }
}
