import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const ApiTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <PanelRow>
            <SelectControl
                onChange={(value) => {
                    setAttributes({type: value});
                }}
                value={attributes?.type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Search',
                        value: 'search'
                    },
                    {
                        label: 'Blog',
                        value: 'blog'
                    },
                ]}
            />
            <ToggleControl
                label="Select Providers"
                checked={attributes?.select_providers}
                onChange={(value) => {
                    setAttributes({select_providers: value});
                }}
            />
        </PanelRow>
    );
};

export default ApiTab;
