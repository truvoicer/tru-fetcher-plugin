<?php

namespace TruFetcher\Includes\Api\Response\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Admin_Keymap_Response extends Tru_Fetcher_Api_Response
{
    public const API_RESPONSE_ERROR_CODE_PREFIX = parent::BASE_API_RESPONSE_ERROR_CODE_PREFIX . '_keymap';
    public int $serviceId;
    public array $keymaps = [];

    public function getServiceId(): int
    {
        return $this->serviceId;
    }

    public function setServiceId(int $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    public function getKeymaps(): array
    {
        return $this->keymaps;
    }

    public function setKeymaps(array $keymaps): void
    {
        $this->keymaps = $keymaps;
    }

}
