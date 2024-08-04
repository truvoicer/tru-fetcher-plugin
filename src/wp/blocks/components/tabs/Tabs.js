import React from 'react';
import {Panel, PanelBody, TabPanel, SelectControl} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import CustomTabs from "./CustomTabs";
import RequestOptions from "../request-options/RequestOptions";
import GlobalOptionsTabConfig from "../global/tabs/GlobalOptionsTabConfig";
import PresetTab from "./tabs/PresetTab";

const Tabs = (props) => {
    const {
        data = [],
        onChange,
        showPresets = true
    } = props;

    function getTabConfig() {
        let tabConfig = [];
        if (showPresets) {
            tabConfig.push({
                name: 'preset',
                title: 'Presets',
                component: PresetTab
            });
        }
        if (data?.presets === 'custom') {
            tabConfig.push({
                name: 'general',
                title: 'General',
                component: GeneralTab
            });
        }
        if (data?.tabs_block_type === 'custom_tabs') {
            tabConfig.push({
                name: 'custom_tabs',
                title: 'Custom Tabs',
                component: CustomTabs
            });
        }
        if (['request_carousel_tabs', 'request_video_tabs'].includes(data?.tabs_block_type)) {
            tabConfig.push({
                name: 'request_options',
                title: 'Request Options',
                component: RequestOptions
            });
        }
        tabConfig = [...tabConfig, ...GlobalOptionsTabConfig];
        return tabConfig;
    }

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let componentProps = {
            showPresets,
            data: data,
            onChange: onChange,
            attributes: data,
            reducers: props?.reducers,
            setAttributes: (dataObj) => {
                Object.keys(dataObj).forEach((key) => {
                    onChange({key, value: dataObj[key]});
                })
            }
        };
        switch (tab.name) {
            case 'custom_tabs':
                componentProps.data = data?.tabs || [];
                break;
            case 'request_options':
                componentProps.data = data?.request_options;
                break;
        }
        let TabComponent = tab.component;
        return <TabComponent {...componentProps} />;
    }


    return (
        <TabPanel
            className="my-tab-panel"
            activeClass="active-tab"
            onSelect={(tabName) => {
                // setTabName(tabName);
            }}
            tabs={
                getTabConfig().map((tab) => {
                    return {
                        name: tab.name,
                        title: tab.title,
                    }
                })
            }>
            {(tab) => {
                return (
                    <>
                        {getTabConfig().map((item) => {
                            if (item.name === tab.name) {
                                return getTabComponent(item);
                            }
                            return null;
                        })}
                    </>
                )

            }}
        </TabPanel>
    );
};

export default Tabs;
