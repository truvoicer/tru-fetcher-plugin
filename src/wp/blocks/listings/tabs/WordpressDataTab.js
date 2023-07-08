import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {
    findPostTypeIdIdentifier,
    findPostTypeSelectOptions, findTaxonomyIdIdentifier,
} from "../../../helpers/wp-helpers";

const WordpressDataTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        config
    } = props;

    const itemListId = findPostTypeIdIdentifier('trf_item_list')
    return (
        <div>
            <SelectControl
                label="Item List"
                onChange={(value) => {
                    setAttributes({[itemListId]: value});
                }}
                value={attributes?.[itemListId]}
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
