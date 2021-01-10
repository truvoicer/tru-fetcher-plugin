<?php
Tru_Fetcher_Class_Loader::loadClass(
    'includes/class-tru-fetcher-base.php'
);

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

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
        $this->hubspotConfig = $this->getHubspotConfig();
        $this->initialiseApiClient();
    }

    private function initialiseApiClient()
    {
        $this->hubspotApiClient = \HubSpot\Factory::createWithApiKey($this->hubspotConfig->api_key);
    }

    private function getHubspotConfig() {
        $config = parent::getConfig(self::HUBSPOT_CONFIG);
            switch (WP_APP_ENV) {
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