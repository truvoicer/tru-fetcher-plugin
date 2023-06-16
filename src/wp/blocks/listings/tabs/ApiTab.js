import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {fetchRequest} from "../../../../library/api/middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";

const ApiTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    async function getFetcherCategories() {
        const results = await fetchRequest({
            config: fetcherApiConfig,
            endpoint: fetcherApiConfig.endpoints.categories,
            apiConfig: apiConfig?.tru_fetcher
        })
        if (results?.data?.data) {
            return results.data.data;
        } else {
            return [];
        }
    }
    async function getApiListingsCategoryOptions() {
        const fetchercategories = await getFetcherCategories();
        console.log({fetchercategories})
        return fetchercategories.map((category) => {
            return {
                label: category.category_label,
                value: category.id
            }
        })
    }
    const fetchercategories = getApiListingsCategoryOptions();
    console.log({fetchercategories})
    return (
        <PanelRow>
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
                label="Api Listings Category"
                onChange={(value) => {
                    setAttributes({api_listings_category: value});
                }}
                value={attributes?.api_listings_category}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                ]}
            />
            <ToggleControl
                label="Select Providers"
                checked={attributes?.select_providers}
                onChange={(value) => {
                    setAttributes({select_providers: value});
                }}
            />
        </PanelRow>
    );
};

export default ApiTab;
