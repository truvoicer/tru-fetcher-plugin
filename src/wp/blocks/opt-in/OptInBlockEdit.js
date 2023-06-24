import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import OptInInfoTab from "./tabs/OptInInfoTab";
import { useInnerBlocksProps, InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import {getChildBlockIds} from "../../helpers/wp-helpers";


const OptInBlockEdit = (props) => {
    const {attributes, setAttributes} = props;
    const { children, ...innerBlocksProps } = useInnerBlocksProps(  );
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }
    console.log({innerBlocksProps, children})
    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
            {
                name: 'optin_info',
                title: 'Opt In Info',
                component: OptInInfoTab
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
        <div { ...useBlockProps() }>
        <Panel>
            <PanelBody title="Opt In Block" initialOpen={true}>
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
                <InnerBlocks allowedBlocks={ getChildBlockIds(props?.config) } />
            </PanelBody>
        </Panel>
        </div>
    );
};

export default OptInBlockEdit;
