import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Carousel from "../../carousel/Carousel";
import {findPostTypeIdIdentifier, findPostTypeSelectOptions} from "../../../../helpers/wp-helpers";

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
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Date',
                        value: 'date'
                    },
                    {
                        label: 'Text',
                        value: 'text'
                    },
                    {
                        label: 'List',
                        value: 'list'
                    },
                ]}
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
