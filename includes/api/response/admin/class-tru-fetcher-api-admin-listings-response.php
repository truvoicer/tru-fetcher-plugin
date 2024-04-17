<?php

namespace TruFetcher\Includes\Api\Response\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Admin_Listings_Response extends Tru_Fetcher_Api_Response
{
    public array $listings = [];

    public const API_RESPONSE_ERROR_CODE_PREFIX = parent::BASE_API_RESPONSE_ERROR_CODE_PREFIX . '_listings';

    public function getListings(): array
    {
        return $this->listings;
    }

    public function setListings(array $listings): void
    {
        $this->listings = $listings;
    }

}
