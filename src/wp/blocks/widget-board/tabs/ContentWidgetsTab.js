import React from 'react';
import {DropdownMenu, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Widgets from "../../widgets/Widgets";

const ContentWidgetsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        childConfigs,
    } = props;

    function formChangeHandler({widgets}) {
        setAttributes({
            ...attributes,
            content_widgets: widgets
        });
    }

    return (
        <Panel>
            <PanelBody title={'Content Widgets'} initialOpen={true}>
                <Widgets
                    childConfigs={childConfigs['content_widgets']}
                    data={attributes?.content_widgets}
                    onChange={formChangeHandler}
                />
            </PanelBody>
        </Panel>
    );
};

export default ContentWidgetsTab;
