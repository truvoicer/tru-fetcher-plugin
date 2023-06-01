<?php

namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Auth_Response extends Tru_Fetcher_Api_Response
{
    public array $roles;

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
