import React from 'react';
import {TextControl, SelectControl} from "@wordpress/components";

const FormLayoutTab = (props) => {
    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <SelectControl
                label="Layout Style"
                onChange={(value) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'layout_style', value: value});
                    }
                }}
                value={data?.layout_style}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Full Width',
                        value: 'full_width'
                    },
                    {
                        label: 'Contained',
                        value: 'contained'
                    },
                ]}
            />
            <SelectControl
                label="Method"
                onChange={(value) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'align', value: value});
                    }
                }}
                value={data?.align}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Left',
                        value: 'left'
                    },
                    {
                        label: 'Right',
                        value: 'right'
                    },
                    {
                        label: 'Center',
                        value: 'center'
                    },
                ]}
            />
            <TextControl
                placeholder="Classes"
                value={ data?.classes }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'classes', value: value});
                    }
                } }
            />
            <TextControl
                placeholder="Column Size"
                value={ data?.column_size }
                onChange={ ( value ) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'column_size', value: value});
                    }
                } }
            />

        </div>
    );
};

export default FormLayoutTab;
