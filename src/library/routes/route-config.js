import Dashboard from "../../Dashboard";
import GeneralSettings from "../../settings/pages/GeneralSettings";
import FormPresets from "../../settings/pages/presets/FormPresets";
import TabPresets from "../../settings/pages/presets/TabPresets";
import Presets from "../../settings/pages/presets/Presets";
import Keymaps from "../../settings/pages/keymaps/KeyMaps";
import Listings from "../../settings/pages/Listings";
import Templates from "../../settings/pages/Templates";

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
        path: "/listings",
        key: 'listings',
        label: 'Listings',
        component: Listings,
    },
    {
        home: false,
        path: "/presets",
        key: 'presets',
        label: 'Presets',
        component: Presets,
        subRoutes: [
            {
                path: "/presets/form-presets",
                key: 'form-presets',
                label: 'Form Presets',
                component: FormPresets,
            },
            {
                path: "/presets/tab-presets",
                key: 'tab-presets',
                label: 'Tab Presets',
                component: TabPresets,
            },
        ]
    },
    {
        home: false,
        path: "/key-maps",
        key: 'key-maps',
        label: 'Keymaps',
        component: Keymaps,
    },
    {
        home: false,
        path: "/templates",
        key: 'templates',
        label: 'Templates',
        component: Templates,
    },
];
