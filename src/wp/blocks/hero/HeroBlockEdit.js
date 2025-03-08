import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import tabConfig from "./tab-config";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';
import BlockEditComponent from '../common/BlockEditComponent';

const HeroBlockEdit = (props) => {

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
            title='Hero Block'
        >
            <Panel>
                <PanelBody title="Hero Block" initialOpen={true}>
                    <TabPanel
                        className="my-tab-panel"
                        activeClass="active-tab"
                        onSelect={(tabName) => {
                            // setTabName(tabName);
                        }}
                        tabs={
                            tabConfig.map((tab) => {
                                return {
                                    name: tab.name,
                                    title: tab.title,
                                }
                            })
                        }>
                        {(tab) => {
                            return (
                                <>
                                    {tabConfig.map((item) => {
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

export default HeroBlockEdit;
