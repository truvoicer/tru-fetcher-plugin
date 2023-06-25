import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import RequestOptions from "../components/request-options/RequestOptions";
import Tabs from "../components/tabs/Tabs";

const TabsBlockEdit = (props) => {
    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
        ];
        if (attributes?.tabs_block_type === 'custom_tabs') {
            Tabs.defaultProps = {
                data: attributes?.tabs,
                onChange: formChangeHandler
            }
            tabConfig.push({
                name: 'custom_tabs',
                title: 'Custom Tabs',
                component: Tabs
            });
        }
        if (['request_carousel_tabs', 'request_video_tabs'].includes(attributes?.tabs_block_type)) {
            RequestOptions.defaultProps = {
                data: attributes.request_options,
                onChange: formChangeHandler
            }
            tabConfig.push({
                name: 'request_options',
                title: 'Request Options',
                component: RequestOptions
            });
        }
        return tabConfig;
    }
    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }
    return (
        <Panel>
            <PanelBody title="Tabs Block" initialOpen={true}>
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
            </PanelBody>
        </Panel>
    );
};

export default TabsBlockEdit;
