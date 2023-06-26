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
        </div>
    );
};

export default GeneralTab;
