import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const ApiTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        config
    } = props;
    console.log({config})
    function findListingsCategoryTerms() {
        return tru_fetcher_react.blocks.taxonomies.find(taxonomy => taxonomy.slug === 'listings_category').terms;
    }
    return (
        <PanelRow>
            <SelectControl
                label="Listings Category"
                onChange={(value) => {
                    setAttributes({listings_category: value});
                }}
                value={attributes?.listings_category}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                ]}
            />
            <SelectControl
                label="Item List"
                onChange={(value) => {
                    setAttributes({item_list: value});
                }}
                value={attributes?.item_list}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                ]}
            />
        </PanelRow>
    );
};

export default ApiTab;
