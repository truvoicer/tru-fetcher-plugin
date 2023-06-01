<?php

namespace TruFetcher\Includes\Api\Response\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Admin_Token_Response extends Tru_Fetcher_Api_Response
{
    public const API_RESPONSE_ERROR_CODE_PREFIX = parent::BASE_API_RESPONSE_ERROR_CODE_PREFIX . '_token';

    public array $tokens;

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param array $tokens
     */
    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

}
