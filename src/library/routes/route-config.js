import Dashboard from "../../Dashboard";
import GeneralSettings from "../../settings/pages/GeneralSettings";

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
        component: GeneralSettings,
    },

];
