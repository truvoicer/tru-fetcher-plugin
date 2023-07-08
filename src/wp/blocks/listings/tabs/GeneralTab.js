import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {findTaxonomyIdIdentifier, findTaxonomySelectOptions} from "../../../helpers/wp-helpers";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    const listingsCategoryId = findTaxonomyIdIdentifier('trf_listings_category')
    return (
        <div>
            <SelectControl
                label="Listing Data Source"
                onChange={(value) => {
                    setAttributes({source: value});
                }}
                value={attributes?.source}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Api',
                        value: 'api'
                    },
                    {
                        label: 'Wordpress',
                        value: 'wordpress'
                    },
                ]}
            />
            <SelectControl
                label="Listings Category"
                onChange={(value) => {
                    setAttributes({[listingsCategoryId]: value});
                }}
                value={attributes?.[listingsCategoryId]}
                options={[
                    ...[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                    ],
                    ...findTaxonomySelectOptions('trf_listings_category')
                ]}
            />
        </div>
    );
};

export default GeneralTab;
