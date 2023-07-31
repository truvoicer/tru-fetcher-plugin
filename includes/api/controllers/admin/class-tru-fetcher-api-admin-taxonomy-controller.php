<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Base_Controller;
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
class Tru_Fetcher_Api_Admin_Taxonomy_Controller extends Tru_Fetcher_Api_Admin_Base_Controller {

    private Tru_Fetcher_Taxonomy $trNewsAppTaxonomy;
    private Tru_Fetcher_Api_Taxonomy_Response $apiTaxonomyResponse;

	public function __construct() {
        parent::__construct();
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
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/taxonomy/(?<taxonomy>[\w-]+)/terms', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, "getTerms" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/taxonomy/(?<taxonomy>[\w-]+)/term/create', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createTerm" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/taxonomy/(?<taxonomy>[\w-]+)/term/update', array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => [ $this, "updateTerm" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/taxonomy/(?<taxonomy>[\w-]+)/terms/save', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "saveTerms" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/taxonomy/(?<taxonomy>[\w-]+)/term/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteTerm" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
	}

    public function getTerms(\WP_REST_Request $request) {
        $getTerms = $this->trNewsAppTaxonomy->getTerms($request->get_param("taxonomy"));
        if ($this->trNewsAppTaxonomy->hasErrors()) {
            $this->apiTaxonomyResponse->setErrors($this->trNewsAppTaxonomy->getErrors());
        }
        if (is_array($getTerms)) {
            $this->apiTaxonomyResponse->setTerms($getTerms);
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Taxonomy fetch success",
            $this->apiTaxonomyResponse
        );
    }

    public function saveTerms(\WP_REST_Request $request) {
		$taxonomy = $request->get_param("taxonomy");
        $termData = $this->trNewsAppTaxonomy->getTermsRequestData($request);
		$this->trNewsAppTaxonomy->saveTerms($termData, $taxonomy);
        if ($this->trNewsAppTaxonomy->hasErrors()) {
            $this->apiTaxonomyResponse->setErrors($this->trNewsAppTaxonomy->getErrors());
        }
		return $this->getTerms($request);
    }

    public function createTerm(\WP_REST_Request $request) {
        $createTerm = $this->trNewsAppTaxonomy->createTermFromRequest($request);
		return $this->getTerms($request);
    }

    public function updateTerm(\WP_REST_Request $request) {
        $updateTerm = $this->trNewsAppTaxonomy->updateTermFromRequest($request);
		return $this->getTerms($request);
    }

    public function deleteTerm(\WP_REST_Request $request) {
        $deleteTerm = $this->trNewsAppTaxonomy->deleteTermBatch(
            $request->get_param("taxonomy"),
            $request->get_param('terms')
        );
		return $this->getTerms($request);
    }

}
