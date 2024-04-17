<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Listings_Response;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Listings;

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
class Tru_Fetcher_Api_Admin_Listings_Controller extends Tru_Fetcher_Api_Admin_Base_Controller {

    private Tru_Fetcher_Api_Admin_Listings_Response $listingsResponse;
    private Tru_Fetcher_Api_Helpers_Listings $listingsHelpers;

	public function __construct() {
        parent::__construct();
        $this->listingsHelpers = new Tru_Fetcher_Api_Helpers_Listings();
        $this->listingsResponse = new Tru_Fetcher_Api_Admin_Listings_Response();
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	public function register_routes() {
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/listings', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchListings" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/listings/(?<id>[\d]+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchSingleListing" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/listings/create', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createListing" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/listings/(?<id>[\d]+)/update', array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => [ $this, "updateListing" ],
            'permission_callback' => [$this->apiAuth, 'allowRequest'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/listings/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteListing" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
	}

    public function fetchListings(\WP_REST_Request $request) {
        $this->listingsResponse->setListings(
            $this->listingsHelpers->getListings()
        );
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched listings",
            $this->listingsResponse
        );
    }
    public function fetchSingleListing(\WP_REST_Request $request) {
        $id = $request->get_param('id');
        if (empty($id)) {
            return $this->controllerHelpers->sendErrorResponse(
                "listings_error",
                "No name provided",
                $this->listingsResponse
            );
        }
        $listing = $this->listingsHelpers->getListingById($id);
        if (!$listing) {
            return $this->controllerHelpers->sendErrorResponse(
                "listings_error",
                "No listing found",
                $this->listingsResponse
            );
        }
        $this->listingsResponse->setListings($listing);
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched settings",
            $this->listingsResponse
        );
    }

    public function createListing(\WP_REST_Request $request) {
        if (!$this->listingsHelpers->createListingFromRequest($request)) {
            $this->listingsResponse->setErrors($this->listingsHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "listings_error",
                "Failed to create listing",
                $this->listingsResponse
            );
        }
        return $this->fetchListings($request);
    }

    public function updateListing(\WP_REST_Request $request) {
        if (!$this->listingsHelpers->updateListingFromRequest($request)) {
            $this->listingsResponse->setErrors($this->listingsHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "listings_error",
                "Failed to update listing",
                $this->listingsResponse
            );
        }
        return $this->fetchListings($request);
    }

    public function deleteListing(\WP_REST_Request $request) {
        if (!$this->listingsHelpers->deleteListing($request)) {
            $this->listingsResponse->setErrors($this->listingsHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "listings_error",
                "Failed to delete listing",
                $this->listingsResponse
            );
        }
        return $this->fetchListings($request);
    }
}
