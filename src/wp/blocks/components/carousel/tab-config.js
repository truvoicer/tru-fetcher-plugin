import GeneralTab from "./tabs/GeneralTab";
import GlobalOptionsTabConfig from "../global/tabs/GlobalOptionsTabConfig";
export default [
    {
        name: 'general_tab',
        title: 'General',
        component: GeneralTab
    },
    ...GlobalOptionsTabConfig
]
