<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Post_List_Response extends Tru_Fetcher_Api_Response
{

    public array $postList = [];

    public function getPostList(): array
    {
        return $this->postList;
    }

    public function setPostList(array $postList): void
    {
        $this->postList = $postList;
    }

}
