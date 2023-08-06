<?php

namespace TruFetcher\Includes\Api\Response;

class Tru_Fetcher_Api_Form_Progress_Response extends Tru_Fetcher_Api_Response
{
    public ?int $overallProgressPercentage = null;
    public array $groups = [];

    /**
     * @return int|null
     */
    public function getOverallProgressPercentage(): ?int
    {
        return $this->overallProgressPercentage;
    }

    /**
     * @param int|null $overallProgressPercentage
     */
    public function setOverallProgressPercentage(?int $overallProgressPercentage): void
    {
        $this->overallProgressPercentage = $overallProgressPercentage;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

}
