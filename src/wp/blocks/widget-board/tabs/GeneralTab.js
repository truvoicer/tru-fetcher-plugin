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
            <ToggleControl
                label="Show Sidebar?"
                checked={attributes?.show_sidebar}
                onChange={(value) => {
                    setAttributes({show_sidebar: value});
                }}
            />
        </div>
    );
};

export default GeneralTab;
