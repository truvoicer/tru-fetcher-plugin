import React from 'react';
import {TabPanel, Panel, RangeControl, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {
    const {
        data = [],
        onChange
    } = props;
    return (
        <div>
            <SelectControl
                label="Tabs Block Type"
                onChange={(value) => {
                    onChange({key: 'tabs_block_type', value: value});
                }}
                value={data?.tabs_block_type}
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
                    onChange({key: 'tabs_orientation', value: value});
                }}
                value={data?.tabs_orientation}
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
                label="Heading"
                placeholder="Heading"
                value={data?.heading}
                onChange={(value) => {
                    onChange({key: 'heading', value: value});
                }}
            />
            <TextControl
                label="Sub Heading"
                placeholder="Sub Heading"
                value={data?.sub_heading}
                onChange={(value) => {
                    onChange({key: 'sub_heading', value: value});
                }}
            />
            <TextControl
                label="CTA"
                placeholder="CTA"
                value={data?.cta}
                onChange={(value) => {
                    onChange({key: 'cta', value: value});
                }}
            />

        </div>
    );
};

export default GeneralTab;
