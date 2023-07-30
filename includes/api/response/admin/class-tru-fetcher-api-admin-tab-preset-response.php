<?php

namespace TruFetcher\Includes\Api\Response\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Admin_Tab_Preset_Response extends Tru_Fetcher_Api_Response
{
    public array $tabPreset = [];

    public const API_RESPONSE_ERROR_CODE_PREFIX = parent::BASE_API_RESPONSE_ERROR_CODE_PREFIX . '_tab_preset';

    /**
     * @return array
     */
    public function getTabPreset(): array
    {
        return $this->tabPreset;
    }

    /**
     * @param array $tabPreset
     */
    public function setTabPreset(array $tabPreset): void
    {
        $this->tabPreset = $tabPreset;
    }

}
