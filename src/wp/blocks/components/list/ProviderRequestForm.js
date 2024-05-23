import React from 'react';
import {Button, SelectControl, TreeSelect} from "@wordpress/components";
import Grid from "../../components/wp/Grid";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import ProviderRequestContext from "./ProviderRequestContext";
import {useContext, useEffect, useState} from "@wordpress/element";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import ProviderRequestTreeGrid from "./ProviderRequestTreeGrid";

const ProviderRequestForm = (props) => {
    const {
        data,
        onChange,
        index,
        moveUp,
        moveDown,
        deleteTab,
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
                        <ProviderRequestTreeGrid providerName={requestData?.provider_name}  reducers={props?.reducers} />
                    </Grid>
                );
            })}
            </Grid>
        </div>
    );
};

export default ProviderRequestForm;
