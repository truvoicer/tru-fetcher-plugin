import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {findSingleItemPostsSelectOptions, getListingsCategoryTermsSelectOptions} from "../../../helpers/wp-helpers";

const WordpressDataTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        config
    } = props;

    return (
        <div>
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
            <SelectControl
                label="Item List"
                onChange={(value) => {
                    setAttributes({item_list: value});
                }}
                value={attributes?.item_list}
                options={[
                    ...[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                ],
                    ...findSingleItemPostsSelectOptions()
            ]}
            />
        </div>
    );
};

export default WordpressDataTab;
