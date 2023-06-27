<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Settings_Response extends Tru_Fetcher_Api_Response
{

    public array $settings;

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
