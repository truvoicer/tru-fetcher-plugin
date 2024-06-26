<?php
namespace TruFetcher\Includes\Api;

// exit if accessed directly
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Setting;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Tru_Fetcher;
use TruFetcher\Includes\Tru_Fetcher_Base;

if (!defined('ABSPATH')) exit;

class Tru_Fetcher_Api_Request extends Tru_Fetcher_Base
{
    use Tru_Fetcher_Traits_Errors;

    const RESPONSE_FORMAT_ARRAY = "array";
    const RESPONSE_FORMAT_OBJECT = "object";
    const API_CONFIG_FILE = "fetcher-request-api-config";
    const ALLOWED_METHODS = ["GET", "POST"];

    private Tru_Fetcher_Api_Helpers_Setting $settingHelper;
    private string $responseFormat = 'object';
    private $apiConfig;
    private string $baseUrl;

    private string $token;

    public function __construct()
    {
        parent::__construct();
        $this->loadDependencies();
        $this->settingHelper = new Tru_Fetcher_Api_Helpers_Setting();
        $this->apiConfig = Tru_Fetcher_Base::getConfig(self::API_CONFIG_FILE);
        $this->baseUrl = $this->getBaseUrl();
    }

    public function loadDependencies()
    {
    }

    private function getBaseUrl()
    {
        $docker = $this->settingHelper->getSetting('docker');
        if ($docker === '1' || $docker === 'true' || $docker === true) {
            return $this->settingHelper->getSetting('docker_api_url');
        }
        return $this->settingHelper->getSetting('api_url');
    }

    private function buildApiRequestUrl(string $endpoint = null)
    {
        return $this->getBaseUrl() . $endpoint;
    }

    private function getApiKey()
    {
        return $this->apiConfig->apiKey;
    }

    private function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            "Authorization" => sprintf("Bearer %s", $this->settingHelper->getSetting('admin_api_token'))
        ];
    }

    public function sendApiRequest(string $endpoint = null, string $method = null, array $data = [])
    {
        if (!Tru_Fetcher::isNotEmpty($endpoint)) {
            $this->addError(
                new \WP_Error("invalid_api_request_error", "Endpoint has not been set.")
            );
            return false;
        }
        if (!Tru_Fetcher::isNotEmpty($method)) {
            $this->addError(
                new \WP_Error("invalid_api_request_error", "Method has not been set.")
            );
            return false;
        }
        if (!in_array(strtoupper($method), self::ALLOWED_METHODS)) {
            $this->addError(
                new \WP_Error("invalid_api_request_error", "Api request method not allowed.")
            );
            return false;
        }
        $requestData = [
            'headers' => $this->getHeaders()
        ];
        if (strtoupper($method) === "GET") {
            $requestData["query"] = $data;
        } else if (strtoupper($method) === "POST") {
            $requestData["form_params"] = $data;
        }
        $client = new \GuzzleHttp\Client(['base_uri' => $this->baseUrl]);
        try {
            $response = $client->request(strtoupper($method), $endpoint, $requestData);
            return $this->responseHandler($response);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $this->addError(
                new \WP_Error("api_request_guzzle_error", $e->getMessage())
            );
            return false;
        }
    }

    private function isResponseFormatAssoc(): bool
    {
        return $this->responseFormat === self::RESPONSE_FORMAT_ARRAY;
    }

    private function responseHandler(\GuzzleHttp\Psr7\Response $response)
    {
        switch ($response->getStatusCode()) {
            case 200:
                return json_decode($response->getBody()->getContents(), $this->isResponseFormatAssoc());
            default:
                $this->addError(
                    new \WP_Error(
                        "api_response_error",
                        "Error from Api",
                        json_decode($response->getBody()->getContents(), $this->isResponseFormatAssoc())
                    )
                );
                return false;
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

        if (!$getData) {
            $this->addError(
                new \WP_Error("api_response_error", "Error from Api", $getData)
            );
            return false;
        }
        if ($getData->status !== "success") {
            $this->addError(
                new \WP_Error("api_response_error", "Error from Api", $getData->data)
            );
            return false;
        }
        return $getData->data;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function setResponseFormat(string $responseFormat): void
    {
        $this->responseFormat = $responseFormat;
    }

}

