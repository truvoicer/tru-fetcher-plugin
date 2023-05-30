<?php

namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine_Base;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Model\WP\Tru_Fetcher_DB_Model_WP;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;

abstract class Tru_Fetcher_DB_Data
{
    use Tru_Fetcher_DB_Traits_WP_Site;

    protected array $errors = [];

    abstract public function install();

    protected Tru_Fetcher_DB_Model $model;

    protected Tru_Fetcher_DB_Model_WP $wpModel;

    protected function doesTableExist(): bool
    {
        if (!isset($this->model)) {
            return false;
        }
        $getTables = (new Tru_Fetcher_DB_Engine_Base())->getDbTables([$this->model]);
        if (is_array($getTables) && count($getTables)) {
            return true;
        }
        return false;
    }

    /**
     * @param Tru_Fetcher_DB_Model $model
     */
    public function setModel(Tru_Fetcher_DB_Model $model): void
    {
        $this->model = $model;
    }

    /**
     * @return Tru_Fetcher_DB_Model|null
     */
    public function getModel(): ?Tru_Fetcher_DB_Model
    {
        if (isset($this->model)) {
            return $this->model;
        }
        return null;
    }

    /**
     * @return Tru_Fetcher_DB_Model_WP|null
     */
    public function getWpModel(): ?Tru_Fetcher_DB_Model_WP
    {
        if (isset($this->wpModel)) {
            return $this->wpModel;
        }
        return null;
    }

    public function getEntityName() {
        $entityName = null;
        $model = $this->getModel();
        $wpModel = $this->getWpModel();
        if ($model instanceof Tru_Fetcher_DB_Model) {
            $entityName = $model->getTableName($this->site, $this->isNetworkWide);
        } elseif ($wpModel instanceof Tru_Fetcher_DB_Model_WP) {
            $entityName = $wpModel->getAlias();
        }
        return $entityName;
    }

    /**
     * @param Tru_Fetcher_DB_Model_WP $wpModel
     */
    public function setWpModel(Tru_Fetcher_DB_Model_WP $wpModel): void
    {
        $this->wpModel = $wpModel;
    }



    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
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

}
