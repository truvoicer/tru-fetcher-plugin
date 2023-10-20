import React from 'react';
import {TabPanel, Panel, RangeControl, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {findTaxonomyIdIdentifier, findTaxonomySelectOptions} from "../../../helpers/wp-helpers";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    const categoryId = findTaxonomyIdIdentifier('category')
    return (
        <div>
            <RangeControl
                label="Posts Per Page"
                initialPosition={50}
                max={100}
                min={0}
                value={attributes?.posts_per_page}
                onChange={(value) => setAttributes({posts_per_page: value})}
            />
            <ToggleControl
                label="Show Carousel?"
                checked={attributes?.show_all_categories}
                onChange={(value) => {
                    setAttributes({show_all_categories: value});
                }}
            />
            <SelectControl
                label="Post Categories To Display"
                onChange={(value) => {
                    setAttributes({[categoryId]: value});
                }}
                multiple={true}
                value={attributes?.[categoryId]}
                options={[
                    ...[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                    ],
                    ...findTaxonomySelectOptions('category')
                ]}
            />
        </div>
    );
};

export default GeneralTab;
