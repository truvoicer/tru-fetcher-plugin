import React, {useContext, useState, useEffect} from 'react';
import {Form, Table, Button, Modal, Input} from 'antd';
import {fetchRequest, sendRequest} from "../../library/api/state-middleware";
import config from "../../library/api/wp/config";
import FormComponent from "../../wp/blocks/components/form/FormComponent";
import {isNotEmpty, isObject} from "../../library/helpers/utils-helpers";
import {getBlockAttributesById} from "../../wp/helpers/wp-helpers";

const FormPresets = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalTitle, setModalTitle] = useState(null);
    const [modalComponent, setModalComponent] = useState(null);
    const [formPresets, setFormPresets] = useState([]);

    const blockAttributes = getBlockAttributesById('form_block');
    const formChangeHandler = ({key, value, record}) => {
        const id = record?.id;
        if (!isNotEmpty(id)) {
            return;
        }
        setFormPresets((prevState) => {
            let newState = [...prevState];
            const index = newState.findIndex((item) => item?.id === id);
            if (index === -1) {
                return newState;
            }
            if (!isObject(newState[index]['config_data'])) {
                newState[index]['config_data'] = {};
            }
            newState[index]['config_data'] = {...newState[index]['config_data'], [key]: value};
            return newState;
        });
    }

    const showModal = () => {
        setIsModalOpen(true);
    };

    const handleOk = () => {
        setIsModalOpen(false);
    };

    const handleCancel = () => {
        setIsModalOpen(false);
    };

    function buildDefaultFormData() {
        const defaultFormData = {
            presets: 'custom',
        };
        if (isObject(blockAttributes)) {
            return {...blockAttributes, ...defaultFormData};
        }
        return false;
    }

    function buildFormData(data) {
        const defaultFormData = buildDefaultFormData();
        if (!defaultFormData) {
            return false;
        }
        console.log({data})
        if (!isObject(data?.config_data)) {
            return defaultFormData
        }
        console.log({data})
        return {...defaultFormData, ...data.config_data};
    }

    async function fetchFormPresets() {
        const results = await fetchRequest({
            config: config,
            endpoint: config.endpoints.formPresets,
        });
        const formPresets = results?.data?.formPreset;
        if (Array.isArray(formPresets)) {
            setFormPresets(formPresets);
        }
    }

    async function createFormPresetRequest(data) {
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.formPresets}/create`,
            data
        });
        const formPresets = results?.data?.formPreset;
        if (Array.isArray(formPresets)) {
            setFormPresets(formPresets);
        }
    }

    useEffect(() => {
        fetchFormPresets();
    }, []);
    const columns = [
        {
            title: 'Name',
            dataIndex: 'name',
            key: 'name',
            render: (text) => <a>{text}</a>,
        },
        {
            title: 'Config',
            key: 'config',
            render: (_, record, index) => (
                <>
                    <Button
                        onClick={() => {
                            // console.log({_, a, b})
                            const data = buildFormData(record);
                            if (!data) {
                                console.warn(`No data found for form preset: ${record.name}`);
                                return;
                            }
                            setModalTitle('Edit Form Preset');
                            setModalComponent(
                                <FormComponent
                                    data={formPresets[index]?.config_data || buildDefaultFormData()}
                                    onChange={({key, value}) => {
                                        formChangeHandler({key, value, record});
                                    }}
                                    showPresets={false}
                                />
                            );
                            showModal();
                        }}
                        type="primary"
                        style={{marginBottom: 16}}>
                        Edit
                    </Button>
                </>
            ),
        },
    ];
    console.log({formPresets});
    return (
        <>
            <Button
                onClick={() => {
                    setModalTitle('Add Form Preset');
                    setModalComponent(
                        <Form
                            name="basic"
                            style={{maxWidth: 600}}
                            initialValues={{name: '', value: ''}}
                            onFinish={(values) => {
                                createFormPresetRequest(values)
                            }}
                            onFinishFailed={errorInfo => {
                                console.log('Failed:', errorInfo);
                            }}
                            autoComplete="off"
                        >
                            <Form.Item
                                label="Name"
                                name="name"
                                rules={[{required: true, message: 'Please input name!'}]}
                            >
                                <Input/>
                            </Form.Item>
                            <Form.Item>
                                <Button type="primary" htmlType="submit">
                                    Submit
                                </Button>
                            </Form.Item>
                        </Form>
                    );
                    showModal();
                }}
                type="primary"
                style={{marginBottom: 16}}>
                Add Form Preset
            </Button>
            <Table columns={columns} dataSource={formPresets}/>
            <Modal title={modalTitle}
                   open={isModalOpen}
                   onOk={handleOk}
                   onCancel={handleCancel}>
                {modalComponent}
            </Modal>
        </>
    );
};

export default FormPresets;
