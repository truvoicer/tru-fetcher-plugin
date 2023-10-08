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

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Api_Providers_Hubspot extends Tru_Fetcher_Base
{
    const HUBSPOT_CONFIG = "hubspot-config";

    private $hubspotConfig;
    private \HubSpot\Discovery\Discovery $hubspotApiClient;

    public function __construct()
    {
        parent::__construct();
        $this->hubspotConfig = $this->getHubspotConfig();
        $this->initialiseApiClient();
    }

    private function initialiseApiClient()
    {
        $this->hubspotApiClient = \HubSpot\Factory::createWithAccessToken($this->hubspotConfig->api_key);
    }

    private function getHubspotConfig() {
        $config = parent::getConfig(self::HUBSPOT_CONFIG);
            switch ($this->getAppEnv()) {
                case "dev":
                    return $config->hubspot_sdk->dev;
                case "prod":
                    return $config->hubspot_sdk->prod;
                default:
                    return false;
            }
    }

    public function newContact(array $data = []) {
        try {
            $contactInput = new \HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput();
            $contactInput->setProperties($data);
            $this->hubspotApiClient->crm()->contacts()->basicApi()->create($contactInput);
            return true;
        } catch (\HubSpot\Client\Crm\Contacts\ApiException $e) {
            return $e->getResponseObject()->getMessage();
        }
    }
}
