import React from 'react';
import {TabPanel, Panel, RangeControl, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {findTaxonomySelectOptions} from "../../../helpers/wp-helpers";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    return (
        <div>
            <SelectControl
                label="Tabs Block Type"
                onChange={(value) => {
                    setAttributes({tabs_block_type: value});
                }}
                value={attributes?.tabs_block_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Request Carousel Tabs',
                        value: 'request_carousel_tabs'
                    },
                    {
                        label: 'Request Video Tabs',
                        value: 'request_video_tabs'
                    },
                    {
                        label: 'Custom Tabs',
                        value: 'custom_tabs'
                    },
                ]}
            />
            <SelectControl
                label="Tabs Orientation"
                onChange={(value) => {
                    setAttributes({tabs_orientation: value});
                }}
                value={attributes?.tabs_orientation}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Vertical',
                        value: 'vertical'
                    },
                    {
                        label: 'Horizontal',
                        value: 'horizontal'
                    },
                ]}
            />
            <TextControl
                placeholder="Heading"
                value={attributes?.heading}
                onChange={(value) => {
                    setAttributes({heading: value});
                }}
            />
            <TextControl
                placeholder="Sub Heading"
                value={attributes?.sub_heading}
                onChange={(value) => {
                    setAttributes({sub_heading: value});
                }}
            />
            <TextControl
                placeholder="CTA"
                value={attributes?.cta}
                onChange={(value) => {
                    setAttributes({cta: value});
                }}
            />

        </div>
    );
};

export default GeneralTab;
