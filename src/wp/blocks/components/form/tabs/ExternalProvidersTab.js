import React from 'react';
import {TextControl, SelectControl, Button} from "@wordpress/components";

const ExternalProvidersTab = (props) => {
    const {
        data,
        onChange
    } = props;

    function addExternalProvider() {
        let cloneData = {...data};
        let cloneExternalProviders = [...cloneData.external_providers];
        cloneExternalProviders.push({form_field_mapping: []});
        onChange({key: 'external_providers', value: cloneExternalProviders});
    }

    function addProviderFieldMapping({providerIndex}) {
        let cloneData = {...data};
        let cloneExternalProviders = [...cloneData.external_providers];
        let cloneExternalProviderItem = {...cloneExternalProviders[providerIndex]};
        cloneExternalProviderItem.form_field_mapping.push({
            form_field_name: '',
            provider_field_name: ''
        });
        cloneExternalProviders[providerIndex] = cloneExternalProviderItem;
        onChange({key: 'external_providers', value: cloneExternalProviders});
    }

    function updateProvider({providerIndex, value}) {
        let cloneData = {...data};
        let cloneExternalProviders = [...cloneData.external_providers];
        let cloneExternalProviderItem = {...cloneExternalProviders[providerIndex]};
        cloneExternalProviderItem.provider = value;
        cloneExternalProviders[providerIndex] = cloneExternalProviderItem;
        onChange({key: 'external_providers', value: cloneExternalProviders});
    }

    function updateProviderMapping({providerIndex, formFieldMapIndex, field, value}) {
        let cloneData = {...data};
        let cloneExternalProviders = [...cloneData.external_providers];
        let cloneExternalProviderItem = {...cloneExternalProviders[providerIndex]};
        cloneExternalProviderItem.form_field_mapping[formFieldMapIndex][field] = value;
        cloneExternalProviders[providerIndex] = cloneExternalProviderItem;
        onChange({key: 'external_providers', value: cloneExternalProviders});
    }
    function deleteProviderMapping({providerIndex, formFieldMapIndex}) {
        let cloneData = {...data};
        let cloneExternalProviders = [...cloneData.external_providers];
        let cloneExternalProviderItem = {...cloneExternalProviders[providerIndex]};
        let form_field_mapping = cloneExternalProviderItem.form_field_mapping;
        form_field_mapping.splice(formFieldMapIndex, 1);
        cloneExternalProviderItem.form_field_mapping = form_field_mapping;
        cloneExternalProviders[providerIndex] = cloneExternalProviderItem;
        onChange({key: 'external_providers', value: cloneExternalProviders});
    }

    return (
        <div className={'tf--form--rows'}>
            {data?.external_providers.map((provider, providerIndex) => {
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
                    addExternalProvider()
                }}
            >
                Add Provider
            </Button>
        </div>
    );
};

export default ExternalProvidersTab;
