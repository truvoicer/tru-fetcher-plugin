<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Page_Response extends Tru_Fetcher_Api_Response
{
    public ?\WP_Post $page;
    public ?array $pageOptions;

    /**
     * @return \WP_Post|null
     */
    public function getPage(): ?\WP_Post
    {
        return $this->page;
    }

    /**
     * @param \WP_Post|null $page
     */
    public function setPage(?\WP_Post $page): void
    {
        $this->page = $page;
    }

    /**
     * @return array|null
     */
    public function getPageOptions(): ?array
    {
        return $this->pageOptions;
    }

    /**
     * @param array|null $pageOptions
     */
    public function setPageOptions(?array $pageOptions): void
    {
        $this->pageOptions = $pageOptions;
    }

}
