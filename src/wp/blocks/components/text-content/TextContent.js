import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const TextContent = (props) => {

    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <TextControl
                placeholder="Content"
                value={data?.content}
                onChange={(value) => {
                    onChange({key: 'content', value: value});
                }}
            />
        </div>
    );
};

export default TextContent;
