import React from 'react';
import {TabPanel, SelectControl} from "@wordpress/components";
import FormSettingsTab from "./tabs/FormSettingsTab";
import EndpointSettingsTab from "./tabs/EndpointSettingsTab";
import FormLayoutTab from "./tabs/FormLayoutTab";
import FormRowsTab from "./tabs/FormRowsTab";
import ExternalProvidersTab from "./tabs/ExternalProvidersTab";
import GlobalOptionsTabConfig from "../global/tabs/GlobalOptionsTabConfig";
import Grid from "../../../../components/Grid";

const FormComponent = (props) => {
    const {data, onChange, showPresets = true, reducers = null} = props;
    
    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }
    
    function getTabs() {
        let tabs = [];
        tabs.push({
            name: 'form_settings',
            title: 'Form Settings',
            component: FormSettingsTab
        });
        if (data?.presets === 'custom') {
            tabs.push({
                name: 'form_layout',
                title: 'Form Layout',
                component: FormLayoutTab
            });
            tabs.push({
                name: 'form_rows',
                title: 'Form Rows',
                component: FormRowsTab
            });
            tabs.push({
                name: 'endpoint_settings',
                title: 'Endpoint Settings',
                component: EndpointSettingsTab
            });
            if (data?.endpoint === 'external_provider') {
                tabs.push({
                    name: 'external_providers',
                    title: 'External Providers',
                    component: ExternalProvidersTab
                });
            }
        }
        tabs = [...tabs, ...GlobalOptionsTabConfig];
        return tabs;
    }

    function getPresets() {
        const formPresets = tru_fetcher_react?.form_presets;
        if (!Array.isArray(formPresets)) {
            console.warn('Form presets not found')
            return [];
        }
        return formPresets.map(preset => {
            return {
                label: preset.name,
                value: preset.id
            }
        });
    }

    return (
        <div className={'tr-news-app__form-block'}>
            {showPresets && (
                <Grid columns={2}>
                    <SelectControl
                        label="Presets"
                        onChange={(value) => {
                            if (typeof onChange === 'function') {
                                onChange({key: 'presets', value: value});
                            }
                        }}
                        value={data?.presets}
                        options={[
                            {
                                label: 'Please select',
                                value: ''
                            },
                            {
                                label: 'Custom',
                                value: 'custom'
                            },
                            ...getPresets()
                        ]}
                    />
                </Grid>
            )}
            {data?.presets === 'custom' && (
                <TabPanel
                    className="my-tab-panel"
                    activeClass="active-tab"
                    onSelect={(tabName) => {
                        // setTabName(tabName);
                    }}
                    tabs={
                        getTabs().map((tab) => {
                            return {
                                name: tab.name,
                                title: tab.title,
                            }
                        })
                    }>
                    {(tab) => {
                        return (
                            <div className={'tr-news-app__form-block'}
                                 style={{display: 'flex', flexDirection: 'column'}}>
                                {getTabs().map((item) => {
                                    if (item.name === tab.name) {
                                        return getTabComponent(item);
                                    }
                                    return null;
                                })}
                            </div>
                        )

                    }}
                </TabPanel>
            )}
        </div>
    );
};

export default FormComponent;
