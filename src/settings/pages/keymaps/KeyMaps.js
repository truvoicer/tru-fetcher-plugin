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
    const [dataKeysOptions, setDataKeysOptions] = useState([]);

    const columns = [
        {
            title: 'Key',
            dataIndex: 'key',
            width: '30%',
        },
        {
            title: 'Map To',
            dataIndex: 'keymap',
            editable: true,
            type: 'select',
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
        return keymapData;
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
                        showAddButton={false}
                        selectOptions={getDataKeysSelectOptions()}
                        columns={columns}
                        dataSource={getKeyMapData()}
                        onDelete={({newData, key}) => {
                            console.log({newData, key})
                        }}
                        onSave={({row, col}) => {
                            console.log({row, col})
                            saveKeymap(row);
                        }}
                    />
                </Col>
            </Row>
        </>
    );
};

export default Keymaps;
