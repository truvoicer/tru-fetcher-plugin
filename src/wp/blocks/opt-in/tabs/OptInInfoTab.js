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
                placeholder="Heading"
                value={ attributes?.heading }
                onChange={ ( value ) => {
                    setAttributes({heading: value});
                } }
            />
            <TextControl
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
