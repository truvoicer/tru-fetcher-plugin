import React from 'react';
import {useContext, useEffect, useState} from "@wordpress/element";
import {ToggleControl, TreeSelect} from "@wordpress/components";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import ProviderRequestContext from "./ProviderRequestContext";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import Grid from "../../../../components/Grid";

function ProviderRequestTreeGrid({providerName, reducers, serviceRequest = null, onChange = null}) {
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

            let data = {
                hasChildren: sr?.hasChildren || false,
                id: sr.id
            }
            if (sr.hasOwnProperty('include_children')) {
                data.include_children = sr.include_children;
            }
            if (Array.isArray(sr?.children)) {
                data.children = buildOptions(sr.children)
            }
            data.name = label;
            return data;
        })
    }

    function findSr(srId, data) {
        for (let i = 0; i < data.length; i++) {
            const sr = data[i];
            if (parseInt(sr.id) === parseInt(srId)) {
                return sr;
            } else if (Array.isArray(sr?.children)) {
                const find = findSr(srId, sr.children);
                if (find) {
                    return find;
                }
            }
        }
        return null;
    }

    function updateTreeItemState(key, value) {
        setTreeData(prevState => {
            let newState = [...prevState];
            newState = updateTreeItem(key, value, selectedSrId, newState);
            return buildOptions(newState);
        })
    }

    function updateTreeItem(key, value, srId, data) {
        return data.map((sr) => {
            if (parseInt(sr.id) === parseInt(srId)) {
                sr[key] = value;
            } else if (Array.isArray(sr.children)) {
                sr.children = updateTreeItem(key, value, srId, sr.children)
            }
            return sr;
        });
    }


    async function srRequest(providerId) {
        if (!providerId) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.provider}/${providerId}/service-request/list`,
            params: {
                include_children: false,
                tree_view: true,
                show_nested_children: true
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

    function buildSaveData() {
        return findSr(selectedSrId, treeData);
    }

    useEffect(() => {
        buildServiceRequests(providerName)
    }, [providerName]);

    useEffect(() => {
        if (!Array.isArray(treeData) || treeData.length === 0) {
            return;
        }
        if (serviceRequest?.id) {
            setSelectedSrId(serviceRequest.id);
            if (serviceRequest.hasOwnProperty('include_children')) {
                updateTreeItemState('include_children', serviceRequest.include_children);
            }
        }
    }, [serviceRequest, treeData]);

    useEffect(() => {
        if (!selectedSrId || selectedSrId === '') {
            return;
        }
        if (typeof onChange !== 'function') {
            return;
        }
        onChange(buildSaveData());
    }, [selectedSrId, treeData]);

    useEffect(() => {
        setTreeData(
            buildOptions(srs)
        )
    }, [srs]);
    const selectedSrData = findSr(selectedSrId, treeData);
    return (
        <>
            {Array.isArray(treeData) && treeData.length > 0 && (
                <Grid columns={1}>
                    <TreeSelect
                        label="Service Request"
                        noOptionLabel="Select a Service Request"
                        onChange={(newPage) => {
                            setSelectedSrId(newPage);
                        }}
                        selectedId={selectedSrId}
                        tree={treeData}
                    />
                    <ToggleControl
                        label="Include children?"
                        checked={selectedSrData?.include_children || false}
                        onChange={(value) => {
                            updateTreeItemState('include_children', value);
                        }}
                    />
                </Grid>
            )}
        </>
    );
}

export default ProviderRequestTreeGrid;
