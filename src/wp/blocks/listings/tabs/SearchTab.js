import React from 'react';
import {useState, useEffect, useContext} from "@wordpress/element";
import {TabPanel, Button, TextControl, RangeControl, SelectControl, ToggleControl} from "@wordpress/components";
import {addParam, updateParam} from "../../../helpers/wp-helpers";
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import {buildSelectOptions} from "../../../../library/helpers/form-helpers";
import ProviderRequestContext from "../../components/list/ProviderRequestContext";
import Grid from "../../../../components/Grid";

const SearchTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;

    const [dataKeysOptions, setDataKeysOptions] = useState([]);

    const providerRequestContext = useContext(ProviderRequestContext);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);

    async function dataKeysRequest(selectedService) {
        if (!isNotEmpty(selectedService)) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${selectedService}/response-key/list`,
            params: {
                pagination: false,
            }
        });
        if (Array.isArray(results?.data?.data?.service_response_keys)) {
            setDataKeysOptions(results.data.data.service_response_keys);
        }
    }

    useEffect(() => {
        dataKeysRequest(
            providerRequestContext.services.find((service) => service?.name === attributes.api_listings_service)?.id
        );
    }, [attributes?.api_listings_service]);

    return (
        <>
            <Grid colspan={2}>
                <SelectControl
                    label="Initial Load"
                    onChange={(value) => {
                        setAttributes({initial_load: value});
                    }}
                    value={attributes?.initial_load}
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
                            label: 'Api Request',
                            value: 'api_request'
                        },
                    ]}
                />
                {attributes?.initial_load === 'search' && (
                    <TextControl
                        label="Initial Search Term"
                        placeholder="Initial Search Term"
                        value={attributes?.initial_search_term}
                        onChange={(value) => {
                            setAttributes({initial_search_term: value})
                        }}
                    />
                )}
            </Grid>
            {attributes?.source === 'api' && (
                <>
                    <Grid colspan={2}>
                        <SelectControl
                            label="Sort by"
                            onChange={(value) => {
                                setAttributes({sort_by: value});
                            }}
                            value={attributes?.sort_by}
                            options={[
                                ...[
                                    {
                                        disabled: false,
                                        label: 'Select a key',
                                        value: ''
                                    },
                                ],
                                ...buildSelectOptions(dataKeysOptions, 'name', 'name')
                            ]}
                        />
                        <SelectControl
                            label="Sort order"
                            onChange={(value) => {
                                setAttributes({sort_order: value});
                            }}
                            value={attributes?.sort_order}
                            options={[
                                {
                                    label: 'Descending',
                                    value: 'desc'
                                },
                                {
                                    label: 'Ascending',
                                    value: 'asc'
                                },
                            ]}
                        />
                    </Grid>
                    <Grid colspan={2}>
                        <SelectControl
                            label="Date key"
                            onChange={(value) => {
                                setAttributes({date_key: value});
                            }}
                            value={attributes?.date_key}
                            options={[
                                ...[
                                    {
                                        disabled: false,
                                        label: 'Select a key',
                                        value: ''
                                    },
                                ],
                                ...buildSelectOptions(dataKeysOptions, 'name', 'name')
                            ]}
                        />
                    </Grid>
                </>
            )}
            {attributes?.initial_load === 'search' && (
                <>
                    <h5>Search Params</h5>
                    {Array.isArray(attributes?.initial_load_search_params) && attributes.initial_load_search_params.map((param, index) => {
                        return (
                            <div style={{display: 'flex'}}>
                                <TextControl
                                    label="Param Name"
                                    placeholder="Param Name"
                                    value={attributes.initial_load_search_params[index].name}
                                    onChange={(value) => {
                                        updateParam({
                                            attr: 'initial_load_search_params',
                                            index,
                                            key: 'name',
                                            value: value,
                                            attributes,
                                            setAttributes
                                        })
                                    }}
                                />

                                <TextControl
                                    label="Param Value"
                                    placeholder="Param Value"
                                    value={attributes?.initial_load_search_params[index].value}
                                    onChange={(value) => {
                                        updateParam({
                                            attr: 'initial_load_search_params',
                                            index,
                                            key: 'value',
                                            value: value,
                                            attributes,
                                            setAttributes
                                        })
                                    }}
                                />
                            </div>
                        );
                    })}
                    <Button
                        variant="primary"
                        onClick={(e) => {
                            e.preventDefault()
                            addParam({attr: 'initial_load_search_params', attributes, setAttributes})
                        }}
                    >
                        Add New
                    </Button>
                </>
            )}
            {attributes?.initial_load === 'api_request' && (
                <>
                    <Grid colspan={2}>
                        <TextControl
                            label="Request Name"
                            value={attributes?.initial_load_request_name}
                            onChange={(value) => setAttributes({initial_load_request_name: value})}
                        />
                        <RangeControl
                            label="Request Limit"
                            initialPosition={50}
                            max={100}
                            min={0}
                            value={attributes?.initial_load_request_limit}
                            onChange={(value) => setAttributes({initial_load_request_limit: value})}
                        />
                    </Grid>
                    <Grid colspan={1}>
                        <h5>Request Params</h5>
                        {Array.isArray(attributes?.initial_load_request_params) && attributes.initial_load_request_params.map((param, index) => {
                            return (
                                <div style={{display: 'flex'}}>
                                    <TextControl
                                        label="Param Name"
                                        placeholder="Param Name"
                                        value={attributes.initial_load_request_params[index].name}
                                        onChange={(value) => {
                                            updateParam({
                                                attr: 'initial_load_request_params',
                                                index,
                                                key: 'name',
                                                value: value,
                                                attributes,
                                                setAttributes
                                            })
                                        }}
                                    />

                                    <TextControl
                                        label="Param Value"
                                        placeholder="Param Value"
                                        value={attributes?.initial_load_request_params[index].value}
                                        onChange={(value) => {
                                            updateParam({
                                                attr: 'initial_load_request_params',
                                                index,
                                                key: 'value',
                                                value: value,
                                                attributes,
                                                setAttributes
                                            })
                                        }}
                                    />
                                </div>
                            );
                        })}
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                addParam({
                                    attr: 'initial_load_request_params',
                                    attributes,
                                    setAttributes
                                })
                            }}
                        >
                            Add New
                        </Button>
                    </Grid>
                </>
            )}
        </>
    );
};

export default SearchTab;
