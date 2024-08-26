import React from 'react';
import {TextControl, SelectControl} from "@wordpress/components";
import Grid from "../../../../../components/Grid";

const FormLayoutTab = (props) => {
    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <Grid columns={2}>
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
                    label="Align"
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
            </Grid>
            <Grid columns={2}>
                <TextControl
                    label="Classes"
                    placeholder="Classes"
                    value={data?.classes}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'classes', value: value});
                        }
                    }}
                />
                <TextControl
                    label="Column Size"
                    placeholder="Column Size"
                    value={data?.column_size}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'column_size', value: value});
                        }
                    }}
                />
            </Grid>

        </div>
    );
};

export default FormLayoutTab;
