<?php
namespace TruFetcher\Includes\Api\Config;

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
class Tru_Fetcher_Api_Config_Endpoints {
    public const ENDPOINT_USER_UPDATE = "ENDPOINT_USER_UPDATE";
    public const ENDPOINT_USER_PROFILE = "ENDPOINT_USER_PROFILE";
    public const ENDPOINT_USER_META = "ENDPOINT_USER_META";
    public const ENDPOINT_EMAIL = "ENDPOINT_EMAIL";
    public const ENDPOINT_REDIRECT = "ENDPOINT_REDIRECT";
    public const ENDPOINTS = [
        self::ENDPOINT_USER_UPDATE => '/users/update',
        self::ENDPOINT_USER_PROFILE => '/user/profile/update',
        self::ENDPOINT_EMAIL => '/forms/email',
        self::ENDPOINT_USER_META => '/forms/user/metadata/save',
        self::ENDPOINT_REDIRECT => '/forms/redirect',
    ];

    public string $publicNamespace = "tru-fetcher-api/public";
    public string $protectedNamespace = "tru-fetcher-api/protected";
    public string $adminNamespace = "tru-fetcher-api/admin";
    public ?string $namespace = null;
    public string $publicEndpoint;
    public string $protectedEndpoint;

    public function endpointsInit(string $namespace) {
        $this->namespace = $namespace;
        $this->publicEndpoint = $this->publicNamespace . $this->namespace;
        $this->protectedEndpoint = $this->protectedNamespace . $this->namespace;
    }

    /**
     * @return string
     */
    public function getPublicNamespace(): string
    {
        return $this->publicNamespace;
    }

    /**
     * @return string
     */
    public function getProtectedNamespace(): string
    {
        return $this->protectedNamespace;
    }

    /**
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getPublicEndpoint(): string
    {
        return $this->publicEndpoint;
    }

    /**
     * @return string
     */
    public function getProtectedEndpoint(): string
    {
        return $this->protectedEndpoint;
    }

    /**
     * @return string
     */
    public function getAdminNamespace(): string
    {
        return $this->adminNamespace;
    }

    public function buildEndpoint(string $endpoint): ?string {
        if (empty(self::ENDPOINTS[$endpoint])) {
           return $endpoint;
        }
        return self::ENDPOINTS[$endpoint];
    }
}
