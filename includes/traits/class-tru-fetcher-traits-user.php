<?php
namespace TruFetcher\Includes\Traits;

trait Tru_Fetcher_Traits_User
{
    protected ?\WP_User $user = null;

    /**
     * @return \WP_User|null
     */
    public function getUser(): ?\WP_User
    {
        return $this->user;
    }

    /**
     * @param \WP_User|null $user
     */
    public function setUser(?\WP_User $user): void
    {
        $this->user = $user;
    }


}
