<?php
namespace TruFetcher\Includes\Providers\Facebook;


use TruFetcher\Includes\Api\Tru_Fetcher_Api_Request;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Setting;

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
class Tru_Fetcher_Providers_Facebook
{
    const FIELD_ID = 'id';
    const FIELD_NAME = 'name';
    const FIELD_EMAIL = 'email';
    const FIELD_PICTURE = 'picture';
    const FIELD_FIRST_NAME = 'first_name';
    const FIELD_LAST_NAME = 'last_name';

    const DEFAULT_FIELDS = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PICTURE,
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
    ];
    private Tru_Fetcher_Api_Request $apiRequest;
    private Tru_Fetcher_Api_Helpers_Setting $settingsHelpers;

    private string $accessToken;
    private string $appId;
    private string $appSecret;
    private string $graphVersion;


    private array $endpoints = [
        "me" => "https://graph.facebook.com/v19.0/me",
    ];
    public function __construct()
    {
        $this->apiRequest = new Tru_Fetcher_Api_Request();
        $this->apiRequest->setResponseFormat($this->apiRequest::RESPONSE_FORMAT_ARRAY);
        $this->settingsHelpers = new Tru_Fetcher_Api_Helpers_Setting();
        return $this->initFacebookSettings();
    }

    public function initFacebookSettings()
    {
        $appId = $this->settingsHelpers->getSetting('facebook_app_id');
        $appSecret = $this->settingsHelpers->getSetting('facebook_app_secret');
        $graphVersion = $this->settingsHelpers->getSetting('facebook_graph_version');
        if (empty($appId)) {
            return new \WP_Error('auth_error', __('Facebook app id not found.', 'jwt-auth'));
        }
        if (empty($appSecret)) {
            return new \WP_Error('auth_error', __('Facebook app secret not found.', 'jwt-auth'));
        }
        if (empty($graphVersion)) {
            return new \WP_Error('auth_error', __('Facebook graph version not found.', 'jwt-auth'));
        }
        $this->setAppId($appId);
        $this->setAppSecret($appSecret);
        $this->setGraphVersion($graphVersion);
        return true;
    }

    private function buildFields(?array $fields = []): string
    {
        $fields = array_merge(self::DEFAULT_FIELDS, $fields);
        return implode(',', array_unique($fields));
    }

    public function getFacebookUser(?array $fields = [])
    {
        $url = $this->endpoints["me"];

        return $this->apiRequest->sendApiRequest(
            $url,
            "GET",
            [
                "fields" => $this->buildFields($fields),
                "access_token" => $this->accessToken,
            ]
        );
    }

    public function getApiRequest(): Tru_Fetcher_Api_Request
    {
        return $this->apiRequest;
    }

    public function setApiRequest(Tru_Fetcher_Api_Request $apiRequest): void
    {
        $this->apiRequest = $apiRequest;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    public function getGraphVersion(): string
    {
        return $this->graphVersion;
    }

    public function setGraphVersion(string $graphVersion): void
    {
        $this->graphVersion = $graphVersion;
    }

    public function getEndpoints(): array
    {
        return $this->endpoints;
    }

    public function setEndpoints(array $endpoints): void
    {
        $this->endpoints = $endpoints;
    }

}
