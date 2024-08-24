import React from 'react';
import {insert} from '@wordpress/rich-text';
import {RichTextToolbarButton} from '@wordpress/block-editor';
import {Modal, Button} from "@wordpress/components";
import {useState, useEffect} from '@wordpress/element';
import {StateMiddleware} from "../../../library/api/StateMiddleware";
import {isNotEmpty, isObject} from "../../../library/helpers/utils-helpers";
import fetcherApiConfig from "../../../library/api/fetcher-api/fetcherApiConfig";
import ApiRequestTreeSelect from "../../../components/ApiRequestTreeSelect";

const PlaceholdersButton = ({isActive, onChange, value, ...otherProps}) => {
    const {reducers} = otherProps;
    const [placeholderModal, setPlaceholderModal] = useState(false);
    const [selectedPlaceholder, setSelectedPlaceholder] = useState(null);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(reducers?.app);
    stateMiddleware.setSessionState(reducers?.session);

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
    function getDataKeysSelectOptions(dataKeysOptions) {
        if (!Array.isArray(dataKeysOptions)) {
            return [];
        }
        return dataKeysOptions.map((item) => {
            let cloneItem = {...item};
            cloneItem.name = item.name;
            return cloneItem;
        })
    }

    function getServicesOptions(services, parent) {
        return services.map((item) => {
            let cloneItem = {...item};
            cloneItem.name = item.label;
            return cloneItem;
        })
    }

    const config = [
        {
            root: true,
            name: 'service',
            label: 'Services',
            returnValue: false,
            getId: (data) => {
                return buildId('service', data.name);
            },
            getData: async (data) => {
                return await serviceListRequest();
            },
            getOptions:  (data) => {
                return getServicesOptions(data)
            },
            onSelect: async (data) => {
                if (!data?.rawId) {
                    return;
                }
                return await dataKeysRequest(data?.rawId);
            },
            child: {
                name: 'responseKeys',
                getId: (data) => {
                    return buildId('responseKeys', data.name);
                },
                getOptions: (data) => {
                    return getDataKeysSelectOptions(data, 'service');
                },
            }
        }
    ];

    return (
        <>
            <RichTextToolbarButton
                icon="editor-code"
                title="Placholders"
                onClick={() => {
                    setPlaceholderModal(true);

                }}
                isActive={isActive}
            />
            {placeholderModal && (
                <Modal title={'Select placeholder'}
                       size={'large'}
                       onRequestClose={() => {
                           setPlaceholderModal(false);
                       }}>
                    <ApiRequestTreeSelect
                        config={config}
                        label={'Placeholders'}
                        // noOptionLabel="No parent page"
                        onChange={ ( data ) => {
                            if (!data?.name) {
                                return;
                            }
                            setSelectedPlaceholder(data.name);
                        } }
                    />

                    <Button
                        variant="primary"
                        onClick={ (e) => {
                            e.preventDefault()
                            if (!isNotEmpty(selectedPlaceholder)) {
                                return;
                            }
                            onChange(
                                insert( value, `[${selectedPlaceholder}]`, value.start, value.end )
                            );
                        }}
                    >
                        Insert
                    </Button>
                </Modal>
            )}
        </>);
}

export default PlaceholdersButton;
