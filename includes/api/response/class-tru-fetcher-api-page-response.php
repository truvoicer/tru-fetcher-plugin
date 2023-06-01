<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Page_Response extends Tru_Fetcher_Api_Response
{
    public $page;

    public array $allSettings;
    public $blocks_data;
    public $site_config;

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return array
     */
    public function getAllSettings(): array
    {
        return $this->allSettings;
    }

    /**
     * @param array $allSettings
     */
    public function setAllSettings(array $allSettings): void
    {
        $this->allSettings = $allSettings;
    }

    /**
     * @return mixed
     */
    public function getBlocksData()
    {
        return $this->blocks_data;
    }

    /**
     * @param mixed $blocks_data
     */
    public function setBlocksData($blocks_data)
    {
        $this->blocks_data = $blocks_data;
    }

	/**
	 * @return mixed
	 */
	public function getSiteConfig() {
		return $this->site_config;
	}

	/**
	 * @param mixed $site_config
	 */
	public function setSiteConfig( $site_config ) {
		$this->site_config = $site_config;
	}

}
