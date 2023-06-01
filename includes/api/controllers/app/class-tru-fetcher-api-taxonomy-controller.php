<?php

namespace TruFetcher\Includes\Api\Controllers\App;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Taxonomy_Response;
use TruFetcher\Includes\Taxonomies\Tru_Fetcher_Taxonomy;

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
class Tru_Fetcher_Api_Taxonomy_Controller extends Tru_Fetcher_Api_Controller_Base {

	private string $publicEndpoint;
	private string $protectedEndpoint;
    private Tru_Fetcher_Taxonomy $trNewsAppTaxonomy;
    private Tru_Fetcher_Api_Taxonomy_Response $apiTaxonomyResponse;

	public function __construct() {
        parent::__construct();
	    $this->publicEndpoint = $this->publicNamespace;
	    $this->protectedEndpoint = $this->protectedNamespace;
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	private function load_dependencies() {}

	private function loadResponseObjects() {
        $this->apiTaxonomyResponse = new Tru_Fetcher_Api_Taxonomy_Response();
        $this->trNewsAppTaxonomy = new Tru_Fetcher_Taxonomy();
	}

	public function register_routes() {
		register_rest_route( $this->publicEndpoint, '/taxonomy/(?<taxonomy>[\w-]+)/terms', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, "getTerms" ],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
		) );
		register_rest_route( $this->publicEndpoint, '/taxonomy/(?<taxonomy>[\w-]+)/term/create', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createTerm" ],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
		) );
		register_rest_route( $this->publicEndpoint, '/taxonomy/(?<taxonomy>[\w-]+)/term/update', array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => [ $this, "updateTerm" ],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
		) );
		register_rest_route( $this->publicEndpoint, '/taxonomy/(?<taxonomy>[\w-]+)/term/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteTerm" ],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
		) );
	}

    public function getTerms(\WP_REST_Request $request) {
        $getTerms = $this->trNewsAppTaxonomy->getTerms($request->get_param("taxonomy"));
        if(is_wp_error($getTerms)) {
            return $this->sendWpErrorResponse($getTerms, $this->apiTaxonomyResponse);
        }
        $this->apiTaxonomyResponse->setTerms($getTerms);
        return $this->controllerHelpers->sendSuccessResponse(
            "Taxonomy fetch success",
            $this->apiTaxonomyResponse
        );
    }

    public function createTerm(\WP_REST_Request $request) {
        $createTerm = $this->trNewsAppTaxonomy->createTerm(
            $request->get_param("taxonomy"),
            $request->get_param('term')
        );
        if (is_wp_error($createTerm)) {
            return $this->sendWpErrorResponse($createTerm, $this->apiTaxonomyResponse);
        }
        $this->apiTaxonomyResponse->setTerms($createTerm);
        return $this->controllerHelpers->sendSuccessResponse(
            "Taxonomy create success",
            $this->apiTaxonomyResponse
        );
    }

    public function updateTerm(\WP_REST_Request $request) {
        $updateTerm = $this->trNewsAppTaxonomy->updateTerm(
            $request->get_param("taxonomy"),
            $request->get_param('term_id')
        );
        if (is_wp_error($updateTerm)) {
            return $this->sendWpErrorResponse($updateTerm, $this->apiTaxonomyResponse);
        }
        $this->apiTaxonomyResponse->setTerms($updateTerm);
        return $this->controllerHelpers->sendSuccessResponse(
            "Taxonomy update success",
            $this->apiTaxonomyResponse
        );
    }

    public function deleteTerm(\WP_REST_Request $request) {
        $deleteTerm = $this->trNewsAppTaxonomy->deleteTerm(
            $request->get_param("taxonomy"),
            $request->get_param('term_id')
        );
        if (is_wp_error($deleteTerm)) {
            return $this->sendWpErrorResponse($deleteTerm, $this->apiTaxonomyResponse);
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Taxonomy delete success",
            $this->apiTaxonomyResponse
        );
    }

}
