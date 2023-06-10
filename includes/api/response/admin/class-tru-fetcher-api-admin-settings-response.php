<?php

namespace TruFetcher\Includes\Api\Response\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Admin_Settings_Response extends Tru_Fetcher_Api_Response
{
    public array $settings = [];

    public const API_RESPONSE_ERROR_CODE_PREFIX = parent::BASE_API_RESPONSE_ERROR_CODE_PREFIX . '_settings';

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

}
