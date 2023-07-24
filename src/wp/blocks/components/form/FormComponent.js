import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import tabConfig from "./tab-config";
import FormSettingsTab from "./tabs/FormSettingsTab";
import EndpointSettingsTab from "./tabs/EndpointSettingsTab";
import FormLayoutTab from "./tabs/FormLayoutTab";
import FormRowsTab from "./tabs/FormRowsTab";
import EndpointProvidersTab from "./tabs/EndpointProvidersTab";

const FormComponent = (props) => {

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
        if (props?.data?.presets === 'custom') {
            tabs.push({
                name: 'endpoint_settings',
                title: 'Endpoint Settings',
                component: EndpointSettingsTab
            });
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
                name: 'endpoint_providers',
                title: 'Endpoint Providers',
                component: EndpointProvidersTab
            });
        }
        return tabs;
    }

    return (
        <div className={'tr-news-app__form-block'}>
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
                        <div className={'tr-news-app__form-block'} style={{display: 'flex', flexDirection: 'column'}}>
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
        </div>
    );
};

export default FormComponent;
