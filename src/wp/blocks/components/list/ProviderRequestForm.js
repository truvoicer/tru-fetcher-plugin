import React from 'react';
import {Button, SelectControl, TreeSelect} from "@wordpress/components";
import ProviderRequestContext from "./ProviderRequestContext";
import {useContext, useEffect, useState} from "@wordpress/element";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import ProviderRequestTreeGrid from "./ProviderRequestTreeGrid";
import Grid from "../../../../components/Grid";

const ProviderRequestForm = (props) => {
    const {
        data,
        onSave,
    } = props;

    const [requestData, setRequestData] = useState({});
    const providerRequestContext = useContext(ProviderRequestContext);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);


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

    function formChangeHandler({key, value}) {
        setRequestData(prevState => {
            let newState = {...prevState};
            newState[key] = value;
            return newState;
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

    useEffect(() => {
        setRequestData(data);
    }, [data?.provider_name]);


    return (
        <Grid columns={1}>
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
            <Grid columns={3}>
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
            </Grid>
            <Grid columns={3} style={{marginTop: 10}}>
                {Array.isArray(requestData?.service_request) && requestData?.service_request.map((serviceRequest, index) => {
                    return (
                        <ProviderRequestTreeGrid
                            key={index}
                            providerName={requestData?.provider_name}
                            serviceRequest={serviceRequest}
                            reducers={props?.reducers}
                            onChange={(value) => {
                                setRequestData(prevState => {
                                    let newState = {...prevState};
                                    newState.service_request[index] = value;
                                    return newState;
                                });
                            }}
                        />
                    );
                })}
            </Grid>
            <Grid columns={4}>
                <Button
                    variant="primary"
                    onClick={(e) => {
                        e.preventDefault();
                        if (typeof onSave === 'function') {
                            onSave(requestData);
                        }
                    }}
                >
                    Save
                </Button>
            </Grid>
        </Grid>
    );
};

export default ProviderRequestForm;
