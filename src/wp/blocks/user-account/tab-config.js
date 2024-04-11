import GeneralTab from "./tabs/GeneralTab";
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";
export default [

    {
        name: 'general',
        title: 'General',
        component: GeneralTab
    },
    ...GlobalOptionsTabConfig
];
