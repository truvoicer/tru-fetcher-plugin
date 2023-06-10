import Dashboard from "../../Dashboard";
import GeneralSettings from "../../settings/pages/GeneralSettings";
import ApiSettings from "../../settings/pages/ApiSettings";
import LayoutSettings from "../../settings/pages/LayoutSettings";
import AccountSettings from "../../settings/pages/AccountSettings";
import GoogleSettings from "../../settings/pages/GoogleSettings";
import FacebookSettings from "../../settings/pages/FacebookSettings";
import GlobalSettings from "../../settings/pages/GlobalSettings";

export default [
    {
        home: true,
        path: "/",
        key: 'dashboard',
        label: 'Dashboard',
        component: Dashboard,
    },

    {
        home: false,
        path: "/settings",
        key: 'settings',
        label: 'Settings',
        component: Dashboard,
        subRoutes: [
            {
                path: "/settings/general-settings",
                key: 'general-settings',
                label: 'General',
                component: GeneralSettings,
            },
            {
                path: "/settings/api-settings",
                key: 'api-settings',
                label: 'Api',
                component: ApiSettings,
            },
            {
                path: "/settings/layout-settings",
                key: 'layout-settings',
                label: 'Layout',
                component: LayoutSettings,
            },
            {
                path: "/settings/account-settings",
                key: 'account-settings',
                label: 'Account',
                component: AccountSettings,
            },
            {
                path: "/settings/google-settings",
                key: 'google-settings',
                label: 'Google',
                component: GoogleSettings,
            },
            {
                path: "/settings/facebook-settings",
                key: 'facebook-settings',
                label: 'Facebook',
                component: FacebookSettings,
            },
            {
                path: "/settings/global-settings",
                key: 'global-settings',
                label: 'Global',
                component: GlobalSettings,
            },
        ]
    },

];
