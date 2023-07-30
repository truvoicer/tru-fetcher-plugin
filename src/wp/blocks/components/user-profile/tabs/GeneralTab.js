import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {

    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={data?.heading}
                onChange={(value) => {
                    onChange({key: 'heading', value: value});
                }}
            />
            <SelectControl
                label="View"
                onChange={(value) => {
                    onChange({key: 'view', value: value});
                }}
                value={data?.view}
                options={[
                    {
                        label: 'Display',
                        value: 'display'
                    },
                    {
                        label: 'Edit',
                        value: 'edit'
                    },
                ]}
            />
        </div>
    );
};

export default GeneralTab;
