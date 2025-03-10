import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import { GutenbergBase } from '../../../helpers/gutenberg/gutenberg-base';

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
                label="Opt In Type"
                onChange={(value) => {
                    setAttributes({optin_type: value});
                }}
                value={attributes?.optin_type || 'form'}
                options={GutenbergBase.getSelectOptions('optin_type', props)}
            />
            <ToggleControl
                label="Show Carousel?"
                checked={attributes?.show_carousel}
                onChange={(value) => {
                    setAttributes({show_carousel: value});
                }}
            />
        </div>
    );
};

export default GeneralTab;
