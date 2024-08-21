
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";
import ContentTab from "./tabs/ContentTab";

export default [
    {
        name: 'content',
        title: 'Content',
        component: ContentTab
    },
    ...GlobalOptionsTabConfig
]
