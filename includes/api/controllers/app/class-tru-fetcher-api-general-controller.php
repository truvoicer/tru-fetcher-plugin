<?php

namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_General_Response;
use TruFetcher\Includes\Forms\Tru_Fetcher_Api_Form_Handler;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Locale;

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
class Tru_Fetcher_Api_General_Controller extends Tru_Fetcher_Api_Controller_Base
{

    const STATUS_SUCCESS = "success";

    private Tru_Fetcher_Api_General_Response $apiGeneralResponse;
    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;
    private Tru_Fetcher_Api_Helpers_Locale $localeHelpers;

    public function __construct()
    {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/general');
        $this->localeHelpers = new Tru_Fetcher_Api_Helpers_Locale();
    }

    public function init()
    {
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
        $this->apiGeneralResponse = new Tru_Fetcher_Api_General_Response();
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->protectedEndpoint, '/countries', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "countriesSelectData"],
            'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
        ));
    }

    public function countriesSelectData() {
        $buildSelectData = $this->apiFormHandler->buildSelectList(
            $this->localeHelpers->getLocaleModel()->getAlias(),
            $this->localeHelpers->getLocaleModel()->getIdColumn(),
            $this->localeHelpers->getLocaleModel()->getIdColumn(),
            $this->localeHelpers->getLocaleModel()->getCountryNameColumn(),
            $this->localeHelpers->findLocales()
        );
        $this->apiGeneralResponse->setData($buildSelectData);
        return $this->controllerHelpers->sendSuccessResponse(
            "Countries list successfully retrieved",
            $this->apiGeneralResponse,
        );
    }

}
