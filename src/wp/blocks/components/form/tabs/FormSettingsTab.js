import React from 'react';
import {TextControl, SelectControl} from "@wordpress/components";
import Grid from "../../../../../components/Grid";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

const FormSettingsTab = (props) => {
    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <Grid columns={2}>
                <SelectControl
                    label="Form Type"
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'form_type', value: value});
                        }
                    }}
                    value={data?.form_type}
                    options={GutenbergBase.getSelectOptions('form_type', props)}
                />
                <SelectControl
                    label="Method"
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'method', value: value});
                        }
                    }}
                    value={data?.method}
                    options={GutenbergBase.getSelectOptions('method', props)}
                />
            </Grid>
            <Grid columns={2}>
                <TextControl
                    label="Submit Button Label"
                    placeholder="Submit Button Label"
                    value={data?.submit_button_label}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'submit_button_label', value: value});
                        }
                    }}
                />
                <TextControl
                    label="Add Item Button Label"
                    placeholder="Add Item Button Label"
                    value={data?.add_item_button_label}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'add_item_button_label', value: value});
                        }
                    }}
                />
            </Grid>
            <Grid columns={2}>
                <TextControl
                    label="Form ID"
                    placeholder="Form ID"
                    value={data?.form_id}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'form_id', value: value});
                        }
                    }}
                />
                <TextControl
                    label="Heading"
                    placeholder="Heading"
                    value={data?.heading}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'heading', value: value});
                        }
                    }}
                />
            </Grid>
            <Grid columns={2}>
                <TextControl
                    label="Sub Heading"
                    placeholder="Sub Heading"
                    value={data?.sub_heading}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'sub_heading', value: value});
                        }
                    }}
                />
            </Grid>
        </div>
    );
};

export default FormSettingsTab;
