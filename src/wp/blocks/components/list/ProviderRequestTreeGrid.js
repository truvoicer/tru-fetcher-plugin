import React from 'react';
import {useContext, useEffect, useState} from "@wordpress/element";
import {Button, SelectControl, TreeSelect} from "@wordpress/components";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import ProviderRequestContext from "./ProviderRequestContext";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";

function ProviderRequestTreeGrid({providerName, reducers}) {
    const [treeData, setTreeData] = useState([]);
    const [selectedSrId, setSelectedSrId] = useState(false);
    const [srs, setSrs] = useState([]);

    const providerRequestContext = useContext(ProviderRequestContext);
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(reducers?.app);
    stateMiddleware.setSessionState(reducers?.session);

    const provider = providerRequestContext?.providers.find((provider) => provider.name === providerName);

    function buildOptions(srs) {
       return srs.map((sr) => {

            let label = sr.name;
            if (sr?.hasChildren) {
                label = `${label} (has children)`;
            }
            return {
                hasChildren: sr?.hasChildren || false,
                name: label,
                id: sr.id
            }
        })
    }

    function findSr(srId, data) {
        let foundSr = null;
        data.forEach((sr) => {
            if (parseInt(sr.id) === parseInt(srId)) {
                foundSr = sr;
            } else if (Array.isArray(sr.children)) {
                foundSr = findSr(srId, sr.children)
            }
        });
        return foundSr;
    }
    function addChildren(srId, children, data) {
        return data.map((sr) => {
            if (parseInt(sr.id) === parseInt(srId)) {
                sr.children = buildOptions(children);
            } else if (Array.isArray(sr.children)) {
                sr.children = addChildren(srId, children, sr.children)
            }
            return sr;
        });
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
            endpoint: `${fetcherApiConfig.endpoints.provider}/${providerId}/service-request/${serviceRequestId}/child/list`,
        });
        if (!Array.isArray(results?.data?.data?.service_requests)) {
            return;
        }
        setTreeData(prevState => {
            let newState = [...prevState];
            newState = addChildren(serviceRequestId, results.data.data.service_requests, newState);
            return newState;
        })
    }

    async function srRequest(providerId) {
        if (!providerId) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.provider}/${providerId}/service-request/list`,
            params: {
                include_children: false
            }
        });

        if (!Array.isArray(results?.data?.data?.service_requests)) {
            return;
        }
        setSrs(results.data.data.service_requests)
    }

    function buildServiceRequests(providerName) {
        if (!providerName) {
            return;
        }

        if (!provider) {
            return;
        }
        srRequest(provider.id)
    }

    useEffect(() => {
        buildServiceRequests(providerName)
    }, [providerName]);

    useEffect(() => {
        setTreeData(
            buildOptions(srs)
        )
    }, [srs]);
    return (
        <>
            {Array.isArray(treeData) && treeData.length > 0 && (
            <TreeSelect
                label="Service Request"
                noOptionLabel="Select a Service Request"
                onChange={(newPage) => {
                    setSelectedSrId(newPage);
                    const findNestedSr = findSr(newPage, treeData);
                    if (!findNestedSr?.hasChildren) {
                        return;
                    }
                    if (!provider) {
                        return;
                    }
                    childSrRequest(provider.id, newPage)
                }}
                selectedId={selectedSrId}
                tree={treeData}
            />
            )}
        </>
    );
}

export default ProviderRequestTreeGrid;
