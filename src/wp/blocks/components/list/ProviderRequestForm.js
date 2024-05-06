import React from 'react';
import {Panel, PanelBody, SelectControl} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import ProviderRequestContext from "./ProviderRequestContext";
import {useContext, useEffect, useState} from "@wordpress/element";

const ProviderRequestForm = (props) => {
    const [serviceRequests, setServiceRequests] = useState([]);
    const providerRequestContext = useContext(ProviderRequestContext);

    const {
        data,
        onChange,
        index,
        moveUp,
        moveDown,
        deleteTab,
    } = props;


    function formChangeHandler({key, value}) {
        if (typeof onChange === 'function') {
            onChange({key, value});
        }
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

    function buildServiceRequests(providerName) {
        if (!providerName) {
            return;
        }
        const provider = providerRequestContext?.providers.find((provider) => provider.name === providerName);
        if (!provider) {
            return;
        }
        setServiceRequests(provider?.service_request);
    }

    useEffect(() => {
        buildServiceRequests(data?.provider_name)
    }, [data?.provider_name]);

    return (
        <div className="tf--list--item tf--list--item--no-header">
            <div className="tf--list--item--content">
                <Panel>
                    <PanelBody title={`Provider Request (${index + 1})`} initialOpen={true}>
                        <SelectControl
                            label="Providers"
                            onChange={(value) => {
                                formChangeHandler({key: 'provider_name', value});
                            }}
                            value={data?.provider_name}
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
                        <SelectControl
                            label="Service Request"
                            onChange={(value) => {
                                formChangeHandler({key: 'service_request_name', value});
                            }}
                            value={data?.service_request_name}
                            options={[
                                ...[
                                    {
                                        label: 'Select a service request',
                                        value: ''
                                    },
                                ],
                                ...buildOptions(serviceRequests)
                            ]}
                        />
                    </PanelBody>
                </Panel>
            </div>
            <div className={'tf--list--item--actions'}>
                <a onClick={() => {
                    moveUp()
                }}>
                    <Icon icon={chevronUp}/>
                </a>
                <a onClick={() => {
                    moveDown()
                }}>
                    <Icon icon={chevronDown}/>
                </a>
                <a onClick={(e) => {
                    e.preventDefault()
                    deleteTab();
                }}>
                    <Icon icon={trash}/>
                </a>
            </div>
        </div>
    );
};

export default ProviderRequestForm;
