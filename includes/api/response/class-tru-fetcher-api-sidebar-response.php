<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Sidebar_Response extends Tru_Fetcher_Api_Response
{

    public array $sidebar;

    /**
     * @return array
     */
    public function getSidebar(): array
    {
        return $this->sidebar;
    }

    /**
     * @param array $sidebar
     */
    public function setSidebar(array $sidebar): void
    {
        $this->sidebar = $sidebar;
    }

}
