<?php


class Tru_Fetcher_Acf_Field_Base extends acf_field
{

    protected $apiConfig;
    protected array $settings;
    protected Tru_Fetcher_Request_Api $fetcherApi;

    public function __construct($settings)
    {

        $this->settings = $settings;

        $this->apiConfig = Tru_Fetcher_Base::getConfig("fetcher-request-api-config");
        $this->fetcherApi = new Tru_Fetcher_Request_Api();

        // do not delete!
        parent::__construct();
    }

    protected function buildSelectList(string $valueKey, string $labelKey, array $servicesArray = [])
    {
        $selectArray = [];
        foreach ($servicesArray as $item) {
            if (isset($item->$valueKey) && isset($item->$labelKey)) {
                $selectArray[$item->$valueKey] = $item->$labelKey;
            }
        }
        return $selectArray;
    }

    protected function buildProviderSelectList(string $valueKey, string $labelKey, array $servicesArray = []) {
        $selectArray = [];
        foreach ($servicesArray as $item) {
            if (isset($item->$valueKey) && isset($item->$labelKey)) {
                $selectArray[$item->$valueKey] = sprintf("%s (%s)", $item->$labelKey, $item->category_labels);
            }
        }
        return $selectArray;
    }
}