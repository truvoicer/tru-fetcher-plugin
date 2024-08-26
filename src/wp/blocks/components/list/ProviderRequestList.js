import React from 'react';
import {Button, Modal, Panel, PanelBody, SelectControl} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import ProviderRequestForm from "./ProviderRequestForm";
import {useContext, useEffect, useState} from "@wordpress/element";
import ProviderRequestContext from "./ProviderRequestContext";
import Grid from "../../../../components/Grid";

const ProviderRequestList = (props) => {

    const {
        data = [],
        onChange
    } = props;

    const [isOpen, setOpen] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const providerRequestContext = useContext(ProviderRequestContext);

    function addFilter() {
        const cloneTabs = [...data];
        cloneTabs.push({
            provider_name: null,
            service_request_name: null,
        });
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }

    function formChangeHandler({value, index}) {
        let cloneTabs = [...data];
        if (cloneTabs.length && typeof cloneTabs[0] !== 'object') {
            cloneTabs = [];
        }
        if (typeof cloneTabs[index] !== 'object') {
            cloneTabs[index] = {};
        }
        cloneTabs[index] = value;
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }

    function deleteTab({index}) {
        const cloneTabs = [...data];
        console.log({index, cloneTabs})
        cloneTabs.splice(index, 1);
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }

    function moveFilterItem({index, item, direction}) {
        let cloneTabs = [...data];
        let newIndex = index + direction;
        if (newIndex < 0) {
            newIndex = 0;
        }
        if (newIndex > cloneTabs.length - 1) {
            newIndex = cloneTabs.length - 1;
        }
        cloneTabs.splice(index, 1);
        cloneTabs.splice(newIndex, 0, item);
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }

    function getModalComponent(item, index) {
        return (
            <ProviderRequestForm
                data={item}
                reducers={props?.reducers}
                onSave={(value) => {
                    formChangeHandler({value, index});
                    closeModal();
                }}
            />
        );
    }

    function getSingleFilterComponent(item, index) {
        const provider = providerRequestContext?.providers.find((provider) => provider.name === item?.provider_name);
        console.log({item, provider})
        const name = provider?.label || item?.provider_name;
        let serviceRequests;
        if (Array.isArray(item?.service_request)) {
            serviceRequests = item.service_request.map((serviceRequest) => {
                return serviceRequest?.name || 'name_error';
            }).join(', ');
        } else {
            serviceRequests = 'service_request_error';
        }
        return (

            <div className="tf--list--item tf--list--item--no-header">
                <div className="tf--list--item--content">
                    <Panel>
                        <PanelBody title={`Provider Request (${name})`} initialOpen={true}>
                            <p>Service Requests: {serviceRequests}</p>
                            <Button variant="secondary" onClick={() => {
                                setModalComponent(getModalComponent(item, index));
                                openModal();
                            }}>
                                Edit Provider Request
                            </Button>
                        </PanelBody>
                    </Panel>
                </div>

                <div className={'tf--list--item--actions'}>
                    <a onClick={() => {

                        moveFilterItem({index, item, direction: -1});
                    }}>
                        <Icon icon={chevronUp}/>
                    </a>
                    <a onClick={() => {
                        moveFilterItem({index, item, direction: 1});
                    }}>
                        <Icon icon={chevronDown}/>
                    </a>
                    <a onClick={(e) => {
                        e.preventDefault();
                        deleteTab({index});
                    }}>
                        <Icon icon={trash}/>
                    </a>
                </div>
            </div>
        )
    }

    const openModal = () => setOpen(true);
    const closeModal = () => setOpen(false);

    return (
        <div>
            <Grid columns={2}>
                {Array.isArray(data) && data.map((item, index) => {
                    return getSingleFilterComponent(item, index)
                })}
            </Grid>

            <Button
                variant="primary"
                onClick={(e) => {
                    e.preventDefault()
                    addFilter();
                }}
            >
                Add Provider Request
            </Button>
            {isOpen && (
                <Modal title="Add a Provider Request" onRequestClose={closeModal} size={'large'}>
                    {modalComponent}
                </Modal>
            )}
        </div>
    );
};

export default ProviderRequestList;
