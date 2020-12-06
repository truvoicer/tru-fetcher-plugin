<?php

class Tru_Fetcher_Api_Post_Response
{
    public $post;
    public $blocks_data;
    public $site_config;

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
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