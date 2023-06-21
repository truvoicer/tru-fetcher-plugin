import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import tabConfig from "./tab-config";

const CarouselBlockEdit = (props) => {

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    return (
        <Panel>
            <PanelBody title="User Account Block" initialOpen={true}>

            </PanelBody>
        </Panel>
    );
};

export default CarouselBlockEdit;
