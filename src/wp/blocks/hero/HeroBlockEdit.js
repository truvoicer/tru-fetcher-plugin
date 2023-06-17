import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import tabConfig from "./tab-config";

const HeroBlockEdit = (props) => {

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    return (
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
    );
};

export default HeroBlockEdit;
