import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";

import {useBlockProps, RichText} from '@wordpress/block-editor';
import GeneralTab from "./tabs/GeneralTab";
import tabConfig from "./tab-config";
import ApiTab from "./tabs/ApiTab";

const ListingsBlockEdit = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    const blockProps = useBlockProps();

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        TabComponent.defaultProps = props;
        return <TabComponent />;
    }

    return (
        <Panel>
            <PanelBody title="Listings Block" initialOpen={true}>
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
                                    return (
                                        <PanelRow>
                                            {getTabComponent(item)}
                                        </PanelRow>
                                    )
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

export default ListingsBlockEdit;
