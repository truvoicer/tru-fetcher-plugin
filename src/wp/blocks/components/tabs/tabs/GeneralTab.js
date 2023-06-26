import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {

    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <ToggleControl
                label="List Start"
                checked={data?.default_active_tab}
                onChange={(value) => {
                    onChange({key: 'default_active_tab', value: value});
                }}
            />
            <SelectControl
                label="Custom Tabs Type"
                onChange={(value) => {
                    onChange({key: 'custom_tabs_type', value: value});
                }}
                value={data?.custom_tabs_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Custom Carousel',
                        value: 'custom_carousel'
                    },
                    {
                        label: 'Custom Content',
                        value: 'custom_content'
                    },
                    {
                        label: 'Form',
                        value: 'form'
                    },
                ]}
            />
            <TextControl
                label="Tab ID"
                placeholder="Tab ID"
                value={data?.tab_id}
                onChange={(value) => {
                    onChange({key: 'tab_id', value: value});
                }}
            />
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={data?.tab_heading}
                onChange={(value) => {
                    onChange({key: 'tab_heading', value: value});
                }}
            />
        </div>
    );
};

export default GeneralTab;
