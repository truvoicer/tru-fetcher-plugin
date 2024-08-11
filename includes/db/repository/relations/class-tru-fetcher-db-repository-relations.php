<?php

namespace TruFetcher\Includes\DB\Repository\Relations;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Base;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

class Tru_Fetcher_DB_Repository_Relations
{
    private Tru_Fetcher_DB_Repository_Relations_Sync $sync;

    public function __construct(Tru_Fetcher_DB_Repository_Base $base)
    {
        $this->sync = new Tru_Fetcher_DB_Repository_Relations_Sync($base);
    }

    public function getSync(): Tru_Fetcher_DB_Repository_Relations_Sync
    {
        return $this->sync;
    }

}
