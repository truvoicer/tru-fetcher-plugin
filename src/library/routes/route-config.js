import Dashboard from "../../Dashboard";
import GeneralSettings from "../../pages/settings/GeneralSettings";
import ApiSettings from "../../pages/settings/ApiSettings";
import LayoutSettings from "../../pages/settings/LayoutSettings";
import AccountSettings from "../../pages/settings/AccountSettings";
import GoogleSettings from "../../pages/settings/GoogleSettings";
import FacebookSettings from "../../pages/settings/FacebookSettings";
import GlobalSettings from "../../pages/settings/GlobalSettings";

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
        path: "/",
        key: 'settings',
        label: 'Settings',
        component: Dashboard,
        subRoutes: [
            {
                path: "/settings/general",
                key: 'general-settings',
                label: 'General',
                component: GeneralSettings,
            },
            {
                path: "/settings/api",
                key: 'api-settings',
                label: 'Api',
                component: ApiSettings,
            },
            {
                path: "/settings/layout",
                key: 'layout-settings',
                label: 'Layout',
                component: LayoutSettings,
            },
            {
                path: "/settings/account",
                key: 'account-settings',
                label: 'Account',
                component: AccountSettings,
            },
            {
                path: "/settings/google",
                key: 'google-settings',
                label: 'Google',
                component: GoogleSettings,
            },
            {
                path: "/settings/facebook",
                key: 'facebook-settings',
                label: 'Facebook',
                component: FacebookSettings,
            },
            {
                path: "/settings/global",
                key: 'global-settings',
                label: 'Global',
                component: GlobalSettings,
            },
        ]
    },

];
