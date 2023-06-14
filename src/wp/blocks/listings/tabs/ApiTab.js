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
                label="Api Fetch Type"
                onChange={(value) => {
                    setAttributes({listing_block_type: value});
                }}
                value={attributes?.listing_block_type}
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
            <SelectControl
                label="Api Listings Category"
                onChange={(value) => {
                    setAttributes({api_listings_category: value});
                }}
                value={attributes?.api_listings_category}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
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
