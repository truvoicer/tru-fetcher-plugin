import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const EndpointSettingsTab = (props) => {
    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <SelectControl
                label="Endpoint"
                onChange={(value) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'endpoint', value: value});
                    }
                }}
                value={data?.endpoint}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Email',
                        value: 'email'
                    },
                    {
                        label: 'User Meta',
                        value: 'user_meta'
                    },
                    {
                        label: 'User Profile',
                        value: 'user_profile'
                    },
                    {
                        label: 'Account Details',
                        value: 'account_details'
                    },
                    {
                        label: 'Redirect',
                        value: 'redirect'
                    },
                    {
                        label: 'Custom',
                        value: 'custom'
                    },
                ]}
            />
            <SelectControl
                label="Endpoint Type"
                onChange={(value) => {
                    if (typeof onChange === 'function') {
                        onChange({key: 'endpoint_type', value: value});
                    }
                }}
                value={data?.endpoint_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Public',
                        value: 'public'
                    },
                    {
                        label: 'Protected',
                        value: 'protected'
                    },
                ]}
            />
            <ToggleControl
                label="Redirect?"
                checked={data?.redirect}
                onChange={(value) => {
                    onChange({key: 'redirect', value: value});
                }}
            />
            {data?.endpoint === 'custom' && (
                <TextControl
                    placeholder="Custom Endpoint"
                    value={data?.custom_endpoint}
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'custom_endpoint', value: value});
                        }
                    }}
                />
            )}
            {data?.endpoint === 'email' && (
                <>
                    <div>
                        <h5>Email Settings</h5>
                        <TextControl
                            placeholder="Email Recipient"
                            value={data?.email_recipient}
                            onChange={(value) => {
                                if (typeof onChange === 'function') {
                                    onChange({key: 'email_recipient', value: value});
                                }
                            }}
                        />
                        <TextControl
                            placeholder="Email Subject"
                            value={data?.email_subject}
                            onChange={(value) => {
                                if (typeof onChange === 'function') {
                                    onChange({key: 'email_subject', value: value});
                                }
                            }}
                        />
                        <TextControl
                            placeholder="Email From"
                            value={data?.email_from}
                            onChange={(value) => {
                                if (typeof onChange === 'function') {
                                    onChange({key: 'email_from', value: value});
                                }
                            }}
                        />
                    </div>
                </>
            )}
        </div>
    );
};

export default EndpointSettingsTab;
