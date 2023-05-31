import Dashboard from "../../Dashboard";
import SendMessage from "../../components/messaging/SendMessage";
import CategoryOptions from "../../components/settings/CategoryOptions";
import Terms from "../../components/settings/Terms";
import OptionsGroups from "../../components/settings/OptionsGroups";
import Menus from "../../components/settings/menu/Menus";
import GeneralSettings from "../../components/settings/GeneralSettings";
import Topics from "../../components/messaging/Topics";
import Devices from "../../components/messaging/Devices";
import MessagingDashboard from "../../components/messaging/MessagingDashboard";

export default [
    {
        home: true,
        path: "/",
        key: 'dashboard',
        label: 'Dashboard',
        component: Dashboard,
    },
    {
        path: "/messaging",
        key: 'messaging',
        label: 'Messaging',
        component: MessagingDashboard,
        subRoutes: [
            {
                path: "/messaging/send-message",
                key: 'send-message',
                label: 'Send Message',
                component: SendMessage,
            },
            {
                path: "/messaging/devices",
                key: 'devices',
                label: 'Devices',
                component: Devices,
            },
            {
                path: "/messaging/topics",
                key: 'topics',
                label: 'Topics',
                component: Topics,
            },
        ]
    },
    {
        path: "/settings",
        key: 'settings',
        label: 'Settings',
        component: Dashboard,
        subRoutes: [
            {
                path: "/settings/general",
                key: 'general',
                label: 'General',
                component: GeneralSettings,
            },
            {
                path: "/settings/menu",
                key: 'menu',
                label: 'Menu',
                component: Menus,
            },
            {
                path: "/settings/category-options",
                key: 'category-options',
                label: 'Category',
                component: CategoryOptions,
            },
            {
                path: "/settings/terms",
                key: 'terms',
                label: 'Terms',
                component: Terms,
            },
            {
                path: "/settings/option-groups",
                key: 'option-groups',
                label: 'Option Groups',
                component: OptionsGroups,
            },
        ]
    },
];
