<?php
namespace TruFetcher\Includes\Forms;
use TruFetcher\Includes\Forms\ProgressGroups\Tru_Fetcher_Progress_Field_Groups;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Locale;
use TruFetcher\Includes\Tru_Fetcher_Filters;

class Tru_Fetcher_Forms_Helpers
{

    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;
    private Tru_Fetcher_Api_Helpers_Locale $localeHelpers;

    public function __construct()
    {
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
        $this->localeHelpers = new Tru_Fetcher_Api_Helpers_Locale();
    }

    public function init() {

    }


}
