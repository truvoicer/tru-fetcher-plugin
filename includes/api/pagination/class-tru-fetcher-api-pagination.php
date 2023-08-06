<?php

namespace TruFetcher\Includes\Api\Pagination;

class Tru_Fetcher_Api_Pagination
{
    public ?int $maxPages = null;
    public ?int $perPage = null;
    public ?int $currentPerPage = null;
    public ?int $offset = null;
    public ?int $total = null;

    /**
     * @return int|null
     */
    public function getMaxPages(): ?int
    {
        return $this->maxPages;
    }

    /**
     * @param int $maxPages
     */
    public function setMaxPages(int $maxPages): void
    {
        $this->maxPages = $maxPages;
    }

    /**
     * @return int|null
     */
    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * @param int|null $total
     */
    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    /**
     * @return int|null
     */
    public function getCurrentPerPage(): ?int
    {
        return $this->currentPerPage;
    }

    /**
     * @param int|null $currentPerPage
     */
    public function setCurrentPerPage(?int $currentPerPage): void
    {
        $this->currentPerPage = $currentPerPage;
    }
}
