import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import GlobalOptionsTabConfig from "../global/tabs/GlobalOptionsTabConfig";

const UserProfile = (props) => {

    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
        ];
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
    );
};

export default UserProfile;
