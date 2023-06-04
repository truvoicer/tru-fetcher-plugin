import React, {useState, useEffect, useContext} from 'react';
import {Select, Table, Button, Modal, Header} from "semantic-ui-react";
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import {fetchRequest} from "../../../../library/api/middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import buildFormField, {FIELDS} from "./fields/field-selector";

const ItemDataKeysTab = ({onChange = false}) => {
    const [services, setServices] = useState([]);
    const [selectedService, setSelectedService] = useState('');
    const [dataKeys, setDataKeys] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const postMetaBoxContext = useContext(PostMetaBoxContext);

    function updateDataKey({value, key, dataItemKeyValue}) {
        const dataKeys = postMetaBoxContext.data.data_keys;
        const findDataKeyIndex = dataKeys.findIndex(item => item['data_item_key'] === dataItemKeyValue);
        if (findDataKeyIndex === -1) {
            return;
        }

        const cloneDataKeys = [...dataKeys];
        cloneDataKeys[findDataKeyIndex][key] = value;
        postMetaBoxContext.updateData('data_keys', cloneDataKeys);
    }

    async function serviceListRequest() {
        const results = await fetchRequest({
            config: fetcherApiConfig,
            endpoint: fetcherApiConfig.endpoints.serviceList,
        });
        if (Array.isArray(results?.data?.data)) {
            setServices(results.data.data);
        }
    }

    function getValueTypeOptions() {
        return Object.keys(FIELDS).map((key) => {
            return {
                text: key.replaceAll('_', ' '),
                value: FIELDS[key],
            }
        })
    }
    function getDataKeysOptions() {
        return dataKeys.map((item) => {
            return {
                text: item.key_value,
                value: item.id,
            }
        })
    }

    function fieldChangeHandler({value, dataItemKeyValue}) {
        updateDataKey({
            value,
            key: 'data_item_text',
            dataItemKeyValue
        })
    }
    function getFormGroup({item, index}) {
        return (
            <Table definition>
                <Table.Body>
                    <Table.Row>
                        <Table.Cell>Data Item key</Table.Cell>
                        <Table.Cell>
                            <Select
                                options={getDataKeysOptions()}
                                onChange={(e, data) => {
                                    updateDataKey({
                                        value: data.value,
                                        key: 'data_item_key',
                                        dataItemKeyValue: item.data_item_key
                                    })
                                }}
                            />
                        </Table.Cell>
                    </Table.Row>
                    <Table.Row>
                        <Table.Cell>Value Type</Table.Cell>
                        <Table.Cell>
                            <Select
                                options={getValueTypeOptions()}
                                onChange={(e, data) => {
                                    updateDataKey({
                                        value: data.value,
                                        key: 'value_type',
                                        dataItemKeyValue: item.data_item_key
                                    })
                                }}
                            />
                        </Table.Cell>
                    </Table.Row>
                    <Table.Row>
                        <Table.Cell>Data Item Text</Table.Cell>
                        <Table.Cell>
                            {buildFormField({
                                fieldType: item.value_type,
                                value: item.data_item_text,
                                index,
                                changeHandler: (value) => {
                                    fieldChangeHandler({
                                        value,
                                        dataItemKeyValue: item.data_item_key
                                    })
                                },
                                setModalHeader,
                                setModalComponent,
                                setShowModal
                            })}
                        </Table.Cell>
                    </Table.Row>
                </Table.Body>
            </Table>
        )
    }
    async function dataKeysRequest() {
        if (!isNotEmpty(selectedService)) {
            return;
        }
        const results = await fetchRequest({
            config: fetcherApiConfig,
            endpoint: fetcherApiConfig.endpoints.serviceResponseKeyList,
            params: {
                service_id: selectedService,
            }
        });
        if (Array.isArray(results?.data?.data)) {
            setDataKeys(results.data.data);
        }
    }
    function getServicesOptions() {
        return services.map((item) => {
            return {
                text: item.service_label,
                value: item.id,
            }
        })
    }
    useEffect(() => {
        serviceListRequest();
    }, []);
    useEffect(() => {
        dataKeysRequest();
    }, [selectedService]);
    console.log('dataKeys', dataKeys)
    return (
        <>
            <div className={'form-group'}>
                <label className={'form-label'}>Service</label>
            <Select
                options={getServicesOptions()}
                value={selectedService}
                onChange={(e, data) => {
                    setSelectedService(data.value);
                }}
            />
            </div>
            {postMetaBoxContext.data.data_keys.map((item, index) => {
                return (
                    <div key={index}>
                        {getFormGroup({item, index})}
                    </div>
                )
            })}
            <Button
                primary
                onClick={(e) => {
                    e.preventDefault();
                    postMetaBoxContext.updateData('data_keys', [
                        ...postMetaBoxContext.data.data_keys,
                        {
                            data_item_key: '',
                            value_type: '',
                            data_item_text: '',
                        }
                    ])
                }}
            >Add Row</Button>
            <Modal
                onClose={() => setShowModal(false)}
                onOpen={() => setShowModal(true)}
                open={showModal}
            >
                <Modal.Header>{modalHeader}</Modal.Header>
                <Modal.Content>
                    {modalComponent}
                </Modal.Content>
                <Modal.Actions>
                    <Button color='black' onClick={() => setShowModal(false)}>
                        Cancel
                    </Button>
                    <Button
                        content="Ok"
                        labelPosition='right'
                        icon='checkmark'
                        onClick={() => setShowModal(false)}
                        positive
                    />
                </Modal.Actions>
            </Modal>
        </>
    );
};

export default ItemDataKeysTab;
