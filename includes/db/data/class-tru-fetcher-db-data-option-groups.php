<?php
namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Option_Group;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Option_Group_Items;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Option_Group_Items;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Option_Groups;

class Tru_Fetcher_DB_Data_Option_Groups extends Tru_Fetcher_DB_Data
{

    private Tru_Fetcher_DB_Model_Option_Group $optionsGroupModel;
    private Tru_Fetcher_DB_Model_Option_Group_Items $optionsGroupItemsModel;

    private Tru_Fetcher_DB_Repository_Option_Groups $optionGroupRepository;
    private Tru_Fetcher_DB_Repository_Option_Group_Items $optionGroupItemsRepository;
    public function __construct()
    {
        $this->setModel(new Tru_Fetcher_DB_Model_Option_Group());

        $this->optionsGroupModel = new Tru_Fetcher_DB_Model_Option_Group();
        $this->optionsGroupItemsModel = new Tru_Fetcher_DB_Model_Option_Group_Items();
        $this->optionGroupRepository = new Tru_Fetcher_DB_Repository_Option_Groups();
        $this->optionGroupItemsRepository = new Tru_Fetcher_DB_Repository_Option_Group_Items();
    }

    private array $data = [
      [
          'name' => 'screen_type',
          'default_value' => 'screen',
          'items' => [
              [
                  'option_key' => 'article',
                  'option_value' => 'article',
                  'option_text' => 'Article',
              ],
              [
                  'option_key' => 'category',
                  'option_value' => 'category',
                  'option_text' => 'Category',
              ],
              [
                  'option_key' => 'screen',
                  'option_value' => 'screen',
                  'option_text' => 'Screen',
              ],
          ]
      ],
      [
          'name' => 'screen',
          'default_value' => 'MATERIAL_TOP_TAB_STACK_COMPONENT',
          'items' => [
              [
                  'option_key' => 'MATERIAL_TOP_TAB_STACK_COMPONENT',
                  'option_value' => 'MATERIAL_TOP_TAB_STACK_COMPONENT',
                  'option_text' => 'MATERIAL_TOP_TAB_STACK_COMPONENT',
              ],
              [
                  'option_key' => 'SETTINGS_SCREEN_COMPONENT',
                  'option_value' => 'SETTINGS_SCREEN_COMPONENT',
                  'option_text' => 'SETTINGS_SCREEN_COMPONENT',
              ],
              [
                  'option_key' => 'SEARCH_SCREEN_COMPONENT',
                  'option_value' => 'SEARCH_SCREEN_COMPONENT',
                  'option_text' => 'SEARCH_SCREEN_COMPONENT',
              ],
              [
                  'option_key' => 'LOGIN_SCREEN_COMPONENT',
                  'option_value' => 'LOGIN_SCREEN_COMPONENT',
                  'option_text' => 'LOGIN_SCREEN_COMPONENT',
              ],
              [
                  'option_key' => 'ACCOUNT_SCREEN_COMPONENT',
                  'option_value' => 'ACCOUNT_SCREEN_COMPONENT',
                  'option_text' => 'ACCOUNT_SCREEN_COMPONENT',
              ],
              [
                  'option_key' => 'CATEGORY_LIST_COMPONENT',
                  'option_value' => 'CATEGORY_LIST_COMPONENT',
                  'option_text' => 'CATEGORY_LIST_COMPONENT',
              ],
              [
                  'option_key' => 'BOOKMARKS_SCREEN_COMPONENT',
                  'option_value' => 'BOOKMARKS_SCREEN_COMPONENT',
                  'option_text' => 'BOOKMARKS_SCREEN_COMPONENT',
              ],
              [
                  'option_key' => 'FEED_SCREEN_COMPONENT',
                  'option_value' => 'FEED_SCREEN_COMPONENT',
                  'option_text' => 'FEED_SCREEN_COMPONENT',
              ],
              [
                  'option_key' => 'POST_LIST_CONTAINER_COMPONENT',
                  'option_value' => 'POST_LIST_CONTAINER_COMPONENT',
                  'option_text' => 'POST_LIST_CONTAINER_COMPONENT',
              ],
          ]
      ],
      [
          'name' => 'sort_by',
          'default_value' => 'date_created',
          'items' => [
              [
                  'option_key' => 'date_modified',
                  'option_value' => 'date_modified',
                  'option_text' => 'Date Modified',
              ],
              [
                  'option_key' => 'date_created',
                  'option_value' => 'date_created',
                  'option_text' => 'Date Created',
              ],
          ]
      ],
      [
          'name' => 'sort_order',
          'default_value' => 'descending',
          'items' => [
              [
                  'option_key' => 'ascending',
                  'option_value' => 'ascending',
                  'option_text' => 'Ascending',
              ],
              [
                  'option_key' => 'descending',
                  'option_value' => 'descending',
                  'option_text' => 'Descending',
              ],
          ]
      ],
      [
          'name' => 'featured_articles_multiple_mode',
          'default_value' => 'slideshow',
          'items' => [
              [
                  'option_key' => 'slideshow',
                  'option_value' => 'slideshow',
                  'option_text' => 'Slideshow',
              ],
              [
                  'option_key' => 'carousel',
                  'option_value' => 'carousel',
                  'option_text' => 'Carousel',
              ],
          ]
      ],
    ];

    public function install() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->optionGroupRepository->setSite($this->site);
            $this->optionGroupItemsRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $optionGroup) {
            $insertOptionGroup = $this->optionGroupRepository->insertOptionGroupData($optionGroup);
            if (!$insertOptionGroup) {
                $this->errors[] = "Error inserting option group at position {$index}";
                continue;
            }
            if (!isset($insertOptionGroup[$this->optionsGroupModel->getIdColumn()])) {
                $this->errors[] = "Error finding option group id at position {$index}";
                continue;
            }
            $optionGroupId = $insertOptionGroup[$this->optionsGroupModel->getIdColumn()];
            foreach ($optionGroup['items'] as $itemIndex => $optionGroupItem) {
                $optionGroupItem[$this->optionsGroupItemsModel->getOptionGroupIdColumn()] = $optionGroupId;
                $installItems = $this->optionGroupItemsRepository->insertOptionGroupItemData($optionGroupItem);
                if (!$installItems) {
                    $this->errors[] = "Error installing option group items at position option group: {$index} option group items: {$itemIndex}";
                }
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
            $this->optionGroupRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $optionGroup) {
            $findOptionGroup = $this->optionGroupRepository->findOptionGroupByName($optionGroup['name']);
            if (!$findOptionGroup) {
                $this->errors[] = "Error finding option group at position {$index}";
                return [
                    'success' => false,
                    'errors' => $this->errors
                ];
            }
            if (!isset($findOptionGroup[$this->optionsGroupModel->getIdColumn()])) {
                $this->errors[] = "Error finding option group id at position {$index}";
                return [
                    'success' => false,
                    'errors' => $this->errors
                ];
            }
            $optionGroupId = $findOptionGroup[$this->optionsGroupModel->getIdColumn()];
            foreach ($optionGroup['items'] as $itemIndex => $optionGroupItem) {
                $optionGroupItem[$this->optionsGroupItemsModel->getOptionGroupIdColumn()] = $optionGroupId;
                $findItemsItems = $this->optionGroupItemsRepository->findOptionGroupItemByParams($optionGroupItem);
                if (!$findItemsItems) {
                    $this->errors[] = "Error finding option group items at position option group: {$index} option group items: {$itemIndex}";
                    return [
                        'success' => false,
                        'errors' => $this->errors
                    ];
                }
            }
        }
        return [
            'success' => true,
        ];
    }
}
