import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const PostsBlockEdit = (props) => {
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
            <PanelBody title="Posts Block" initialOpen={true}>
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

export default PostsBlockEdit;
