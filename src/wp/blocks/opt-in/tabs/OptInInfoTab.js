import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    return (
        <div>
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={ attributes?.heading }
                onChange={ ( value ) => {
                    setAttributes({heading: value});
                } }
            />
            <TextControl
                label="Text"
                placeholder="Text"
                value={ attributes?.text }
                onChange={ ( value ) => {
                    setAttributes({text: value});
                } }
            />
        </div>
    );
};

export default GeneralTab;
