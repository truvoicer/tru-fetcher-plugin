import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import ContentWidgetsTab from "./tabs/ContentWidgetsTab";
import SidebarWidgetsTab from "./tabs/SidebarWidgetsTab";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const WidgetBoardBlockEdit = (props) => {

    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
            {
                name: 'content_widgets',
                title: 'Content Widgets',
                component: ContentWidgetsTab
            },
        ];
        if (props.attributes?.show_sidebar) {
            tabConfig.push({
                name: 'sidebar_widgets',
                title: 'Sidebar Widgets',
                component: SidebarWidgetsTab
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
        <div {...useBlockProps()}>
            <Panel>
                <PanelBody title="User Account Block" initialOpen={true}>
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

export default WidgetBoardBlockEdit;
