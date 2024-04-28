import React, {useEffect, useState} from 'react';
import {Col, Row, Select, Button, Modal, Card, Space, Form} from 'antd';
import {fetchRequest, sendRequest} from "../../../library/api/state-middleware";
import fetcherApiConfig from "../../../library/api/fetcher-api/fetcherApiConfig";
import NameValueDatatable from "../../../components/tables/name-value-datatable/NameValueDatatable";
import {isNotEmpty} from "../../../library/helpers/utils-helpers";
import config from "../../../library/api/wp/config";

const Keymaps = () => {

    const [services, setServices] = useState([]);
    const [selectedService, setSelectedService] = useState(null);
    const [keymapData, setKeymapData] = useState([]);
    const [postKeys, setPostKeys] = useState([]);
    const [dataKeysOptions, setDataKeysOptions] = useState([]);

    const columns = [
        {
            title: 'Key',
            dataIndex: 'key',
            width: '30%',
        },
        {
            title: 'Map To Post Key',
            dataIndex: 'post_key',
            editable: true,
            type: 'select',
            required: false,
        },
        {
            title: 'Label',
            dataIndex: 'label',
            editable: true,
            type: 'text',
            required: false,
        },
    ];

    async function dataKeysRequest() {
        if (!isNotEmpty(selectedService)) {
            return;
        }
        const results = await fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${selectedService}/response-key/list`,
            params: {
                pagination: false,
            }
        });
        if (Array.isArray(results?.data?.data?.service_response_keys)) {
            setDataKeysOptions(results.data.data.service_response_keys);
        }
    }
    async function serviceListRequest() {
        const results = await fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/list`,
        });

        if (Array.isArray(results?.data?.data?.services)) {
            setServices(results.data.data.services);
        }
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
    function getPostKeysSelectOptions() {
        if (!Array.isArray(postKeys)) {
            return [];
        }
        return postKeys.map((item) => {
            return {
                label: item,
                value: item,
            }
        })
    }
    function getServicesOptions() {
        if (!Array.isArray(services)) {
            return [];
        }
        return services.map((item) => {
            return {
                label: item.label,
                value: item.id,
            }
        })
    }

    function getKeyMapData() {

        if (!Array.isArray(dataKeysOptions)) {
            return [];
        }
        return dataKeysOptions.map((item) => {
            const find = keymapData.find((keymap) => keymap.key === item.name);
            return {
                key: item.name,
                post_key: find?.post_key || '',
                label: find?.label || '',
            }
        });
    }

    function fetchKeyMapData() {
        if (!selectedService) {
            return;
        }
        fetchRequest({
            config: config,
            endpoint: `${config.endpoints.keymap}/service/${selectedService}`,
        }).then((results) => {
            console.log(results)
            if (Array.isArray(results?.data?.keymaps)) {
                setKeymapData(results.data.keymaps);
            }
        })

    }
    function fetchPostKeys() {
        fetchRequest({
            config: config,
            endpoint: `${config.endpoints.keymap}/keys/post`,
        }).then((results) => {
            console.log(results)
            if (Array.isArray(results?.data?.keys)) {
                setPostKeys(results.data.keys);
            }
        })
    }

    async function saveKeymap(data) {
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.keymap}/service/${selectedService}/save`,
            data: {
                keymap: data,
            }
        });
        const keymaps = results?.data?.keymaps;
        if (Array.isArray(keymaps)) {
            setKeymapData(keymaps);
        }
    }

    useEffect(() => {
        serviceListRequest();
        fetchPostKeys();
    }, []);

    useEffect(() => {
        if (!isNotEmpty(selectedService) || isNaN(selectedService)) {
            return;
        }
        console.log({selectedService})
        dataKeysRequest();
    }, [selectedService]);

    useEffect(() => {
        fetchKeyMapData();
    }, [dataKeysOptions]);

    return (
        <>
            <h1>Keymaps</h1>
            <Row>
                <Col>
                    <Form.Item label="Service">
                        {Array.isArray(services) && services.length && (
                            <Select
                                placeholder="Select a service"
                                style={{minWidth: 180}}
                                options={getServicesOptions()}
                                value={selectedService}
                                onChange={(e, data) => {
                                    setSelectedService(data.value);
                                }}
                            />
                        )}
                    </Form.Item>
                </Col>
            </Row>
            <Row>
                <Col>
                    <NameValueDatatable
                        showAddButton={true}
                        selectOptions={getPostKeysSelectOptions()}
                        columns={columns}
                        dataSource={getKeyMapData()}
                        onDelete={({newData, key}) => {
                            console.log({newData, key})
                        }}
                        onSave={({row, col}) => {
                            console.log({row, col})
                            saveKeymap(row);
                        }}
                        onAdd={({values}) => {
                            console.log({values})
                        }}
                        addFormComponent={(
                            <>
                                <Form.Item
                                    label="Data key"
                                    name="data_key"
                                    rules={[{required: true, message: 'Please input your username!'}]}
                                >
                                    <Select
                                        placeholder={'Please Select'}
                                        style={{minWidth: 180}}
                                        options={getDataKeysSelectOptions()}
                                    />
                                </Form.Item>
                                <Form.Item>
                                    <Button type="primary" htmlType="submit">
                                        Submit
                                    </Button>
                                </Form.Item>
                            </>
                        )}
                    />
                </Col>
            </Row>
        </>
    );
};

export default Keymaps;
