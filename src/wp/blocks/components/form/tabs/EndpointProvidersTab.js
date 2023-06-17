import React from 'react';
import {TextControl, SelectControl, Button} from "@wordpress/components";

const EndpointProvidersTab = (props) => {
    const {
        data,
        onChange
    } = props;

    function addEndpointProvider() {
        let cloneData = {...data};
        let cloneEndpointProviders = [...cloneData.endpoint_providers];
        cloneEndpointProviders.push({form_field_mapping: []});
        onChange({key: 'endpoint_providers', value: cloneEndpointProviders});
    }
    function addProviderFieldMapping({rowIndex}) {
        let cloneData = {...data};
        let cloneEndpointProviders = [...cloneData.endpoint_providers];
        let cloneEndpointProviderItem = {...cloneEndpointProviders[rowIndex]};
        cloneEndpointProviderItem.form_field_mapping.push({
            form_field_name: '',
            provider_field_name: ''
        });
        cloneEndpointProviders[rowIndex] = cloneEndpointProviderItem;
        onChange({key: 'endpoint_providers', value: cloneEndpointProviders});
    }
    function updateProvider({providerIndex, value}) {
        let cloneData = {...data};
        let cloneEndpointProviders = [...cloneData.endpoint_providers];
        let cloneEndpointProviderItem = {...cloneEndpointProviders[providerIndex]};
        cloneEndpointProviderItem.provider = value;
        cloneEndpointProviders[providerIndex] = cloneEndpointProviderItem;
        onChange({key: 'endpoint_providers', value: cloneEndpointProviders});
    }
    function updateProviderMapping({providerIndex, formFieldMapIndex, field, value}) {
        let cloneData = {...data};
        let cloneEndpointProviders = [...cloneData.endpoint_providers];
        let cloneEndpointProviderItem = {...cloneEndpointProviders[providerIndex]};
        cloneEndpointProviderItem.form_field_mapping[formFieldMapIndex][field] = value;
        cloneEndpointProviders[providerIndex] = cloneEndpointProviderItem;
        onChange({key: 'endpoint_providers', value: cloneEndpointProviders});
    }

    return (
        <div>
            {data?.endpoint_providers.map((provider, providerIndex) => {
                return (
                    <>
                        <SelectControl
                            label="Provider"
                            onChange={(value) => {
                                updateProvider({providerIndex, value})
                            }}
                            value={provider?.provider}
                            options={[
                                {
                                    disabled: true,
                                    label: 'Select an Option',
                                    value: ''
                                },
                                {
                                    label: 'Hubspot',
                                    value: 'hubspot'
                                },
                            ]}
                        />

                        {provider?.form_field_mapping.map((formFieldMap, formFieldMapIndex) => {
                            return (
                                <>
                                    <TextControl
                                        placeholder="Form Field Name"
                                        value={formFieldMap?.form_field_name}
                                        onChange={(value) => {
                                            updateProviderMapping({
                                                providerIndex,
                                                formFieldMapIndex,
                                                field: 'form_field_name',
                                                value
                                            })
                                        }}
                                    />
                                    <TextControl
                                        placeholder="Provider Field Name"
                                        value={formFieldMap?.provider_field_name}
                                        onChange={(value) => {
                                            updateProviderMapping({
                                                providerIndex,
                                                formFieldMapIndex,
                                                field: 'provider_field_name',
                                                value
                                            })
                                        }}
                                    />
                                </>
                            )
                        })}
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                addEndpointProvider()
                            }}
                        >
                            Add Field Mapping
                        </Button>
                    </>
                )
            })}
            <Button
                variant="primary"
                onClick={(e) => {
                    e.preventDefault()
                    addEndpointProvider()
                }}
            >
                Add Provider
            </Button>
        </div>
    );
};

export default EndpointProvidersTab;
