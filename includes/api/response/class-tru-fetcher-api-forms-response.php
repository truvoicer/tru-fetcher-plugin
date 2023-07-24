<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Forms_Response extends Tru_Fetcher_Api_Response
{
   public array $metaData = [];

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * @param array $metaData
     */
    public function setMetaData(array $metaData): void
    {
        $this->metaData = $metaData;
    }

}
