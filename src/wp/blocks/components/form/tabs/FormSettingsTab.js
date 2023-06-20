import React from 'react';
import {TextControl, SelectControl} from "@wordpress/components";

const FormSettingsTab = (props) => {
    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <SelectControl
                label="Form Type"
                onChange={(value) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'form_type', value: value});
                    }
                }}
                value={data?.form_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Single',
                        value: 'single'
                    },
                    {
                        label: 'List',
                        value: 'list'
                    },
                ]}
            />
            <SelectControl
                label="Method"
                onChange={(value) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'method', value: value});
                    }
                }}
                value={data?.method}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'GET',
                        value: 'get'
                    },
                    {
                        label: 'POST',
                        value: 'post'
                    },
                ]}
            />
            <TextControl
                label="Submit Button Label"
                placeholder="Submit Button Label"
                value={ data?.submit_button_label }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'submit_button_label', value: value});
                    }
                } }
            />
            <TextControl
                label="Add Item Button Label"
                placeholder="Add Item Button Label"
                value={ data?.add_item_button_label }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'add_item_button_label', value: value});
                    }
                } }
            />
            <TextControl
                label="Form ID"
                placeholder="Form ID"
                value={ data?.form_id }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'form_id', value: value});
                    }
                } }
            />
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={ data?.heading }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'heading', value: value});
                    }
                } }
            />
            <TextControl
                label="Sub Heading"
                placeholder="Sub Heading"
                value={ data?.sub_heading }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'sub_heading', value: value});
                    }
                } }
            />
        </div>
    );
};

export default FormSettingsTab;
