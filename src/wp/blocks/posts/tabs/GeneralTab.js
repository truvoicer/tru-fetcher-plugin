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
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={attributes?.heading}
                onChange={(value) => {
                    setAttributes({heading: value});
                }}
            />
            <SelectControl
                label="Load More Type"
                onChange={(value) => {
                    setAttributes({load_more_type: value});
                }}
                value={attributes?.load_more_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Pagination',
                        value: 'pagination'
                    },
                    {
                        label: 'Infinite Scroll',
                        value: 'infinite_scroll'
                    },
                ]}
            />
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
            <ToggleControl
                label="Show Sidebar?"
                checked={attributes?.show_sidebar}
                onChange={(value) => {
                    setAttributes({show_sidebar: value});
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
