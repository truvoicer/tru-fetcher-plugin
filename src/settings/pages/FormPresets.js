import React, {useContext, useState, useEffect} from 'react';
import { Form, Table, Button, Modal, Input } from 'antd';
import {fetchRequest, sendRequest} from "../../library/api/state-middleware";
import config from "../../library/api/wp/config";
import FormComponent from "../../wp/blocks/components/form/FormComponent";

const FormPresets = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalTitle, setModalTitle] = useState(null);
    const [modalComponent, setModalComponent] = useState(null);
    const [formPresets, setFormPresets] = useState([]);

    const formChangeHandler = (data) => {
        console.log({data});
    }
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
            render: (_, record) => (
                <>
                <Button
                    onClick={() => {
                        setModalTitle('Edit Form Preset');
                        setModalComponent(
                            <FormComponent
                                data={{}}
                                onChange={formChangeHandler}
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
    const showModal = () => {
        setIsModalOpen(true);
    };

    const handleOk = () => {
        setIsModalOpen(false);
    };

    const handleCancel = () => {
        setIsModalOpen(false);
    };
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
            <Table columns={columns} dataSource={formPresets} />
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
