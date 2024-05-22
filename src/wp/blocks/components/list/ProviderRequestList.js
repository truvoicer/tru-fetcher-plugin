import React from 'react';
import {Button, Modal, Panel, PanelBody, SelectControl} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import ProviderRequestForm from "./ProviderRequestForm";
import {useContext, useEffect, useState} from "@wordpress/element";

const ProviderRequestList = (props) => {

    const {
        data = [],
        onChange
    } = props;

    const [isOpen, setOpen] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);

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

    function formChangeHandler({key, value, index}) {
        let cloneTabs = [...data];
        if (cloneTabs.length && typeof cloneTabs[0] !== 'object') {
            cloneTabs = [];
        }
        if (typeof cloneTabs[index] !== 'object') {
            cloneTabs[index] = {};
        }
        cloneTabs[index][key] = value;
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }

    function deleteTab({index}) {
        const cloneTabs = [...data];
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
        ProviderRequestForm.defaultProps = {
            index,
            data: item,
            moveUp: () => {
                moveFilterItem({index, item, direction: -1});
            },
            moveDown: () => {
                moveFilterItem({index, item, direction: 1});
            },
            onChange: ({key, value}) => {
                formChangeHandler({key, value, index});
            },
            deleteTab: () => {
                deleteTab({index});
            }
        }
        return <ProviderRequestForm reducers={props?.reducers}/>;
    }
    function getSingleFilterComponent(item, index) {
        return (

            <div className="tf--list--item tf--list--item--no-header">
                <div className="tf--list--item--content">
                    <Panel>
                        <PanelBody title={`Provider Request (${index + 1})`} initialOpen={true}>
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
        )
    }

    const openModal = () => setOpen(true);
    const closeModal = () => setOpen(false);

    return (
        <div>
            {Array.isArray(data) && data.map((item, index) => {
                return getSingleFilterComponent(item, index)
            })}

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
                <Modal title="This is my modal" onRequestClose={closeModal} size={'large'}>
                    {modalComponent}
                </Modal>
            )}
        </div>
    );
};

export default ProviderRequestList;
