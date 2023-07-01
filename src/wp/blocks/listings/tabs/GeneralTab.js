import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {getListingsCategoryTermsSelectOptions} from "../../../helpers/wp-helpers";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
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
                    setAttributes({listings_category: value});
                }}
                value={attributes?.listings_category}
                options={[
                    ...[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                    ],
                    ...getListingsCategoryTermsSelectOptions()
                ]}
            />
        </div>
    );
};

export default GeneralTab;
