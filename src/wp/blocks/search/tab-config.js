import GeneralTab from "./tabs/GeneralTab";
import ListingsOptionsTab from "../common/tabs/listings/ListingsOptionsTab";
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";
export default [
    {
        name: 'general',
        title: 'General',
        component: GeneralTab
    },
    {
        name: 'listings_options',
        title: 'Listings Options',
        component: ListingsOptionsTab
    },
    ...GlobalOptionsTabConfig
]
