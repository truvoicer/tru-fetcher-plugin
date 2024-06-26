import React, {useState, useEffect, useContext} from 'react';
import {connect} from 'react-redux';
import {Col, Row, Select, Button, Modal, Card, Space, Form} from 'antd';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";
import fetcherApiConfig from "../../../../../library/api/fetcher-api/fetcherApiConfig";
import {isNotEmpty} from "../../../../../library/helpers/utils-helpers";
import buildFormField, {FIELDS} from "../../../components/comparisons/fields/field-selector";
import {APP_STATE} from "../../../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../../../library/redux/constants/session-constants";
import {CONFIG} from "../../../components/item/CustomItemFormFields";
import {StateMiddleware} from "../../../../../library/api/StateMiddleware";


const ItemDataKeysTab = ({app, session}) => {
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(app);
    stateMiddleware.setSessionState(session);
    const [services, setServices] = useState([]);
    const [selectedService, setSelectedService] = useState('');
    const [dataKeysOptions, setDataKeysOptions] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const postMetaBoxContext = useContext(PostMetaBoxContext);

    function updateDataKey({value, key, index}) {
        const dataKeys = postMetaBoxContext.formData.data_keys;
        const cloneDataKeys = [...dataKeys];
        cloneDataKeys[index][key] = value;
        postMetaBoxContext.updateFormData('data_keys', cloneDataKeys);
    }

    async function serviceListRequest() {
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/list`,
        });
        if (Array.isArray(results?.data?.data?.services)) {
            setServices(results.data.data.services);
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

    function getValueTypeSelectValue(value) {
        return getValueTypeOptions().find((item) => item.value === value);
    }

    function getDataKeysOptions() {
        const dataKeys = postMetaBoxContext.formData.data_keys;
        const usedKeys = dataKeys.map((item) => item.data_item_key);
        const filteredDataKeysOptions = dataKeysOptions.filter((item) => {
            return !usedKeys.includes(item.name);
        });

        const extraDataKeys = CONFIG.filter((item) => {
            return !usedKeys.includes(item.name) && !filteredDataKeysOptions.find((key) => key.name === item.name);
        });

        return [...extraDataKeys, ...filteredDataKeysOptions].map((item) => {
            return {
                label: item.name,
                value: item.name,
            }
        });
    }

    function getDataKeysSelectValue(value) {
        const findItem = [...CONFIG, ...dataKeysOptions].find((item) => item?.name === value);
        if (!findItem) {
            return null;
        }
        return {
            label: findItem.name,
            value: findItem.name,
        }
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
                            {Array.isArray(dataKeysOptions) && dataKeysOptions.length && (
                                <Select
                                    options={getDataKeysOptions({index})}
                                    value={getDataKeysSelectValue(item?.data_item_key)}
                                    onChange={(e, data) => {
                                        updateDataKey({
                                            index,
                                            value: data.value,
                                            key: 'data_item_key',
                                        })
                                    }}
                                />
                            )}
                        </Form.Item>
                    </div>
                    <div>
                        <Form.Item label="Value Type">
                            <Select
                                options={getValueTypeOptions()}
                                value={getValueTypeSelectValue(item?.value_type)}
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
        if (!isNotEmpty(postMetaBoxContext.formData?.service)) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${postMetaBoxContext.formData?.service}/response-key/list`,
            params: {
                pagination: false,
            }
        });
        if (Array.isArray(results?.data?.data?.service_response_keys)) {
            setDataKeysOptions(results.data.data.service_response_keys);
        }
    }

    function getServicesOptions() {
        return services.map((item) => {
            return {
                label: item.label,
                value: item.id,
            }
        })
    }

    function getServicesSelectValue() {
        return getServicesOptions().find((item) => parseInt(item.value) === parseInt(postMetaBoxContext.formData?.service));
    }

    useEffect(() => {
        serviceListRequest();
    }, []);
    useEffect(() => {
        dataKeysRequest();
    }, [postMetaBoxContext.formData?.service]);


    return (
        <>
            <Row>
                <Col>
                    <Form.Item label="Service">
                        {Array.isArray(services) && services.length && (
                            <Select
                                style={{minWidth: 180}}
                                options={getServicesOptions()}
                                value={getServicesSelectValue()}
                                onChange={(e, data) => {
                                    postMetaBoxContext.updateFormData('service', data.value);
                                }}
                            />
                        )}
                    </Form.Item>
                </Col>
            </Row>
            <Row>
                {postMetaBoxContext.formData.data_keys.map((item, index) => {
                    return (
                        <Col key={index}>
                            {getFormGroup({item, index})}
                        </Col>
                    )
                })}
            </Row>
            <Row>
                <Col>
                    {isNotEmpty(postMetaBoxContext.formData?.service) && (
                        <Button
                            type={'primary'}
                            onClick={(e) => {
                                e.preventDefault();
                                postMetaBoxContext.updateFormData('data_keys', [
                                    ...postMetaBoxContext.formData.data_keys,
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
export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(ItemDataKeysTab);
