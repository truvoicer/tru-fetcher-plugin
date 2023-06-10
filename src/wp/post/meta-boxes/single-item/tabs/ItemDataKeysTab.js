import React, {useState, useEffect, useContext} from 'react';
import {Col, Row, Select, Button, Modal, Card, Space, Form} from 'antd';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";
import {fetchRequest} from "../../../../../library/api/middleware";
import fetcherApiConfig from "../../../../../library/api/fetcher-api/fetcherApiConfig";
import {isNotEmpty} from "../../../../../library/helpers/utils-helpers";
import buildFormField, {FIELDS} from "../../../components/comparisons/fields/field-selector";

const ItemDataKeysTab = () => {
    const [services, setServices] = useState([]);
    const [selectedService, setSelectedService] = useState('');
    const [dataKeysOptions, setDataKeysOptions] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const postMetaBoxContext = useContext(PostMetaBoxContext);

    function updateDataKey({value, key, index}) {
        const dataKeys = postMetaBoxContext.data.data_keys;
        const cloneDataKeys = [...dataKeys];
        cloneDataKeys[index][key] = value;
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
                label: key.replaceAll('_', ' '),
                value: FIELDS[key],
            }
        })
    }

    function getDataKeysOptions() {
        const dataKeys = postMetaBoxContext.data.data_keys;
        const usedKeys = dataKeys.map((item) => item.data_item_key);
        const filteredDataKeysOptions = dataKeysOptions.filter((item) => {
            return !usedKeys.includes(item.key_value);
        });
        return filteredDataKeysOptions.map((item) => {
            return {
                label: item.key_value,
                value: item.key_value,
            }
        })
    }

    function fieldChangeHandler({value, index}) {
        updateDataKey({
            index,
            value,
            key: 'data_item_value',
        })
    }

    function getFormGroup({item, index}) {
        return (
            <Space direction="vertical" size={16}>
                <Card style={{width: 300}}>
                    <div>
                        <Form.Item label="Data Item Key">
                            <Select
                                options={getDataKeysOptions({index})}
                                onChange={(e, data) => {
                                    updateDataKey({
                                        index,
                                        value: data.value,
                                        key: 'data_item_key',
                                    })
                                }}
                            />
                        </Form.Item>
                    </div>
                    <div>
                        <Form.Item label="Value Type">
                            <Select
                                options={getValueTypeOptions()}
                                onChange={(e, data) => {
                                    updateDataKey({
                                        index,
                                        value: data.value,
                                        key: 'value_type',
                                    })
                                }}
                            />
                        </Form.Item>
                    </div>
                    <div>
                        <Form.Item label="Data Item Value">
                            {buildFormField({
                                fieldType: item.value_type,
                                value: item.data_item_value,
                                index,
                                changeHandler: (value) => {
                                    fieldChangeHandler({
                                        index,
                                        value,
                                    })
                                },
                                setModalHeader,
                                setModalComponent,
                                setShowModal
                            })}
                        </Form.Item>
                    </div>
                </Card>
            </Space>
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
            setDataKeysOptions(results.data.data);
        }
    }

    function getServicesOptions() {
        return services.map((item) => {
            return {
                label: item.service_label,
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

    return (
        <>
            <Row>
                <Col>
                    <Form.Item label="Service">
                        <Select
                            style={{minWidth: 180}}
                            options={getServicesOptions()}
                            value={selectedService}
                            onChange={(e, data) => {
                                setSelectedService(data.value);
                            }}
                        />
                    </Form.Item>
                </Col>
            </Row>
            <Row>
                {postMetaBoxContext.data.data_keys.map((item, index) => {
                    return (
                        <Col key={index}>
                            {getFormGroup({item, index})}
                        </Col>
                    )
                })}
            </Row>
            <Row>
                <Col>
                    {isNotEmpty(selectedService) && (
                        <Button
                            type={'primary'}
                            onClick={(e) => {
                                e.preventDefault();
                                postMetaBoxContext.updateData('data_keys', [
                                    ...postMetaBoxContext.data.data_keys,
                                    {
                                        data_item_key: '',
                                        value_type: '',
                                        data_item_value: '',
                                    }
                                ])
                            }}
                        >
                            Add Row
                        </Button>
                    )}
                </Col>
            </Row>
            <Modal
                title={modalHeader}
                open={showModal}
                onOk={() => setShowModal(false)}
                onCancel={() => setShowModal(false)}
            >

                {modalComponent}
            </Modal>
        </>
    );
};

export default ItemDataKeysTab;