<?php
namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine_Base;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Form_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Form_Presets;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;

class Tru_Fetcher_DB_Data_Form_Preset extends Tru_Fetcher_DB_Data
{
    private Tru_Fetcher_DB_Repository_Form_Presets $formPresetRepository;
    public function __construct()
    {
        $this->setModel(new Tru_Fetcher_DB_Model_Form_Presets());
        $this->formPresetRepository = new Tru_Fetcher_DB_Repository_Form_Presets();
    }

    private array $data = [
      [
          'name' => 'user_profile',
          'config_data' => 'a:15:{s:7:\"presets\";s:6:\"custom\";s:9:\"form_type\";s:6:\"single\";s:6:\"method\";s:4:\"post\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:7:\"form_id\";s:13:\"user_personal\";s:7:\"heading\";s:7:\"Profile\";s:8:\"endpoint\";s:9:\"user_meta\";s:13:\"endpoint_type\";s:9:\"protected\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:2:{i:0;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:7:\"country\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:13:\"Enter Country\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:1;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:4:\"town\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:10:\"Enter Town\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}}s:18:\"endpoint_providers\";a:0:{}}',
      ],
      [
          'name' => 'experiences',
          'config_data' => 'a:15:{s:7:\"presets\";s:6:\"custom\";s:9:\"form_type\";s:4:\"list\";s:6:\"method\";s:4:\"post\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:7:\"form_id\";s:11:\"experiences\";s:7:\"heading\";s:11:\"Experiences\";s:8:\"endpoint\";s:9:\"user_meta\";s:13:\"endpoint_type\";s:9:\"protected\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:4:{i:0;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:9:\"job_title\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:9:\"Job Title\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:1;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:7:\"company\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:7:\"Company\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:2;a:1:{s:10:\"form_items\";a:3:{i:0;a:16:{s:12:\"form_control\";s:4:\"date\";s:4:\"name\";s:4:\"from\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:4:\"From\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}i:1;a:16:{s:12:\"form_control\";s:4:\"date\";s:4:\"name\";s:2:\"to\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:2:\"To\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}i:2;a:16:{s:12:\"form_control\";s:8:\"checkbox\";s:4:\"name\";s:20:\"currently_work_there\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:17:\"I still work here\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:3;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:8:\"textarea\";s:4:\"name\";s:11:\"description\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:11:\"Description\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}}s:18:\"endpoint_providers\";a:0:{}}',
      ],
      [
          'name' => 'skills',
          'config_data' => 'a:15:{s:7:\"presets\";s:6:\"custom\";s:9:\"form_type\";s:4:\"list\";s:6:\"method\";s:4:\"post\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:9:\"Add Skill\";s:7:\"form_id\";s:6:\"skills\";s:7:\"heading\";s:6:\"Skills\";s:8:\"endpoint\";s:9:\"user_meta\";s:13:\"endpoint_type\";s:9:\"protected\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:1:{i:0;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:5:\"skill\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:9:\"Add skill\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}}s:18:\"endpoint_providers\";a:0:{}}',
      ],
      [
          'name' => 'cv',
          'config_data' => 'a:15:{s:7:\"presets\";s:6:\"custom\";s:9:\"form_type\";s:6:\"single\";s:6:\"method\";s:4:\"post\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:7:\"form_id\";s:2:\"cv\";s:7:\"heading\";s:2:\"CV\";s:8:\"endpoint\";s:9:\"user_meta\";s:13:\"endpoint_type\";s:9:\"protected\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:1:{i:0;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:11:\"file_upload\";s:4:\"name\";s:2:\"cv\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:2:\"CV\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:1;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}}s:18:\"endpoint_providers\";a:0:{}}',
      ],
      [
          'name' => 'education',
          'config_data' => 'a:15:{s:7:\"presets\";s:6:\"custom\";s:9:\"form_type\";s:4:\"list\";s:6:\"method\";s:4:\"post\";s:19:\"submit_button_label\";s:6:\"Submit\";s:21:\"add_item_button_label\";s:8:\"Add Item\";s:7:\"form_id\";s:9:\"education\";s:7:\"heading\";s:9:\"Education\";s:8:\"endpoint\";s:9:\"user_meta\";s:13:\"endpoint_type\";s:9:\"protected\";s:8:\"redirect\";b:0;s:12:\"layout_style\";s:10:\"full_width\";s:11:\"column_size\";i:12;s:5:\"align\";s:4:\"left\";s:9:\"form_rows\";a:5:{i:0;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:18:\"select_data_source\";s:4:\"name\";s:4:\"type\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:4:\"Type\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:2:{i:0;a:2:{s:5:\"label\";s:7:\"A-Level\";s:5:\"value\";s:7:\"a_level\";}i:1;a:2:{s:5:\"label\";s:10:\"Bsc Degree\";s:5:\"value\";s:10:\"bsc_degree\";}}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:1;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:14:\"institute_name\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:19:\"Name of institution\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:2;a:1:{s:10:\"form_items\";a:2:{i:0;a:16:{s:12:\"form_control\";s:4:\"date\";s:4:\"name\";s:4:\"from\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:4:\"From\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}i:1;a:16:{s:12:\"form_control\";s:4:\"date\";s:4:\"name\";s:2:\"to\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:2:\"To\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:3;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:7:\"subject\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:7:\"Subject\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}i:4;a:1:{s:10:\"form_items\";a:1:{i:0;a:16:{s:12:\"form_control\";s:4:\"text\";s:4:\"name\";s:5:\"grade\";s:5:\"value\";s:0:\"\";s:11:\"placeholder\";s:5:\"Grade\";s:14:\"label_position\";s:3:\"top\";s:7:\"classes\";s:0:\"\";s:11:\"description\";s:0:\"\";s:8:\"multiple\";b:0;s:7:\"options\";a:0:{}s:8:\"endpoint\";s:0:\"\";s:13:\"show_dropzone\";b:0;s:16:\"dropzone_message\";s:15:\"Drop files here\";s:27:\"accepted_file_types_message\";s:0:\"\";s:18:\"allowed_file_types\";a:0:{}s:11:\"button_type\";s:6:\"submit\";s:11:\"button_text\";s:6:\"Submit\";}}}}s:18:\"endpoint_providers\";a:0:{}}',
      ],
    ];

    public function install() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->formPresetRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $setting) {
            $insertSetting = $this->formPresetRepository->insertFormPreset($setting);
            if (!$insertSetting) {
                $this->errors[] = "Error inserting form preset ({$setting['name']}) at position {$index}";
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
            $this->formPresetRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $setting) {
            $findSetting = $this->formPresetRepository->findFormPresetByName($setting['name']);
            if (!$findSetting) {
                $this->errors[] = "Error finding form preset ({$setting['name']}) at position {$index}";
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
