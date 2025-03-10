import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

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
                options={GutenbergBase.getSelectOptions('view', props)}
            />
        </div>
    );
};

export default GeneralTab;
