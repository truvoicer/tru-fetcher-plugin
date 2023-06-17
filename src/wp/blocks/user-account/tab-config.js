import FormSettingsTab from "../components/form/tabs/FormSettingsTab";
import EndpointSettingsTab from "../components/form/tabs/EndpointSettingsTab";
import FormLayoutTab from "../components/form/tabs/FormLayoutTab";
export default [
    {
        name: 'form_settings_tab',
        title: 'Form Settings',
        component: FormSettingsTab
    },
    {
        name: 'endpoint_settings_tab',
        title: 'Endpoint Settings',
        component: EndpointSettingsTab
    },
    {
        name: 'form_layout_tab',
        title: 'Form Layout',
        component: FormLayoutTab
    },
]
