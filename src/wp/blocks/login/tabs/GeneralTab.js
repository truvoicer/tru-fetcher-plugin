import React from 'react';
import Grid from '../../../../components/Grid';
import { SelectControl, TextControl } from '@wordpress/components';
import { GutenbergBase } from '../../../helpers/gutenberg/gutenberg-base';
import { useDispatch, useSelect } from '@wordpress/data';
import { getChildBlockParams } from '../../../helpers/wp-helpers';
import { store as blockEditorStore } from '@wordpress/block-editor';
import FormComponent from '../../components/form/FormComponent';

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        clientId,
    } = props;

    const { updateBlockAttributes } = useDispatch(blockEditorStore);

    const { rootClientId, hasParents, parentAttributes } = useSelect(
        (select) => {
            return getChildBlockParams({ blockEditorStore, select, clientId });
        },
        [clientId]
    );

    let cloneFormDataAtts = {};
    if (typeof attributes?.form_data === 'object') {
        cloneFormDataAtts = { ...attributes.form_data };
    }

    function formChangeHandler({ key, value }) {
        const newAttributes = {
            ...cloneFormDataAtts,
            [key]: value
        };
        setAttributes({ form_data: newAttributes });

        if (hasParents) {
            updateBlockAttributes(rootClientId, {
                [props.config.id]: newAttributes,
            });
        }
    }
    return (
        <>
            <Grid columns={2}>
                <SelectControl
                    label="Form Type"
                    value={attributes?.form_type}
                    options={GutenbergBase.getSelectOptions('form_type', props)}
                    onChange={(value) => {
                        setAttributes({ form_type: value });
                    }}
                />
            </Grid>
            {attributes?.form_type === 'custom' && (
                <Grid columns={1}>
                    <FormComponent
                        {...props}
                        data={(hasParents && parentAttributes) ? parentAttributes : cloneFormDataAtts}
                        onChange={formChangeHandler}
                    />
                </Grid>
            )}
            {attributes?.form_type === 'default' && (
                <>
                    <Grid columns={2}>
                        <TextControl
                            label="Email Label"
                            placeholder="Email Label"
                            value={attributes?.email_label}
                            onChange={(value) => {
                                setAttributes({ email_label: value });
                            }}
                        />
                        <TextControl
                            label="Email Placeholder"
                            placeholder="Email Placeholder"
                            value={attributes?.email_placeholder}
                            onChange={(value) => {
                                setAttributes({ email_placeholder: value });
                            }}
                        />
                    </Grid>
                    <Grid columns={2}>
                        <TextControl
                            label="Username Label"
                            placeholder="Username Label"
                            value={attributes?.username_label}
                            onChange={(value) => {
                                setAttributes({ username_label: value });
                            }}
                        />
                        <TextControl
                            label="Username Placeholder"
                            placeholder="Username Placeholder"
                            value={attributes?.username_placeholder}
                            onChange={(value) => {
                                setAttributes({ username_placeholder: value });
                            }}
                        />
                    </Grid>
                    <Grid columns={2}>
                        <TextControl
                            label="Password Label"
                            placeholder="Password Label"
                            value={attributes?.password_label}
                            onChange={(value) => {
                                setAttributes({ password_label: value });
                            }}
                        />
                        <TextControl
                            label="Password Placeholder"
                            placeholder="Password Placeholder"
                            value={attributes?.password_placeholder}
                            onChange={(value) => {
                                setAttributes({ password_placeholder: value });
                            }} />
                    </Grid>
                    <Grid columns={2}>
                        <TextControl
                            label="Submit Text"
                            placeholder="Submit Text"
                            value={attributes?.submit_text}
                            onChange={(value) => {
                                setAttributes({ submit_text: value });
                            }}
                        />
                        <TextControl
                            label="Cancel Text"
                            placeholder="Cancel Text"
                            value={attributes?.cancel_text}
                            onChange={(value) => {
                                setAttributes({ cancel_text: value });
                            }}
                        />
                    </Grid>
                    <Grid columns={2}>
                        <TextControl
                            label="Forgot Password Text"
                            placeholder="Forgot Password Text"
                            value={attributes?.forgot_password_text}
                            onChange={(value) => {
                                setAttributes({ forgot_password_text: value });
                            }}
                        />
                        <TextControl
                            label="Success Message"
                            placeholder="Success Message"
                            value={attributes?.success_message}
                            onChange={(value) => {
                                setAttributes({ success_message: value });
                            }}
                        />
                    </Grid>
                </>
            )}
        </>
    )
};

export default GeneralTab;
