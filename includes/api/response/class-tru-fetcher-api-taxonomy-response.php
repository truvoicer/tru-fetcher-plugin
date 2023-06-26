<?php
namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Taxonomy_Response extends Tru_Fetcher_Api_Response
{
    public array $terms = [];

    /**
     * @return array
     */
    public function getTerms(): array
    {
        return $this->terms;
    }

    /**
     * @param array $terms
     */
    public function setTerms(array $terms): void
    {
        $this->terms = $terms;
    }

}
