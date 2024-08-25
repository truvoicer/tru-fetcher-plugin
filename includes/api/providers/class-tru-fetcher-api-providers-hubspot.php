<?php
namespace TruFetcher\Includes\Api\Providers;
/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

use TruFetcher\Includes\Tru_Fetcher_Base;
use \HubsSpot\Factory;

class Tru_Fetcher_Api_Providers_Hubspot
{

    private string $accessToken;

    private \HubSpot\Discovery\Discovery $hubspotApiClient;

    public function __construct()
    {
    }

    private function initialiseApiClient()
    {
        if (empty($this->accessToken)) {
            throw new \Exception("Access token is required to initialise Hubspot API client");
        }
        $this->hubspotApiClient = \HubSpot\Factory::createWithAccessToken($this->accessToken);
    }

    public function newContact(array $data = []) {
        $this->initialiseApiClient();
        try {
            $contactInput = new \HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput();
            $contactInput->setProperties($data);
            $this->hubspotApiClient->crm()->contacts()->basicApi()->create($contactInput);
            return true;
        } catch (\HubSpot\Client\Crm\Contacts\ApiException $e) {
            return $e->getResponseObject()->getMessage();
        }
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

}
