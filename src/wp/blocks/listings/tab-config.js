import GeneralTab from "./tabs/GeneralTab";
import ApiTab from "./tabs/ApiTab";
import DisplayTab from "./tabs/DisplayTab";
import SearchTab from "./tabs/SearchTab";
import CustomItemsTab from "./tabs/CustomItemsTab";
import WordpressDataTab from "./tabs/WordpressDataTab";

export default [
    {
        name: 'general',
        title: 'General',
        component: GeneralTab
    },
    {
        name: 'display',
        title: 'Display',
        component: DisplayTab
    },
    {
        name: 'api_settings',
        title: 'Api Settings',
        component: ApiTab
    },
    {
        name: 'wordpress_settings',
        title: 'Wordpress Settings',
        component: WordpressDataTab
    },
    {
        name: 'search',
        title: 'Search',
        component: SearchTab
    },
    {
        name: 'custom_items',
        title: 'Custom Items',
        component: CustomItemsTab
    }
]
