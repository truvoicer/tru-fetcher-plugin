<?php

namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Token_Response extends Tru_Fetcher_Api_Response
{
    public string $token;
    public int $issuedAt;
    public int $expiresAt;

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getIssuedAt(): int
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(int $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(int $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}
