<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_List_Response extends Tru_Fetcher_Api_Response
{

    public array $list = [];

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
    }

}
