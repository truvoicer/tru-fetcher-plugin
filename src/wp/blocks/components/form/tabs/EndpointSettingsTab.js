import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Grid from "../../../../../components/Grid";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

const EndpointSettingsTab = (props) => {
    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <Grid columns={2}>
                <SelectControl
                    label="Endpoint"
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'endpoint', value: value});
                        }
                    }}
                    value={data?.endpoint}
                    options={GutenbergBase.getSelectOptions('endpoint', props)}
                />
                <SelectControl
                    label="Endpoint Type"
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'endpoint_type', value: value});
                        }
                    }}
                    value={data?.endpoint_type}
                    options={GutenbergBase.getSelectOptions('endpoint_type', props)}
                />
            </Grid>
            <Grid columns={2}>
                <ToggleControl
                    label="Redirect?"
                    checked={data?.redirect}
                    onChange={(value) => {
                        onChange({key: 'redirect', value: value});
                    }}
                />
                {data?.redirect && (
                    <TextControl
                        label="Redirect URL"
                        placeholder="Redirect URL"
                        value={data?.redirect_url}
                        onChange={(value) => {
                            if (typeof onChange === 'function') {
                                onChange({key: 'redirect_url', value: value});
                            }
                        }}
                    />
                )}
            </Grid>
            <Grid columns={2}>
                <ToggleControl
                    label="Fetch User Data?"
                    checked={data?.fetch_user_data}
                    onChange={(value) => {
                        onChange({key: 'fetch_user_data', value: value});
                    }}
                />
            </Grid>
            {data?.endpoint === 'custom' && (
                <Grid columns={1}>
                    <TextControl
                        label="Custom Endpoint"
                        placeholder="Custom Endpoint"
                        value={data?.custom_endpoint}
                        onChange={(value) => {
                            if (typeof onChange === 'function') {
                                onChange({key: 'custom_endpoint', value: value});
                            }
                        }}
                    />
                </Grid>
            )}
            {data?.endpoint === 'email' && (
                <>
                    <div>
                        <h5>Email Settings</h5>
                        <Grid columns={2}>
                            <TextControl
                                label="Email Recipient"
                                placeholder="Email Recipient"
                                value={data?.email_recipient}
                                onChange={(value) => {
                                    if (typeof onChange === 'function') {
                                        onChange({key: 'email_recipient', value: value});
                                    }
                                }}
                            />
                            <TextControl
                                label="Email Subject"
                                placeholder="Email Subject"
                                value={data?.email_subject}
                                onChange={(value) => {
                                    if (typeof onChange === 'function') {
                                        onChange({key: 'email_subject', value: value});
                                    }
                                }}
                            />
                        </Grid>
                        <Grid columns={2}>
                            <TextControl
                                label="Email From"
                                placeholder="Email From"
                                value={data?.email_from}
                                onChange={(value) => {
                                    if (typeof onChange === 'function') {
                                        onChange({key: 'email_from', value: value});
                                    }
                                }}
                            />
                        </Grid>
                    </div>
                </>
            )}
        </div>
    );
};

export default EndpointSettingsTab;
