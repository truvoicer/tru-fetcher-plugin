<?php

namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine_Base;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Menu;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_MenuItems;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Menu;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Menu_Items;

class Tru_Fetcher_DB_Data_Menus extends Tru_Fetcher_DB_Data
{

    private Tru_Fetcher_DB_Model_Menu $menuModel;
    private Tru_Fetcher_DB_Model_MenuItems $menuItemsModel;
    private Tru_Fetcher_DB_Repository_Menu $menuRepository;
    private Tru_Fetcher_DB_Repository_Menu_Items $menuItemsRepository;

    public function __construct()
    {
        $this->setModel(new Tru_Fetcher_DB_Model_Menu());

        $this->menuModel = new Tru_Fetcher_DB_Model_Menu();
        $this->menuItemsModel = new Tru_Fetcher_DB_Model_MenuItems();
        $this->menuRepository = new Tru_Fetcher_DB_Repository_Menu();
        $this->menuItemsRepository = new Tru_Fetcher_DB_Repository_Menu_Items();
    }

    private array $data = [
        [
            'name' => 'left_sidebar_menu',
            'items' => [
                [
                    'category_options_id' => null,
                    'name' => 'Home',
                    'initial_screen' => true,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'MATERIAL_TOP_TAB_STACK_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => true,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => true,
                    'featured_articles_show_multiple' => true,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => true,
                ],
                [
                    'category_options_id' => null,
                    'name' => 'Login',
                    'initial_screen' => false,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'LOGIN_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],

                [
                    'category_options_id' => null,
                    'name' => 'Settings',
                    'initial_screen' => false,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'SETTINGS_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],

                [
                    'category_options_id' => null,
                    'name' => 'Search',
                    'initial_screen' => false,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'SEARCH_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],

                [
                    'category_options_id' => null,
                    'name' => 'Account',
                    'initial_screen' => false,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'ACCOUNT_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],
            ]
        ],
        [
            'name' => 'bottom_tabs_menu',
            'items' => [
                [
                    'category_options_id' => null,
                    'name' => 'Home',
                    'initial_screen' => true,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'POST_LIST_CONTAINER_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => true,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => true,
                    'featured_articles_show_multiple' => true,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => true,
                ],
                [
                    'category_options_id' => null,
                    'name' => 'Categories',
                    'initial_screen' => false,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'CATEGORY_LIST_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],

                [
                    'category_options_id' => null,
                    'name' => 'Bookmarks',
                    'initial_screen' => false,
                    'active' => true,

                    'type' => 'screen',
                    'screen' => 'BOOKMARKS_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],

                [
                    'category_options_id' => null,
                    'name' => 'Feed',
                    'initial_screen' => false,
                    'active' => true,
                    'type' => 'screen',
                    'screen' => 'FEED_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],

                [
                    'category_options_id' => null,
                    'name' => 'Account',
                    'initial_screen' => false,
                    'active' => true,
                    'type' => 'screen',
                    'screen' => 'ACCOUNT_SCREEN_COMPONENT',
                    'icon' => 'fa-code',
                    'post_id' => null,
                    'articles_show_all' => false,
                    'articles_sort_by' => 'date_created',
                    'articles_sort_order' => 'descending',
                    'featured_articles_show' => false,
                    'featured_articles_show_multiple' => false,
                    'featured_articles_multiple_mode' => 'slideshow',
                    'featured_articles_slideshow_timer' => 3600,
                    'category_options_override' => false,
                ],
            ]
        ],
    ];

    public function install()
    {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->menuRepository->setSite($this->site);
            $this->menuItemsRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $menu) {
            $insertMenu = $this->menuRepository->insertMenus($menu);
            if (!$insertMenu) {
                $this->errors[] = "Error inserting menu at position {$index}";
                continue;
            }
            if (!isset($insertMenu[$this->menuModel->getIdColumn()])) {
                $this->errors[] = "Error finding menu id at position {$index}";
                continue;
            }
            $menuId = $insertMenu[$this->menuModel->getIdColumn()];
            foreach ($menu['items'] as $itemIndex => $menuItem) {
                $menuItem[$this->menuItemsModel->getMenuIdColumn()] = $menuId;
                $installItems = $this->menuItemsRepository->insertMenuItems($menuItem);
                if (!$installItems) {
                    $this->errors[] = "Error installing menu items at position option group: {$index}menu items: {$itemIndex}";
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

    public function check()
    {
        return [
            'success' => true,
        ];
    }
}
