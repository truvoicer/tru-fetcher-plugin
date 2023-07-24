import Dashboard from "../../Dashboard";
import GeneralSettings from "../../settings/pages/GeneralSettings";
import FormPresets from "../../settings/pages/FormPresets";

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
    {
        home: false,
        path: "/form-presets",
        key: 'form-presets',
        label: 'Form Presets',
        component: FormPresets,
    },
];
