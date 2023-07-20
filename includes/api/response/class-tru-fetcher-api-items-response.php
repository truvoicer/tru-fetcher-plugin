<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Items_Response extends Tru_Fetcher_Api_Response
{
    public array $savedItems = [];
    public array $itemRatings = [];

    /**
     * @return array
     */
    public function getSavedItems(): array
    {
        return $this->savedItems;
    }

    /**
     * @param array $savedItems
     */
    public function setSavedItems(array $savedItems): void
    {
        $this->savedItems = $savedItems;
    }

    /**
     * @return array
     */
    public function getItemRatings(): array
    {
        return $this->itemRatings;
    }

    /**
     * @param array $itemRatings
     */
    public function setItemRatings(array $itemRatings): void
    {
        $this->itemRatings = $itemRatings;
    }

}
