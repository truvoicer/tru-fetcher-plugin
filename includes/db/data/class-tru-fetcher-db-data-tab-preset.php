<?php
namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine_Base;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Tab_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Form_Presets;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Tab_Presets;

class Tru_Fetcher_DB_Data_Tab_Preset extends Tru_Fetcher_DB_Data
{
    private Tru_Fetcher_DB_Repository_Tab_Presets $tabPresetRepository;
    public function __construct()
    {
        $this->setModel(new Tru_Fetcher_DB_Model_Tab_Presets());
        $this->tabPresetRepository = new Tru_Fetcher_DB_Repository_Tab_Presets();
    }

    private array $data = [
      [
          'name' => 'user_profile',
          'config_data' => 'a:4:{s:15:\"tabs_block_type\";s:11:\"custom_tabs\";s:16:\"tabs_orientation\";s:8:\"vertical\";s:4:\"tabs\";a:5:{i:0;a:7:{s:18:\"default_active_tab\";b:0;s:16:\"custom_tabs_type\";s:4:\"form\";s:6:\"tab_id\";s:0:\"\";s:11:\"tab_heading\";s:0:\"\";s:14:\"carousel_block\";N;s:13:\"content_block\";N;s:10:\"form_block\";a:11:{s:7:\"presets\";s:1:\"1\";s:9:\"form_type\";s:6:\"single\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:8:\"endpoint\";s:6:\"custom\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:0:{}s:18:\"endpoint_providers\";a:0:{}}}i:1;a:7:{s:18:\"default_active_tab\";b:0;s:16:\"custom_tabs_type\";s:4:\"form\";s:6:\"tab_id\";s:0:\"\";s:11:\"tab_heading\";s:0:\"\";s:14:\"carousel_block\";N;s:13:\"content_block\";N;s:10:\"form_block\";a:11:{s:7:\"presets\";s:1:\"2\";s:9:\"form_type\";s:6:\"single\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:8:\"endpoint\";s:6:\"custom\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:0:{}s:18:\"endpoint_providers\";a:0:{}}}i:2;a:7:{s:18:\"default_active_tab\";b:0;s:16:\"custom_tabs_type\";s:4:\"form\";s:6:\"tab_id\";s:0:\"\";s:11:\"tab_heading\";s:0:\"\";s:14:\"carousel_block\";N;s:13:\"content_block\";N;s:10:\"form_block\";a:11:{s:7:\"presets\";s:1:\"3\";s:9:\"form_type\";s:6:\"single\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:8:\"endpoint\";s:6:\"custom\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:0:{}s:18:\"endpoint_providers\";a:0:{}}}i:3;a:7:{s:18:\"default_active_tab\";b:0;s:16:\"custom_tabs_type\";s:4:\"form\";s:6:\"tab_id\";s:0:\"\";s:11:\"tab_heading\";s:0:\"\";s:14:\"carousel_block\";N;s:13:\"content_block\";N;s:10:\"form_block\";a:11:{s:7:\"presets\";s:1:\"4\";s:9:\"form_type\";s:6:\"single\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:8:\"endpoint\";s:6:\"custom\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:0:{}s:18:\"endpoint_providers\";a:0:{}}}i:4;a:7:{s:18:\"default_active_tab\";b:0;s:16:\"custom_tabs_type\";s:4:\"form\";s:6:\"tab_id\";s:0:\"\";s:11:\"tab_heading\";s:0:\"\";s:14:\"carousel_block\";N;s:13:\"content_block\";N;s:10:\"form_block\";a:11:{s:7:\"presets\";s:1:\"5\";s:9:\"form_type\";s:6:\"single\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:8:\"endpoint\";s:6:\"custom\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:0:{}s:18:\"endpoint_providers\";a:0:{}}}}s:15:\"request_options\";N;}',
      ],
    ];

    public function install() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->tabPresetRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $setting) {
            $insertSetting = $this->tabPresetRepository->insertTabPreset($setting, false);
            if (!$insertSetting) {
                $this->errors[] = "Error inserting tab preset at position {$index}";
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
            $this->tabPresetRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $setting) {
            $findSetting = $this->tabPresetRepository->findTabPresetByName($setting['name']);
            if (!$findSetting) {
                $this->errors[] = "Error finding tab preset at position {$index}";
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
