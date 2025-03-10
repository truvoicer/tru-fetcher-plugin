import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Carousel from "../../carousel/Carousel";
import {findPostTypeIdIdentifier, findPostTypeSelectOptions} from "../../../../helpers/wp-helpers";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

const GeneralTab = (props) => {

    const {
        data,
        onChange
    } = props;

    const filterListId = findPostTypeIdIdentifier('trf_filter_list')
    return (
        <div>
            <SelectControl
                label="Type"
                onChange={(value) => {
                    onChange({key: 'type', value: value});
                }}
                value={data?.type}
                options={GutenbergBase.getSelectOptions('type', props)}
            />
            <TextControl
                label="Name"
                placeholder="Name"
                value={data?.name}
                onChange={(value) => {
                    onChange({key: 'name', value: value});
                }}
            />
            <TextControl
                label="Label"
                placeholder="Label"
                value={data?.label}
                onChange={(value) => {
                    onChange({key: 'label', value: value});
                }}
            />
        </div>
    );
};

export default GeneralTab;
