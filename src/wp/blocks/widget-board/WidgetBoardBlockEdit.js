import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';
import ContentWidgetsTab from "./tabs/ContentWidgetsTab";
import Carousel from "../components/carousel/Carousel";
import SidebarWidgetsTab from "./tabs/SidebarWidgetsTab";
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";
import BlockEditComponent from '../common/BlockEditComponent';

const WidgetBoardBlockEdit = (props) => {
    const {attributes, setAttributes, childConfigs} = props;
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
        if (attributes?.show_sidebar) {
            tabConfig.push({
                name: 'sidebar_widgets',
                title: 'Sidebar Widgets',
                component: SidebarWidgetsTab
            });
        }
        tabConfig = [...tabConfig, ...GlobalOptionsTabConfig];
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
        <BlockEditComponent
            {...props}
            title='Widget Board Block'
        >
            <Panel>
                <PanelBody title="Widget Board Block" initialOpen={true}>
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
        </BlockEditComponent>
    );
};

export default WidgetBoardBlockEdit;
