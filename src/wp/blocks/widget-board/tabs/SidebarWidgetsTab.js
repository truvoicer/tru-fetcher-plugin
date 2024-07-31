import React from 'react';
import {DropdownMenu, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Widgets from "../../widgets/Widgets";

const SidebarWidgetsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        childConfigs
    } = props;
    function formChangeHandler({widgets}) {
        setAttributes({
            ...attributes,
            sidebar_widgets: widgets
        });
    }

    return (
        <Panel>
            <PanelBody title={'Sidebar Widgets'} initialOpen={true}>
                <Widgets
                    childConfigs={childConfigs['sidebar_widgets']}
                    data={attributes?.sidebar_widgets}
                    onChange={formChangeHandler}
                />
            </PanelBody>
        </Panel>
    );
};

export default SidebarWidgetsTab;
