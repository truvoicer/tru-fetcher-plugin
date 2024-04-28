<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_List_Response extends Tru_Fetcher_Api_Response
{

    public array $labels = [];
    public array $list = [];

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }

}
