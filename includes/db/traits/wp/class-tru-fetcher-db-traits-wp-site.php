<?php
namespace TruFetcher\Includes\DB\Traits\WP;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;

trait Tru_Fetcher_DB_Traits_WP_Site
{
    protected bool $isNetworkWide = false;
    protected bool $isMultiSite = false;
    protected ?\WP_Site $site = null;

    public function initialise() {
        $this->setIsMultiSite(is_multisite());
        $this->setIsNetworkWide(is_network_admin());
        if ($this->isMultiSite()) {
            $this->setSite(get_site());
        }
    }

    protected function buildInsertDataForSite(Tru_Fetcher_DB_Model $model, ?array $insertData = []) {
        if (!$this->isMultiSite) {
            return $insertData;
        }
        if (!method_exists($model, 'getBlogIdColumn')) {
            return false;
        }
        if ($this->site instanceof \WP_Site) {
            $insertData[$model->getBlogIdColumn()] = $this->site->blog_id;
        }
        return $insertData;
    }
    protected function buildWhereDataForSite(Tru_Fetcher_DB_Model $model, ?string $whereQuery = null, ?array $whereData = []) {
        if (!$this->isMultiSite) {
            return [
                'where_query' => $whereQuery,
                'where_data'  => $whereData
            ];
        }
        if (!isset($this->site)) {
            return [
                'where_query' => $whereQuery,
                'where_data'  => $whereData
            ];
        }
        if (!method_exists($model, 'getBlogIdColumn')) {
            return false;
        }
        if (empty($whereQuery)) {
            $query = '';
        } else {
            $query = "{$whereQuery} AND ";
        }
        if ($this->site instanceof \WP_Site) {
            $query .= "{$model->getBlogIdColumn()} = %d";
            $whereData[] = $this->site->blog_id;
        }
        return [
            'where_query' => $query,
            'where_data'  => $whereData
        ];
    }

    /**
     * @return bool
     */
    public function isMultiSite(): bool
    {
        return $this->isMultiSite;
    }

    /**
     * @param bool $isMultiSite
     */
    public function setIsMultiSite(bool $isMultiSite): void
    {
        $this->isMultiSite = $isMultiSite;
    }

    /**
     * @return \WP_Site|null
     */
    public function getSite(): ?\WP_Site
    {
        return $this->site;
    }

    /**
     * @param \WP_Site|null $site
     */
    public function setSite(?\WP_Site $site): void
    {
        $this->site = $site;
    }

    /**
     * @return bool
     */
    public function isNetworkWide(): bool
    {
        return $this->isNetworkWide;
    }

    /**
     * @param bool $isNetworkWide
     */
    public function setIsNetworkWide(bool $isNetworkWide): void
    {
        $this->isNetworkWide = $isNetworkWide;
    }

}
