<?php
namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine_Base;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;

class Tru_Fetcher_DB_Data_Settings extends Tru_Fetcher_DB_Data
{
    private Tru_Fetcher_DB_Repository_Settings $settingsRepository;
    public function __construct()
    {
        $this->setModel(new Tru_Fetcher_DB_Model_Settings());
        $this->settingsRepository = new Tru_Fetcher_DB_Repository_Settings();
    }

    private array $data = [
      [
          'name' => 'article_source',
          'value' => 'posts',
      ],
      [
          'name' => 'default_theme',
          'value' => 'light',
      ],
      [
          'name' => 'default_topic',
          'value' => Tru_Fetcher_DB_Model_Topic::DEFAULT_TOPIC,
      ],
    ];

    public function install() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->settingsRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $setting) {
            $insertSetting = $this->settingsRepository->insertSettingsData($setting, Tru_Fetcher_DB_Engine_Base::DB_OPERATION_INSERT);
            if (!$insertSetting) {
                $this->errors[] = "Error inserting setting at position {$index}";
            }
        }
        if (count($this->errors)) {
            return [
                'success' => false,
                'errors' => $this->errors
            ];
        }
        return [
            'success' => true,
        ];
    }

    public function check() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->settingsRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $setting) {
            $findSetting = $this->settingsRepository->findSettingByName($setting['name']);
            if (!$findSetting) {
                $this->errors[] = "Error finding setting at position {$index}";
                return [
                    'success' => false,
                    'errors' => $this->errors
                ];
            }
        }
        return [
            'success' => true,
        ];
    }
}
