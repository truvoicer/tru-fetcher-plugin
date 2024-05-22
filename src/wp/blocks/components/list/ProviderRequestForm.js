import React from 'react';
import {Button, SelectControl} from "@wordpress/components";
import Grid from "../../components/wp/Grid";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import ProviderRequestContext from "./ProviderRequestContext";
import {useContext, useEffect, useState} from "@wordpress/element";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";

const ProviderRequestForm = (props) => {
    const {
        data,
        onChange,
        index,
        moveUp,
        moveDown,
        deleteTab,
    } = props;

    const [childSrs, setChildSrs] = useState([]);
    const [srs, setSrs] = useState([]);
    const [requestData, setRequestData] = useState({});
    const providerRequestContext = useContext(ProviderRequestContext);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);


    function formChangeHandler({key, value}) {
        setRequestData(prevState => {
            let newState = {...prevState};
            newState[key] = value;
            return newState;
        })
    }

    function addServiceRequest() {
        setRequestData(prevState => {
            let newState = {...prevState};
            if (!Array.isArray(newState?.service_request)) {
                newState.service_request = [];
            }
            newState.service_request = [
                ...newState.service_request,
                {
                    name: null,
                }
            ];
            return newState;
        });
    }

    function buildSrOptions(dataSource) {
        if (!Array.isArray(dataSource)) {
            return [];
        }
        return dataSource.map((sr) => {
            let label = sr.name;
            if (sr?.hasChildren) {
                label = `${label} (has children)`;
            }
            return {
                label: label,
                value: sr.name
            }
        })
    }
    function buildOptions(dataSource) {
        if (!Array.isArray(dataSource)) {
            return [];
        }
        return dataSource.map((provider) => {
            return {
                label: provider.label,
                value: provider.name
            }
        })
    }

    async function childSrRequest(providerId, serviceRequestId) {
        if (!providerId) {
            return;
        }
        if (!serviceRequestId) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.provider}/${providerId}/service-request/${serviceRequestId}/child`,
        });
        if (Array.isArray(results?.data?.data?.providers)) {
            //     updateProviderRequestData({providers: results.data.data.providers})
        }
    }

    async function srRequest(providerId) {
        if (!providerId) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.provider}/${providerId}/service-request/list`,
            params: {
                include_children: true
            }
        });
        console.log(results)
        if (!Array.isArray(results?.data?.data?.service_requests)) {
            return;
            // setSrs(results.data.data.service_requests)
        }
        setSrs(results.data.data.service_requests)
    }

    function buildServiceRequests(providerName) {
        if (!providerName) {
            return;
        }

        const provider = providerRequestContext?.providers.find((provider) => provider.name === providerName);
        if (!provider) {
            return;
        }
        srRequest(provider.id)
    }
    function getSrChildSelects(serviceRequest) {
        if (!serviceRequest) {
            return null;
        }
        if (!Array.isArray(serviceRequest?.children)) {
            return null;
        }
        return serviceRequest.children.map((child, index) => {
            return (
                <SelectControl
                    label={child.name}
                    onChange={(value) => {
                        formChangeHandler({key: `service_request_child_${index}`, value});
                    }}
                    value={data[`service_request_child_${index}`]}
                    options={[
                        ...[
                            {
                                label: 'Select a service request',
                                value: ''
                            },
                        ],
                        ...buildOptions(srs)
                    ]}
                />
            );
        })
    }

    useEffect(() => {
        setRequestData(data);
    }, [data?.provider_name]);

    useEffect(() => {
        buildServiceRequests(requestData?.provider_name)
    }, [requestData?.provider_name]);
    console.log({data})
    return (
        <div className="">
            <Grid columns={2}>
            <SelectControl
                label="Providers"
                onChange={(value) => {
                    console.log('provider_name', value)
                    formChangeHandler({key: 'provider_name', value});
                }}
                value={requestData?.provider_name}
                options={[
                    ...[
                        {
                            label: 'Select a provider',
                            value: ''
                        },
                    ],
                    ...buildOptions(providerRequestContext?.providers)
                ]}
            />
            </Grid>
            {requestData?.provider_name && (
            <Button
                variant="primary"
                onClick={(e) => {
                    e.preventDefault()
                    addServiceRequest();
                }}
            >
                Add Service Request
            </Button>
            )}
            <Grid columns={
                Array.isArray(requestData?.service_request)? requestData?.service_request.length : 1
            }>
            {Array.isArray(requestData?.service_request) && requestData?.service_request.map((serviceRequest, index) => {
                return (
                    <Grid columns={1}>
                        <SelectControl
                            label="Service Request"
                            onChange={(value) => {
                                formChangeHandler({key: 'service_request_name', value});
                            }}
                            value={serviceRequest?.name}
                            options={[
                                ...[
                                    {
                                        label: 'Select a service request',
                                        value: ''
                                    },
                                ],
                                ...buildSrOptions(srs)
                            ]}
                        />
                        {getSrChildSelects(serviceRequest)}
                    </Grid>
                );
            })}
            </Grid>
        </div>
    );
};

export default ProviderRequestForm;
