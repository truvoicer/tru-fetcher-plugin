import React from 'react';
import {toggleFormat} from '@wordpress/rich-text';
import {RichTextToolbarButton} from '@wordpress/block-editor';
import {Modal, TreeSelect} from "@wordpress/components";
import {useState, useEffect} from '@wordpress/element';
import {StateMiddleware} from "../../../library/api/StateMiddleware";
import {isNotEmpty, isObject} from "../../../library/helpers/utils-helpers";
import fetcherApiConfig from "../../../library/api/fetcher-api/fetcherApiConfig";
import config from "../../../library/api/wp/config";

const PlaceholdersButton = ({isActive, onChange, value, ...otherProps}) => {
    const {reducers} = otherProps;
    const [placeholderModal, setPlaceholderModal] = useState(false);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(reducers?.app);
    stateMiddleware.setSessionState(reducers?.session);

    const [requestData, setRequestData] = useState({});
    const [treeData, setTreeData] = useState([]);
    const [selectedTreeId, setSelectedTreeId] = useState(null);
    const [dataKeysOptions, setDataKeysOptions] = useState([]);

    async function dataKeysRequest(serviceId) {
        if (!isNotEmpty(serviceId)) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${serviceId}/response-key/list`,
            params: {
                pagination: false,
            }
        });
        if (Array.isArray(results?.data?.data?.service_response_keys)) {
            return results.data.data.service_response_keys;
        }
        return [];
    }

    async function serviceListRequest() {
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/list`,
        });

        if (Array.isArray(results?.data?.data?.services)) {
            return results.data.data.services;
        }
        return [];
    }
    function buildId(id, name) {
        return `${id}_${name}`;
    }
    function getDataKeysSelectOptions() {
        if (!Array.isArray(dataKeysOptions)) {
            return [];
        }
        return dataKeysOptions.map((item) => {
            return {
                label: item.name,
                value: item.name,
            }
        })
    }

    function getServicesOptions(services, parent) {
        return services.map((item) => {
            let cloneItem = {...item};
            cloneItem.name = item.label;
            cloneItem.parent = parent;
            return cloneItem;
        })
    }

    const config = [
        {
            root: true,
            name: 'service',
            id: 'service',
            getId: (data) => {
              return `${data.id}_${data.name}`;
            },
            getData: async () => {
                const services = await serviceListRequest();
                return getServicesOptions(services, 'service')
            },
            onSelect: async (data) => {
                if (!data?.rawId) {
                    return;
                }
                const responseKeys = await dataKeysRequest(data?.rawId);
                console.log('service onSelect', responseKeys);
                setTreeData(
                    addDataToTreeById(
                        data.id,
                        {children: responseKeys},
                        treeData
                    )
                );
                //     if (!isNotEmpty(selectedService) || isNaN(selectedService)) {
                //         return;
                //     }
                //     dataKeysRequest();
            }
        }
    ];
    function addDataToTreeById(id, data, tree) {
        return tree.map((item) => {
            let cloneItem = {...item};
            if (cloneItem.id !== id) {
                cloneItem.children = cloneItem.children.map((child) => {
                    return addDataToTreeById(id, data, child);
                });
            } else {
                cloneItem = {...cloneItem, ...data};
            }
            return cloneItem;
        });
    }
    function findSelectedNameData(data, id) {
        if (data?.id === id) {
            return data;
        }
        if (Array.isArray(data.children)) {
            for (const child of data.children) {
                const selected = findSelectedNameData(child, id);
                if (selected) {
                    return selected;
                }
            }
        }
        return null;
    }
    function getSelectedNameData(data, id) {
        for (const child of data) {
            const findSelected = findSelectedNameData(child, id);
            if (findSelected) {
                return findSelected;
            }
        }
        return null;
    }
    function selectHandler(id) {
        console.log({id});
        const getSelectedData = getSelectedNameData(treeData, id);
        console.log({getSelectedData});
        const findCategory = config.find((item) => item.id === getSelectedData?.parent);
        console.log({findCategory});
        if (!findCategory) {
            return;
        }
        if (typeof findCategory?.onSelect === 'function') {
            findCategory.onSelect(getSelectedData);
        }
    }

    function buildTree() {
        return config.map(async (item) => {
            let data = {
                name: item.name,
                id: item.id,
            };
            if (typeof item?.getId !== 'function') {
                return data;
            }
            if (typeof item?.getData === 'function') {
                return data;
            }
            const itemData = await item.getData();
            if (!Array.isArray(itemData)) {
                return data;
            }

            data.children = itemData.map((child) => {
                let cloneChild = {...child};
                cloneChild.rawId = child.id;
                cloneChild.id = item.getId(child);
                return cloneChild;
            });

            return data;
        });
    }

    useEffect(() => {
        setTreeData(buildTree());
    }, []);

    useEffect(() => {
        console.log({selectedTreeId});
        if (selectedTreeId) {
            selectHandler(selectedTreeId);
        }
    }, [selectedTreeId]);


    console.log(buildTree());
    return (
        <>
            <RichTextToolbarButton
                icon="editor-code"
                title="Placholders"
                onClick={() => {
                    setPlaceholderModal(true);
                    // onChange(
                    //     toggleFormat( value, {
                    //         type: 'tru-fetcher-format/placeholder-button',
                    //     } )
                    // );
                }}
                isActive={isActive}
            />
            {placeholderModal && (
                <Modal title={'Select placeholder'}
                       size={'large'}
                       onRequestClose={() => {
                           setPlaceholderModal(false);
                       }}>
                    <h1>sdsdds</h1>
                    <TreeSelect
                        __nextHasNoMarginBottom
                        label={'Placeholders'}
                        // noOptionLabel="No parent page"
                        onChange={ ( newPage ) => setSelectedTreeId( newPage ) }
                        selectedId={ selectedTreeId }
                        tree={treeData}
                    />
                </Modal>
            )}
        </>);
}

export default PlaceholdersButton;
