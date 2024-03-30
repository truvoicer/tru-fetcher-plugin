import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {fetchRequest} from "../../../../library/api/middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";

const ApiTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    function getFetcherServices() {
        return apiConfig?.tru_fetcher?.services;
    }
    function getFetcherCategories() {
        return apiConfig?.tru_fetcher?.categories;
    }
    function buildSelectOptions(data, labelKey = 'label', valueKey = 'id') {
        if (!Array.isArray(data)) {
            return [];
        }
        return data.map((category) => {
            return {
                label: category[labelKey],
                value: category[valueKey]
            }
        })
    }
    function getFetcherProviders() {
        return apiConfig?.tru_fetcher?.providers;
    }
    function getApiListingsProviderOptions() {
        const fetcherProviders = getFetcherProviders();
        return fetcherProviders.map((provider) => {
            return {
                label: provider.label,
                value: provider.name
            }
        })
    }
    console.log({attributes})
    return (
        <div>
            <SelectControl
                label="Api Fetch Type"
                onChange={(value) => {
                    setAttributes({listing_block_type: value});
                }}
                value={attributes?.listing_block_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Search',
                        value: 'search'
                    },
                    {
                        label: 'Blog',
                        value: 'blog'
                    },
                ]}
            />
            <SelectControl
                label="Api Listings Service"
                onChange={(value) => {
                    setAttributes({api_listings_service: value});
                }}
                value={attributes?.api_listings_service}
                options={[
                    ...[
                    {
                        disabled: false,
                        label: 'Select a service',
                        value: ''
                    },
                ],
                    ...buildSelectOptions(getFetcherServices(), 'label', 'id')
            ]}
            />
            <SelectControl
                label="Api Listings Category"
                onChange={(value) => {
                    setAttributes({api_listings_category: value});
                }}
                value={attributes?.api_listings_category}
                options={[
                    ...[
                    {
                        disabled: false,
                        label: 'Select a category',
                        value: ''
                    },
                ],
                    ...buildSelectOptions(getFetcherCategories(), 'label', 'name')
            ]}
            />
            <ToggleControl
                label="Select Providers"
                checked={attributes?.select_providers}
                onChange={(value) => {
                    setAttributes({select_providers: value});
                }}
            />
            {attributes?.select_providers &&
                <SelectControl
                    label="Providers"
                    multiple={true}
                    onChange={(value) => {
                        setAttributes({providers_list: value});
                    }}
                    value={attributes?.providers_list}
                    options={[
                        ...[
                            {
                                disabled: true,
                                label: 'Select an Option',
                                value: ''
                            },
                        ],
                        ...getApiListingsProviderOptions()
                    ]}
                />
            }
        </div>
    );
};

export default ApiTab;
