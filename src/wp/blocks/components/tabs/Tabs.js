import React from 'react';
import {Panel, PanelBody, TabPanel, SelectControl} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import CustomTabs from "./CustomTabs";
import RequestOptions from "../request-options/RequestOptions";

const Tabs = (props) => {
    const {
        data = [],
        onChange,
        showPresets = true
    } = props;

    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
        ];
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
        return tabConfig;
    }

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let componentProps = {
            data: data,
            onChange: onChange
        };
        switch (tab.name) {
            case 'custom_tabs':
                componentProps = {
                    data: data?.tabs || [],
                    onChange: onChange
                };
                break;
            case 'request_options':
                componentProps = {
                    data: data?.request_options,
                    onChange: onChange
                };
                break;
        }
        let TabComponent = tab.component;
        return <TabComponent {...componentProps} />;
    }

    function getPresets() {
        const tabPresets = tru_fetcher_react?.tab_presets;
        if (!Array.isArray(tabPresets)) {
            console.warn('Tab presets not found')
            return [];
        }
        return tabPresets.map(preset => {
            return {
                label: preset.name,
                value: preset.id
            }
        });
    }

    return (
        <Panel>
            <PanelBody title="Tabs Block" initialOpen={true}>
                {showPresets && (
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
                                label: 'Custom',
                                value: 'custom'
                            },
                            ...getPresets()
                        ]}
                    />
                )}
                <SelectControl
                    label="Access Control"
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'access_control', value: value});
                        }
                    }}
                    value={data?.access_control}
                    options={[
                        {
                            label: 'Public',
                            value: 'public'
                        },
                        {
                            label: 'Protected',
                            value: 'protected'
                        },
                    ]}
                />
                {data?.presets === 'custom' && (
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
                )}
            </PanelBody>
        </Panel>
    );
};

export default Tabs;
