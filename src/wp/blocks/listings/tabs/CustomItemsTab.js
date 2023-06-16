import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const CustomItemsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <>
            <PanelRow>
                <ToggleControl
                    label="List Start"
                    checked={attributes?.list_start}
                    onChange={(value) => {
                        setAttributes({list_start: value});
                    }}
                />
            </PanelRow>
            <PanelRow>
                <ToggleControl
                    label="List End"
                    checked={attributes?.list_end}
                    onChange={(value) => {
                        setAttributes({list_end: value});
                    }}
                />
            </PanelRow>
            <PanelRow>
                <ToggleControl
                    label="Custom Position"
                    checked={attributes?.custom_position}
                    onChange={(value) => {
                        setAttributes({custom_position: value});
                    }}
                />
            </PanelRow>
        </>
    );
};

export default CustomItemsTab;
