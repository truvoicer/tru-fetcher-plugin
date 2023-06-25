import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import WidgetsTab from "./tabs/WidgetsTab";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const WidgetBoardBlockEdit = (props) => {
    const {attributes, setAttributes} = props;
    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
            {
                name: 'widgets',
                title: 'Widgets',
                component: WidgetsTab
            },
        ];
        return tabConfig;
    }

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    // console.log({attributes})
    return (
        <div {...useBlockProps()}>
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
        </div>
    );
};

export default WidgetBoardBlockEdit;
