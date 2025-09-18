<?php

namespace TruFetcher\Includes\Api\Auth;

use Carbon\Carbon;
use TruFetcher\Includes\Tru_Fetcher_Base;

class Tru_Fetcher_Api_Auth_Jwt extends Tru_Fetcher_Base
{

    public const ERROR_CODE_PREFIX = 'tr_news_app_api_jwt';

    const TOKEN = 'token';
    const ISSUED_AT = 'iat';
    const EXPIRES_AT = 'exp';
    const TOKEN_DATA_HEADER = 'header';
    const TOKEN_DATA_PAYLOAD = 'payload';
    const TOKEN_DATA_SIGNATURE = 'signature';

    private string $secret;
    private string $jwtKeyPlaceHolder = '%s_%s_%s_%s';
    private string $jwtKey;
    private array $defaultPayload;
    private array $requiredTokenDataKeys = ['header', 'payload', 'signature'];

    private const DEFAULT_HEADER = [
        "alg" => "HS256",
        "typ" => "JWT"
    ];

    public function init(string $keyType, string $keyApp, \WP_User $user)
    {
        $this->setJwtKey($keyType, $keyApp, $user);
    }

    private function encodeHeader(array $header = self::DEFAULT_HEADER) {
        return $this->base64UrlEncode(json_encode($header));
    }

    private function encodePayload(array $payload) {
        return $this->base64UrlEncode(json_encode($payload));
    }

    private function buildSignatureHash(string $encodedHeader, string $encodedPayload, string $secret) {
        return hash_hmac('sha256', $encodedHeader . "." . $encodedPayload, $secret, true);
    }

    private function encodeSignatureHash(string $signature) {
        return $this->base64UrlEncode($signature);
    }

    private function buildJwt(array $payload, string $secret, ?array $header = self::DEFAULT_HEADER) {
        $encodedHeader = $this->encodeHeader($header);
        $encodedPayload = $this->encodePayload($payload);
        $signature = $this->buildSignatureHash($encodedHeader, $encodedPayload, $secret);
        $signatureHash = $this->encodeSignatureHash($signature);

        return "{$encodedHeader}.{$encodedPayload}.{$signatureHash}";
    }

    private function buildTokenData(string $jwt) {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) {
            return new \WP_Error(
                self::ERROR_CODE_PREFIX . '_invalid_token_parts',
                'Invalid token parts'
            );
        }
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        return [
            self::TOKEN_DATA_HEADER => $header,
            self::TOKEN_DATA_PAYLOAD => $payload,
            self::TOKEN_DATA_SIGNATURE => $signatureProvided,
        ];
    }

    private function validateTokenData(array $tokenData) {
        foreach (array_keys($tokenData) as $key) {
            if (!in_array($key, $this->requiredTokenDataKeys)) {
                return new \WP_Error(
                    self::ERROR_CODE_PREFIX . '_token_data_invalid',
                    "{$key} should not exist in token data"
                );
            }
        }
        if (count($tokenData) === count($this->requiredTokenDataKeys)) {
            return true;
        }
        return new \WP_Error(
            self::ERROR_CODE_PREFIX . '_token_data_invalid',
            'Token data is invalid'
        );
    }

    private function validateSignature(array $tokenData, string $secret) {
        $validateTokenData = $this->validateTokenData($tokenData);
        if (is_wp_error($validateTokenData)) {
            return $validateTokenData;
        }
        $base64UrlHeader = $this->base64UrlEncode($tokenData[self::TOKEN_DATA_HEADER]);
        $base64UrlPayload = $this->base64UrlEncode($tokenData[self::TOKEN_DATA_PAYLOAD]);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        if ($base64UrlSignature === $tokenData[self::TOKEN_DATA_SIGNATURE]) {
            return true;
        }
        return new \WP_Error(
            self::ERROR_CODE_PREFIX . '_signature_invalid',
            'Signatire is invalid'
        );
    }

    private function buildDecodedTokenData(array $tokenData) {
        $buildData = [];
        foreach ($tokenData as $key => $value) {
            if ($key !== self::TOKEN_DATA_SIGNATURE) {
                $buildData[$key] = json_decode($value, true);
                continue;
            }
            $buildData[$key] = $value;
        }
        return $buildData;
    }

    public function jwtEncode(string $keyType, string $keyApp, \WP_User $user, array $data)
    {
        $this->setDefaultPayload();
        $this->init($keyType, $keyApp, $user);
        return $this->buildJwt(
            array_merge(
                $this->getDefaultPayload(),
                $data
            ),
            "{$this->getJwtKey()}_{$this->getSecret()}"
        );
    }

    public function jwtRawEncode(array $data)
    {
        $this->setDefaultPayload();
        return $this->buildJwt(
            $data,
            $this->getSecret()
        );
    }
    public function jwtRawDecode(string $jwt)
    {
        $tokenData = $this->buildTokenData($jwt);
        $validateSignature = $this->validateSignature($tokenData, $this->getSecret());
        if (is_wp_error($validateSignature)) {
            return $validateSignature;
        }
        return $this->buildDecodedTokenData($tokenData);
    }

    public function jwtDecode(string $keyType, string $keyApp, \WP_User $user, string $jwt)
    {
        $this->init($keyType, $keyApp, $user);
        $tokenData = $this->buildTokenData($jwt);
        $validateSignature = $this->validateSignature($tokenData, "{$this->getJwtKey()}_{$this->getSecret()}");
        if (is_wp_error($validateSignature)) {
            return $validateSignature;
        }
        return $this->buildDecodedTokenData($tokenData);
    }

    public function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    /**
     * @return array
     */
    public function getDefaultPayload(): array
    {
        return $this->defaultPayload;
    }

    /**
     * @param array $defaultPayload
     */
    public function setDefaultPayload(?array $defaultPayload = null): void
    {
        if (!$defaultPayload) {
            $this->defaultPayload = [
                self::ISSUED_AT => Carbon::now()->timestamp
            ];
            return;
        }
        $this->defaultPayload = $defaultPayload;
    }

    /**
     * @return string
     */
    public function getJwtKey(?string $keyType = null, ?string $keyApp = null, ?\WP_User $user = null): string
    {
        if ($keyType && $keyApp && $user) {
            $this->setJwtKey($keyType, $keyApp, $user);
        }
        return $this->jwtKey;
    }

    /**
     * @param string $keyType
     * @param string $keyApp
     * @param \WP_User $user
     * @return void
     */
    public function setJwtKey(string $keyType, string $keyApp, \WP_User $user): void
    {
        $this->jwtKey = sprintf(
            $this->jwtKeyPlaceHolder,
            $keyType,
            $keyApp,
            TRU_FETCHER_PLUGIN_NAME,
            $user->ID
        );
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getPayload(array $decodedToken) {
        if (!isset($decodedToken[self::TOKEN_DATA_PAYLOAD])) {
            return new \WP_Error(
                self::ERROR_CODE_PREFIX . '_payload_not_found',
                'Payload not found'
            );
        }
        return $decodedToken[self::TOKEN_DATA_PAYLOAD];
    }
}
