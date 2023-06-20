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

    function addProviderFieldMapping({providerIndex}) {
        let cloneData = {...data};
        let cloneEndpointProviders = [...cloneData.endpoint_providers];
        let cloneEndpointProviderItem = {...cloneEndpointProviders[providerIndex]};
        cloneEndpointProviderItem.form_field_mapping.push({
            form_field_name: '',
            provider_field_name: ''
        });
        cloneEndpointProviders[providerIndex] = cloneEndpointProviderItem;
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
    function deleteProviderMapping({providerIndex, formFieldMapIndex}) {
        let cloneData = {...data};
        let cloneEndpointProviders = [...cloneData.endpoint_providers];
        let cloneEndpointProviderItem = {...cloneEndpointProviders[providerIndex]};
        let form_field_mapping = cloneEndpointProviderItem.form_field_mapping;
        form_field_mapping.splice(formFieldMapIndex, 1);
        cloneEndpointProviderItem.form_field_mapping = form_field_mapping;
        cloneEndpointProviders[providerIndex] = cloneEndpointProviderItem;
        onChange({key: 'endpoint_providers', value: cloneEndpointProviders});
    }

    return (
        <div className={'tf--form--rows'}>
            {data?.endpoint_providers.map((provider, providerIndex) => {
                return (
                    <div className={'tf--form--row tf--form--row--body'}>
                        <div>
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
                        </div>

                        <div className={'tf--form--row--endpoint-mappings'}>
                            {provider?.form_field_mapping.map((formFieldMap, formFieldMapIndex) => {
                                return (
                                    <div className={'tf--form--row--endpoint-mappings--item'}>
                                        <TextControl
                                            label="Form Field Name"
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
                                            label="Provider Field Name"
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
                                        <Button
                                            variant="primary"
                                            onClick={(e) => {
                                                e.preventDefault()
                                                deleteProviderMapping({
                                                    providerIndex,
                                                    formFieldMapIndex,
                                                })
                                            }}
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                )
                            })}
                            <Button
                                variant="primary"
                                onClick={(e) => {
                                    e.preventDefault()
                                    addProviderFieldMapping({providerIndex})
                                }}
                            >
                                Add Field Mapping
                            </Button>
                        </div>
                    </div>
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
