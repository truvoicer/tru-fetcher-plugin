import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

import GeneralTab from "./tabs/GeneralTab";
import DisplayTab from "./tabs/DisplayTab";
import ApiTab from "./tabs/ApiTab";
import WordpressDataTab from "./tabs/WordpressDataTab";
import SearchTab from "./tabs/SearchTab";
import CustomItemsTab from "./tabs/CustomItemsTab";
import SidebarTab from "./tabs/SidebarTab";
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";

const ListingsBlockEdit = (props) => {

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    function getTabConfig() {
        let tabConfig = [];
        tabConfig.push({
            name: 'general',
            title: 'General',
            component: GeneralTab
        });
        if (props.attributes?.source === 'wordpress') {
            tabConfig.push({
                name: 'wordpress_settings',
                title: 'Wordpress Settings',
                component: WordpressDataTab
            });
        }
        tabConfig.push({
            name: 'display',
            title: 'Display',
            component: DisplayTab
        });
        tabConfig.push({
            name: 'sidebar',
            title: 'Sidebar',
            component: SidebarTab
        });
        if (props.attributes?.source === 'api') {
            tabConfig.push({
                name: 'api_settings',
                title: 'Api Settings',
                component: ApiTab
            });
        }
        tabConfig.push({
            name: 'search',
            title: 'Search',
            component: SearchTab
        });
        tabConfig.push({
            name: 'custom_items',
            title: 'Custom Items',
            component: CustomItemsTab
        });
        tabConfig = [...tabConfig, ...GlobalOptionsTabConfig];
        return tabConfig;
    }

    return (
        <div {...useBlockProps()}>
            <Panel>
                <PanelBody title="Listings Block" initialOpen={true}>
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
        </div>
    );
};

export default ListingsBlockEdit;
