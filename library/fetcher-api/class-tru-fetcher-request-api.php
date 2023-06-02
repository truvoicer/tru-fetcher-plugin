<?php

// exit if accessed directly
use TruFetcher\Includes\Tru_Fetcher;
use TruFetcher\Includes\Tru_Fetcher_Base;

if (!defined('ABSPATH')) exit;

// check if class already exists
if (!class_exists('Tru_Fetcher_Request_Api')) :

    class Tru_Fetcher_Request_Api
    {

        const API_CONFIG_FILE = "fetcher-request-api-config";
        const ALLOWED_METHODS = ["GET", "POST"];

        private $apiConfig;
        private string $baseUrl;

        public function __construct()
        {
            $this->loadDependencies();
            $this->apiConfig = Tru_Fetcher_Base::getConfig(self::API_CONFIG_FILE);
            $this->baseUrl = $this->getBaseUrl();
        }

        public function loadDependencies()
        {
        }

        private function getBaseUrl()
        {
            $truFetcherSettings = \get_fields("option");
            if (isset($truFetcherSettings["api_url"]) && Tru_Fetcher::isNotEmpty($truFetcherSettings["api_url"])) {
                return $truFetcherSettings["api_url"];
            }
            return $this->apiConfig->baseUrl;
        }

        private function buildApiRequestUrl(string $endpoint = null)
        {
            return $this->getBaseUrl() . $endpoint;
        }

        private function getApiKey()
        {
            $truFetcherSettings = \get_fields("option");
            if (isset($truFetcherSettings["api_key"]) && Tru_Fetcher::isNotEmpty($truFetcherSettings["api_key"])) {
                return $truFetcherSettings["api_key"];
            }
            return $this->apiConfig->apiKey;
        }

        private function getHeaders()
        {
            return [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                "Authorization" => sprintf("Bearer %s", $this->getApiKey())
            ];
        }

        public function sendApiRequest(string $endpoint = null, string $method = null, array $data = [])
        {
            if (!Tru_Fetcher::isNotEmpty($endpoint)) {
                return new WP_Error("invalid_api_request_error", "Endpoint has not been set.");
            }
            if (!Tru_Fetcher::isNotEmpty($method)) {
                return new WP_Error("invalid_api_request_error", "Method has not been set.");
            }
            if (!in_array(strtoupper($method), self::ALLOWED_METHODS)) {
                return new WP_Error("invalid_api_request_error", "Api request method not allowed.");
            }
            $requestData = [
                'headers' => $this->getHeaders()
            ];
            if (strtoupper($method) === "GET") {
                $requestData["query"] = $data;
            } else if (strtoupper($method) === "POST") {
                $requestData["form_params"] = $data;
            }
            $client = new GuzzleHttp\Client(['base_uri' => $this->baseUrl]);
            try {
                $response = $client->request(strtoupper($method), $endpoint, $requestData);
                return $this->responseHandler($response);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return new WP_Error("api_request_guzzle_error", $e->getMessage());
            }
        }

        private function responseHandler(GuzzleHttp\Psr7\Response $response)
        {
            switch ($response->getStatusCode()) {
                case 200:
                    return json_decode($response->getBody()->getContents());
                default:
                    return new WP_Error(
                        "api_response_error",
                        "Error from Api",
                        json_decode($response->getBody()->getContents())
                    );
            }
        }

        public function getApiDataList(string $endpoint = null, array $args = [], array $requestData = []) {
            if ($endpoint === null) {
                return false;
            }
            if (!isset($this->apiConfig->endpoints->$endpoint)) {
                return false;
            }
            $getData = $this->sendApiRequest(sprintf($this->apiConfig->endpoints->$endpoint, ...$args), "GET", $requestData);
            if (is_wp_error($getData)) {
                return $getData;
            }
            if ($getData->status !== "success") {
                return new WP_Error("api_response_error", "Error from Api", $getData->data);
            }
            return $getData->data;
        }
    }
endif;
