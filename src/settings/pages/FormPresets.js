import React, {useEffect, useState} from 'react';
import {Button, Form, Input, Modal, Table, notification} from 'antd';
import {fetchRequest, sendRequest} from "../../library/api/state-middleware";
import config from "../../library/api/wp/config";
import FormComponent from "../../wp/blocks/components/form/FormComponent";
import {isObject} from "../../library/helpers/utils-helpers";
import {getBlockAttributesById} from "../../wp/helpers/wp-helpers";

const FormPresets = () => {
    const [api, contextHolder] = notification.useNotification();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentRecord, setCurrentRecord] = useState(null);
    const [formPresets, setFormPresets] = useState([]);
    const openNotificationWithIcon = (type, title, message) => {
        api[type]({
            message: title,
            description: message,
        });
    };

    const blockAttributes = getBlockAttributesById('form_block');
    const formChangeHandler = ({key, value, record}) => {
        if (!record?.id) {
            return;
        }
        setCurrentRecord((prevState) => {
            let newState = {...prevState};
            let cloneConfigData = {...newState['config_data']};
            newState['config_data'] = {
                ...{...blockAttributes, ...cloneConfigData},
                [key]: value
            };
            return newState;
        });
    }


    const handleOk = () => {
        if (!currentRecord?.id) {
            console.error('Id not set in currentRecord')
            return;
        }
        updateFormPresetRequest(currentRecord);
        // setIsModalOpen(false);
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

        if (!isObject(data?.config_data)) {
            return defaultFormData
        }

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
        if (results?.data?.status !== 'success') {
            openNotificationWithIcon('error', 'Error', 'Failed to create form preset');
            return;
        }
        openNotificationWithIcon('success', 'Success', 'Form preset created successfully');
        if (Array.isArray(formPresets)) {
            setFormPresets(formPresets);
        }
    }
    async function updateFormPresetRequest(data) {
        if (!data?.id) {
            console.error('Id not set in data')
            return;
        }
        const id = data.id;
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.formPresets}/${id}/update`,
            data
        });
        const formPresets = results?.data?.formPreset;
        if (results?.data?.status !== 'success') {
            openNotificationWithIcon('error', 'Error', 'Failed to update form preset');
            return;
        }
        openNotificationWithIcon('success', 'Success', 'Form preset updated successfully');
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
                            // const data = buildFormData(record);
                            // if (!data) {
                            //     console.warn(`No data found for form preset: ${record.name}`);
                            //     return;
                            // }
                            setCurrentRecord(record);
                            setIsEditModalOpen(true);
                        }}
                        type="primary"
                        style={{marginBottom: 16}}>
                        Edit
                    </Button>
                </>
            ),
        },
    ];

    return (
        <>
            {contextHolder}
            <Button
                onClick={() => {
                    setIsAddModalOpen(true);
                }}
                type="primary"
                style={{marginBottom: 16}}>
                Add Form Preset
            </Button>
            <Table columns={columns} dataSource={formPresets}/>
            <Modal title={'Edit Form Preset'}
                   open={isEditModalOpen}
                   onOk={() => {
                       if (!currentRecord?.id) {
                           console.error('Id not set in currentRecord')
                           return;
                       }
                       updateFormPresetRequest(currentRecord);
                   }}
                   onCancel={() => {
                       setIsEditModalOpen(false);
                   }}>
                <FormComponent
                    data={buildFormData(currentRecord)}
                    onChange={({key, value}) => {
                        // console.log({key, value, record})
                        formChangeHandler({key, value, record: currentRecord});
                    }}
                    showPresets={false}
                />
            </Modal>
            <Modal title={'Add Form Preset'}
                   open={isAddModalOpen}
                   onOk={() => {
                       createFormPresetRequest(values)
                   }}
                   onCancel={() => {
                       setIsAddModalOpen(false);
                   }}>
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
            </Modal>
        </>
    );
};

export default FormPresets;
