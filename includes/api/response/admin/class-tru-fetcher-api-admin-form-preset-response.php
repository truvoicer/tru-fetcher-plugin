<?php

namespace TruFetcher\Includes\Api\Response\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Admin_Form_Preset_Response extends Tru_Fetcher_Api_Response
{
    public array $formPreset = [];

    public const API_RESPONSE_ERROR_CODE_PREFIX = parent::BASE_API_RESPONSE_ERROR_CODE_PREFIX . '_form_preset';

    /**
     * @return array
     */
    public function getFormPreset(): array
    {
        return $this->formPreset;
    }

    /**
     * @param array $formPreset
     */
    public function setFormPreset(array $formPreset): void
    {
        $this->formPreset = $formPreset;
    }

}
