import React, {useContext} from 'react';
import SettingsContext from "../contexts/SettingsContext";
import NameValueDatatable from "../../components/tables/name-value-datatable/NameValueDatatable";

const GeneralSettings = () => {
    const settingsContext = useContext(SettingsContext);
    const columns = [
        {
            title: 'Name',
            dataIndex: 'name',
            width: '30%',
        },
        {
            title: 'Value',
            dataIndex: 'value',
            editable: true,
        },
    ];
    const groups = [
        {
            title: 'General Settings',
            names: [
                {
                    name: 'frontend_url',
                    label: 'Frontend URL',
                    type: 'url'
                },
                {
                    name: 'default_api_service',
                    label: 'Default API Service',
                    type: 'fetcher_api_service'
                },
            ]
        },
        {
            title: 'Api Settings',
            names: [
                {
                    name: 'docker',
                    label: 'Docker',
                    type: 'checkbox'
                },
                {
                    name: 'api_url',
                    label: 'API URL',
                    type: 'url'
                },
                {
                    name: 'docker_api_url',
                    label: 'Docker API URL',
                    type: 'url'
                },
                {
                    name: 'api_token',
                    label: 'API Token',
                    type: 'text'
                },
            ]
        },
        {
            title: 'Google Settings',
            names: [
                {
                    name: 'google_login_client_id',
                    label: 'Google Login Client ID',
                    type: 'text'
                },
                {
                    name: 'google_tag_manager_id',
                    label: 'Google Tag Manager ID',
                    type: 'text'
                },
            ]
        },
        {
            title: 'Facebook Settings',
            names: [
                {
                    name: 'facebook_app_id',
                    label: 'Facebook App ID',
                    type: 'text'
                },
            ]
        },
        {
            title: 'Global Scripts',
            names: [
                {
                    name: 'header_scripts',
                    label: 'Header Scripts',
                    type: 'textarea'
                },
                {
                    name: 'footer_scripts',
                    label: 'Footer Scripts',
                    type: 'textarea'
                },
            ]
        },
        {
            title: 'Layout Settings',
            names: [
                {
                    name: 'profile_menu_login_text',
                    label: 'Profile Menu Login Text',
                    type: 'text'
                },
                {
                    name: 'profile_menu_register_text',
                    label: 'Profile Menu Register Text',
                    type: 'text'
                },
            ]
        },
        {
            title: 'Account Settings',
            names: [
                {
                    name: 'tabs_orientation',
                    label: 'Tabs Orientation',
                    type: 'select',
                    options: [
                        {label: 'Vertical', value: 'vertical'},
                        {label: 'Horizontal', value: 'horizontal'},
                    ]
                },
                {
                    name: 'sidebar_background_image',
                    label: 'Sidebar Background Image',
                    type: 'image'
                },
                {
                    name: 'sidebar_bg_color',
                    label: 'Sidebar Background Color',
                    type: 'color_picker'
                },
            ]
        },
    ];

    function getSettings() {
        return settingsContext.settings.map((setting, index) => {
            let cloneSetting = {...setting};
            cloneSetting.key = index;
            return cloneSetting
        })
    }

    return (
        <NameValueDatatable
            groups={groups}
            columns={columns}
            dataSource={getSettings()}
            onDelete={({newData, key}) => {
                console.log({newData, key})
            }}
            onSave={({row, col}) => {
                console.log({row, col})
                if (col?.dataIndex !== 'value') {
                    return;
                }
                const findSettingIndex = settingsContext.settings.findIndex(setting => setting?.name === row?.name);
                let setting;
                if (findSettingIndex === -1) {
                    setting = {
                        name: row.name,
                        value: row.value
                    }
                    settingsContext.createSingleSetting(setting);
                } else {
                    setting = {...settingsContext.settings[findSettingIndex]};
                    setting['value'] = row.value;
                    settingsContext.updateSingleSetting(setting);
                }
            }}
            onAdd={({values}) => {
                settingsContext.addSetting(values)
            }}
        />
    );
};

export default GeneralSettings;
