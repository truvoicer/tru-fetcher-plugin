<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Page_List_Response extends Tru_Fetcher_Api_Response
{

    public array $pageList = [];

    public function getPageList(): array
    {
        return $this->pageList;
    }

    public function setPageList(array $pageList): void
    {
        $this->pageList = $pageList;
    }

}
