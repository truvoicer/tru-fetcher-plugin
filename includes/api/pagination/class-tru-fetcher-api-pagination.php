<?php

namespace TruFetcher\Includes\Api\Pagination;

class Tru_Fetcher_Api_Pagination
{
    public string $pagination_type = 'offset';
    public ?int $page_count = null;
    public ?int $page_size = null;
    public ?int $page_number = null;
    public ?int $current_per_page = null;
    public ?int $offset = null;
    public ?int $total_items = null;
    public ?int $total_pages = null;

    public function getPageNumber(): ?int
    {
        return $this->page_number;
    }

    public function setPageNumber(?int $page_number): void
    {
        $this->page_number = $page_number;
    }

    public function getPaginationType(): string
    {
        return $this->pagination_type;
    }

    public function setPaginationType(string $pagination_type): void
    {
        $this->pagination_type = $pagination_type;
    }

    /**
     * @return int|null
     */
    public function getPageCount(): ?int
    {
        return $this->page_count;
    }

    /**
     * @param int $page_count
     */
    public function setPageCount(int $page_count): void
    {
        $this->page_count = $page_count;
    }

    /**
     * @return int|null
     */
    public function getPageSize(): ?int
    {
        return $this->page_size;
    }

    /**
     * @param int $page_size
     */
    public function setPageSize(int $page_size): void
    {
        $this->page_size = $page_size;
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
    public function getTotalItems(): ?int
    {
        return $this->total_items;
    }

    /**
     * @param int|null $total_items
     */
    public function setTotalItems(?int $total_items): void
    {
        $this->total_items = $total_items;
    }

    /**
     * @return int|null
     */
    public function getCurrentPerPage(): ?int
    {
        return $this->current_per_page;
    }

    /**
     * @param int|null $current_per_page
     */
    public function setCurrentPerPage(?int $current_per_page): void
    {
        $this->current_per_page = $current_per_page;
    }

    public function getTotalPages(): ?int
    {
        return $this->total_pages;
    }

    public function setTotalPages(?int $total_pages): void
    {
        $this->total_pages = $total_pages;
    }

}
