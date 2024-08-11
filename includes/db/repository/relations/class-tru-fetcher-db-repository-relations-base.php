<?php

namespace TruFetcher\Includes\DB\Repository\Relations;

use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Base;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

class Tru_Fetcher_DB_Repository_Relations_Base
{
    protected Tru_Fetcher_DB_Repository_Base $repoBase;

    public function __construct(Tru_Fetcher_DB_Repository_Base $base)
    {
        $this->repoBase = $base;
    }
}
