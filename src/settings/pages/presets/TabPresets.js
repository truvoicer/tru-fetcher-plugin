import React, {useEffect, useState} from 'react';
import {Button, Form, Input, Modal, Table, notification} from 'antd';
import {fetchRequest, sendRequest} from "../../../library/api/state-middleware";
import config from "../../../library/api/wp/config";
import TabComponent from "../../../wp/blocks/components/tabs/Tabs";
import {isObject} from "../../../library/helpers/utils-helpers";
import {getBlockAttributesById} from "../../../wp/helpers/wp-helpers";

const TabPresets = () => {
    const [api, contextHolder] = notification.useNotification();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentRecord, setCurrentRecord] = useState(null);
    const [tabPresets, setTabPresets] = useState([]);
    const openNotificationWithIcon = (type, title, message) => {
        api[type]({
            message: title,
            description: message,
        });
    };

    const blockAttributes = getBlockAttributesById('tabs_block');
    const tabChangeHandler = ({key, value, record}) => {
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

    function buildDefaultTabData() {
        const defaultTabData = {
            presets: 'custom',
        };
        if (isObject(blockAttributes)) {
            return {...blockAttributes, ...defaultTabData};
        }
        return false;
    }

    function buildTabData(data) {
        const defaultTabData = buildDefaultTabData();
        if (!defaultTabData) {
            return false;
        }

        if (!isObject(data?.config_data)) {
            return defaultTabData
        }

        return {...defaultTabData, ...data.config_data};
    }

    async function fetchTabPresets() {
        const results = await fetchRequest({
            config: config,
            endpoint: config.endpoints.tabPresets,
            params: {
                build_config_data: true
            }
        });
        const tabPresets = results?.data?.tabPreset;
        if (Array.isArray(tabPresets)) {
            setTabPresets(tabPresets);
        }
    }

    async function createTabPresetRequest(data) {
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.tabPresets}/create`,
            data
        });
        const tabPresets = results?.data?.tabPreset;
        if (results?.data?.status !== 'success') {
            openNotificationWithIcon('error', 'Error', 'Failed to create tab preset');
            return;
        }
        openNotificationWithIcon('success', 'Success', 'Tab preset created successfully');
        if (Array.isArray(tabPresets)) {
            setTabPresets(tabPresets);
        }
    }
    async function updateTabPresetRequest(data) {
        if (!data?.id) {
            console.error('Id not set in data')
            return;
        }
        const id = data.id;
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.tabPresets}/${id}/update`,
            data
        });
        const tabPresets = results?.data?.tabPreset;
        if (results?.data?.status !== 'success') {
            openNotificationWithIcon('error', 'Error', 'Failed to update tab preset');
            return;
        }
        openNotificationWithIcon('success', 'Success', 'Tab preset updated successfully');
        if (Array.isArray(tabPresets)) {
            setTabPresets(tabPresets);
        }
    }

    useEffect(() => {
        fetchTabPresets();
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
                            // const data = buildTabData(record);
                            // if (!data) {
                            //     console.warn(`No data found for tab preset: ${record.name}`);
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
                Add Tab Preset
            </Button>
            <Table columns={columns} dataSource={tabPresets}/>
            <Modal title={'Edit Tab Preset'}
                   open={isEditModalOpen}
                   onOk={() => {
                       if (!currentRecord?.id) {
                           console.error('Id not set in currentRecord')
                           return;
                       }
                       updateTabPresetRequest(currentRecord);
                   }}
                   onCancel={() => {
                       setCurrentRecord(null);
                       setIsEditModalOpen(false);
                   }}>
                <TabComponent
                    data={buildTabData(currentRecord)}
                    onChange={({key, value}) => {
                        // console.log({key, value, record})
                        tabChangeHandler({key, value, record: currentRecord});
                    }}
                    showPresets={false}
                />
            </Modal>
            <Modal title={'Add Tab Preset'}
                   open={isAddModalOpen}
                   onOk={() => {
                       createTabPresetRequest(values)
                   }}
                   onCancel={() => {
                       setCurrentRecord(null);
                       setIsAddModalOpen(false);
                   }}>
                <Form
                    name="basic"
                    style={{maxWidth: 600}}
                    initialValues={{name: '', value: ''}}
                    onFinish={(values) => {
                        createTabPresetRequest(values)
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

export default TabPresets;
