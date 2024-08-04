import React, {useEffect, useState} from 'react';
import {Button, Form, Input, Table, notification} from 'antd';
import config from "../../../library/api/wp/config";
import TabComponent from "../../../wp/blocks/components/tabs/Tabs";
import {isObject} from "../../../library/helpers/utils-helpers";
import {getBlockAttributesById} from "../../../wp/helpers/wp-helpers";
import {APP_STATE} from "../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import {StateMiddleware} from "../../../library/api/StateMiddleware";
import {Modal} from '@wordpress/components'

const TabPresets = ({app, session}) => {
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(app);
    stateMiddleware.setSessionState(session);

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
        const results = await stateMiddleware.fetchRequest({
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
        const results = await stateMiddleware.sendRequest({
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
        const results = await stateMiddleware.sendRequest({
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
            {isEditModalOpen &&
                <Modal title={'Edit Tab Preset'}
                       size={'fill'}
                       headerActions={
                           <div>
                               <Button variant="secondary"
                                       onClick={() => {
                                           if (!currentRecord?.id) {
                                               console.error('Id not set in currentRecord')
                                               return;
                                           }
                                           updateTabPresetRequest(currentRecord);
                                       }}>
                                   Save
                               </Button>
                           </div>
                       }
                       onRequestClose={() => {
                           setCurrentRecord(null);
                           setIsEditModalOpen(false);
                       }}>

                    <TabComponent
                        reducers={{
                            app, session
                        }}
                        data={buildTabData(currentRecord)}
                        onChange={({key, value}) => {
                            tabChangeHandler({key, value, record: currentRecord});
                        }}
                        showPresets={false}
                    />
                </Modal>
            }
            {isAddModalOpen &&
                <Modal title={'Add Tab Preset'}
                       size={'fill'}
                       onOk={() => {
                           createTabPresetRequest(values)
                       }}
                       onRequestClose={() => {
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
            }
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
)(TabPresets);
