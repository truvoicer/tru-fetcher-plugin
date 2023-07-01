import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {
    findPostTypeSelectOptions,
    findSingleItemPostsSelectOptions,
    getListingsCategoryTermsSelectOptions
} from "../../../helpers/wp-helpers";

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
                    ...findPostTypeSelectOptions('trf_item_list')
            ]}
            />
        </div>
    );
};

export default WordpressDataTab;
