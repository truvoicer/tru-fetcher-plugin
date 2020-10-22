<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( !class_exists('Tru_Fetcher_Request_Api') ) :

class Tru_Fetcher_Request_Api {

    const API_CONFIG_FILE = "fetcher-request-api-config";
    const ALLOWED_METHODS = [ "GET", "POST" ];

    private $apiConfig;
    private string $baseUrl;

	public function __construct() {
		$this->loadDependencies();
		$this->apiConfig = Tru_Fetcher_Base::getConfig(self::API_CONFIG_FILE);
		$this->baseUrl = $this->getBaseUrl();
	}

	public function loadDependencies() {
	}

	private function getBaseUrl() {
        $truFetcherSettings = get_fields( "option" );
        if ( isset( $truFetcherSettings["api_url"] ) && Tru_Fetcher::isNotEmpty($truFetcherSettings["api_url"])) {
            return $truFetcherSettings["api_url"];
        }
        return $this->apiConfig->baseUrl;
    }

    private function buildApiRequestUrl(string $endpoint = null) {
	    return $this->getBaseUrl() . $endpoint;
    }

    private function getApiKey() {
        $truFetcherSettings = get_fields( "option" );
        if ( isset( $truFetcherSettings["api_key"] ) && Tru_Fetcher::isNotEmpty($truFetcherSettings["api_key"])) {
            return $truFetcherSettings["api_key"];
        }
        return $this->apiConfig->apiKey;
    }

    private function getHeaders() {
        return [
            'Accept'     => 'application/json',
            "Authorization" => sprintf("Bearer %s", $this->getApiKey())
        ];
    }

	public function sendApiRequest(string $endpoint = null, string $method = null, array $data = []) {
	    if (!Tru_Fetcher::isNotEmpty($endpoint)) {
	        return new WP_Error("invalid_api_request_error", "Endpoint has not been set." );
        }
	    if (!Tru_Fetcher::isNotEmpty($method)) {
	        return new WP_Error("invalid_api_request_error", "Method has not been set." );
        }
	    if (!in_array(strtoupper($method), self::ALLOWED_METHODS)) {
            return new WP_Error("invalid_api_request_error", "Api request method not allowed." );
        }
	    var_dump($endpoint, $method, $this->baseUrl, $this->getApiKey(), $this->getHeaders());
        $client = new GuzzleHttp\Client(['base_uri' => $this->baseUrl]);
        try {
            $request = $client->request(strtoupper($method), $endpoint, [
                'headers' => $this->getHeaders()
            ]);
            $statusCode = $request->getStatusCode();
            $body = $request->getBody();
            $contents = $body->getContents();
            var_dump($statusCode, $body, $contents);
            return true;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return new WP_Error("api_request_guzzle_error", $e->getMessage());
        }
    }

}
endif;