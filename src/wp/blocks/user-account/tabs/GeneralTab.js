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
                label="Component"
                onChange={(value) => {
                    setAttributes({component: value});
                }}
                value={attributes?.component}
                options={GutenbergBase.getSelectOptions('component', props)}
            />
            <TextControl
                label="Tab Label"
                placeholder="Tab Label"
                value={ attributes?.tab_label }
                onChange={ ( value ) => {
                    setAttributes({tab_label: value});
                } }
            />
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={ attributes?.heading }
                onChange={ ( value ) => {
                    setAttributes({heading: value});
                } }
            />
            <SelectControl
                label="Tabs Orientation"
                onChange={(value) => {
                    setAttributes({tabs_orientation: value});
                }}
                value={attributes?.tabs_orientation}
                options={GutenbergBase.getSelectOptions('tabs_orientation', props)}
            />
        </div>
    );
};

export default GeneralTab;
