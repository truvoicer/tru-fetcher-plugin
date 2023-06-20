import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import tabConfig from "./tab-config";

const FormComponent = (props) => {

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    return (
        <div className={'tr-news-app__form-block'}>
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
                        <div className={'tr-news-app__form-block'} style={{display: 'flex', flexDirection: 'column'}}>
                            {tabConfig.map((item) => {
                                if (item.name === tab.name) {
                                    return getTabComponent(item);
                                }
                                return null;
                            })}
                        </div>
                    )

                }}
            </TabPanel>
        </div>
    );
};

export default FormComponent;