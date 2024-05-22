import {useState, useEffect} from "@wordpress/element";
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import ProviderRequestList from "../../components/list/ProviderRequestList";
import ProviderRequestContext, {providerRequestData} from "../../components/list/ProviderRequestContext";

const ApiTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);

    function updateProviderRequestData(updateData) {
        setProviderRequestState(prevState => {
            let cloneState = {...prevState};
            Object.keys(updateData).forEach((key) => {
                cloneState[key] = updateData[key];
            });
            return cloneState;
        })
    }

    const [providerRequestState, setProviderRequestState] = useState({
        ...providerRequestData,
        update: updateProviderRequestData
    });

    async function serviceListRequest() {
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/list`,
        });
        if (Array.isArray(results?.data?.data?.services)) {
            updateProviderRequestData({services: results.data.data.services})
        }
    }

    async function categoryListRequest() {
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.categories}`,
        });
        if (Array.isArray(results?.data?.data?.categories)) {
            updateProviderRequestData({categories: results.data.data.categories})
        }
    }

    async function providerListRequest(serviceName) {
        if (!serviceName) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${serviceName}/providers`,
        });
        if (Array.isArray(results?.data?.data?.providers)) {
            updateProviderRequestData({providers: results.data.data.providers})
        }
    }

    useEffect(() => {
        providerListRequest(providerRequestState.selectedService);
    }, [providerRequestState.selectedService]);
    useEffect(() => {
        providerListRequest(attributes?.api_listings_service);
    }, [attributes?.api_listings_service]);

    useEffect(() => {
        if (!Array.isArray(providerRequestState.services) || providerRequestState.services.length === 0) {
            return;
        }
        updateProviderRequestData({
            selectedService: providerRequestState.services.find((service) => parseInt(service.id) === parseInt(attributes.api_listings_service))
        })
    }, [providerRequestState.services]);

    useEffect(() => {
        serviceListRequest();
        categoryListRequest();
    }, []);

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

    return (
        <ProviderRequestContext.Provider value={providerRequestState}>
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
                    ...[
                        {
                            disabled: false,
                            label: 'Select a category',
                            value: ''
                        },
                    ],
                    ...buildSelectOptions(providerRequestState.categories, 'label', 'name')
                ]}
            />
            <SelectControl
                label="Api Listings Service"
                onChange={(value) => {
                    setAttributes({api_listings_service: value});
                    updateProviderRequestData({
                        selectedService: providerRequestState.services.find((service) => parseInt(service.id) === parseInt(value))
                    })
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
                    ...buildSelectOptions(providerRequestState.services, 'label', 'name')
                ]}
            />
            {Array.isArray(providerRequestState?.providers) && providerRequestState.providers.length > 0 &&
                <>
                    <ToggleControl
                        label="Select Providers"
                        checked={attributes?.select_providers}
                        onChange={(value) => {
                            setAttributes({select_providers: value});
                        }}
                    />
                    {attributes?.select_providers &&
                        <ProviderRequestList
                            reducers={props?.reducers}
                            data={attributes?.providers_list || []}
                            onChange={(value) => {
                                setAttributes({providers_list: value});
                            }}
                        />
                    }
                </>
            }
        </ProviderRequestContext.Provider>
    );
};

export default ApiTab;
