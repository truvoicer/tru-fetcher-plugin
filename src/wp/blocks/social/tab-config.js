
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";
import GeneralTab from "./tabs/GeneralTab";

export default [
    {
        name: 'general',
        title: 'General',
        component: GeneralTab
    },
    ...GlobalOptionsTabConfig
]
