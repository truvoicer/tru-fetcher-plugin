import FormSettingsTab from "./tabs/FormSettingsTab";
import EndpointSettingsTab from "./tabs/EndpointSettingsTab";
import FormLayoutTab from "./tabs/FormLayoutTab";
import FormRowsTab from "./tabs/FormRowsTab";
import ExternalProvidersTab from "./tabs/ExternalProvidersTab";
export default [
    {
        name: 'form_settings',
        title: 'Form Settings',
        component: FormSettingsTab
    },
    {
        name: 'endpoint_settings',
        title: 'Endpoint Settings',
        component: EndpointSettingsTab
    },
    {
        name: 'form_layout',
        title: 'Form Layout',
        component: FormLayoutTab
    },
    {
        name: 'form_rows',
        title: 'Form Rows',
        component: FormRowsTab
    },
    {
        name: 'endpoint_providers',
        title: 'Endpoint Providers',
        component: ExternalProvidersTab
    },
]
