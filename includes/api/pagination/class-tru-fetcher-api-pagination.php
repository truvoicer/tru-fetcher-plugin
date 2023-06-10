<?php

namespace TruFetcher\Includes\Api\Pagination;

use TrNewsApp\Includes\Api\Pagination\Tr_News_App_Api_Pagination_Posts;

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

//    /**
//     * @return array
//     */
//    public function getStats(): array
//    {
//        return $this->stats;
//    }

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
        return count($this->posts);
    }

    /**
     * @param int|null $currentPerPage
     */
    public function setCurrentPerPage(?int $currentPerPage): void
    {
        $this->currentPerPage = $currentPerPage;
    }

//    /**
//     * @param array $stats
//     */
//    public function setStats(): void
//    {
//        $this->stats['offset'] = $this->getOffset();
//        $this->stats['perPage'] = $this->getPerPage();
//        $this->stats['currentPerPage'] = $this->getCurrentPerPage();
//        $this->stats['maxPages'] = $this->getMaxPages();
//        $this->stats['total'] = $this->getTotal();
//    }

    public function getPagination(\WP_Query $postsQuery, \WP_Query $totalPostsQuery, int $offset, int $perPage) {
        $total = $totalPostsQuery->post_count;
        $offset = $offset + $perPage + 1;
        if ($offset > $total) {
            $offset = $total;
        }
        $this->setMaxPages($postsQuery->max_num_pages);
        $this->setOffset($offset);
        $this->setPerPage($perPage);
        $this->setTotal($total);
        $this->setCurrentPerPage(count($postsQuery->get_posts()));
        return $this;
    }
}
