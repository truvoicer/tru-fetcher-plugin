import React, {useEffect, useState} from 'react';
import {Button, Form, Input, Modal, Table, notification} from 'antd';
import config from "../../library/api/wp/config";
import {isObject} from "../../library/helpers/utils-helpers";
import {getBlockAttributesById} from "../../wp/helpers/wp-helpers";
import ListingsBlockEdit from "../../wp/blocks/listings/ListingsBlockEdit";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import {StateMiddleware} from "../../library/api/StateMiddleware";

const Listings = ({app, session}) => {
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(app);
    stateMiddleware.setSessionState(session);

    const [api, contextHolder] = notification.useNotification();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentRecord, setCurrentRecord] = useState(null);
    const [listings, setListings] = useState([]);
    const [attributes, setAttributes] = useState({});
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

    async function fetchListings() {
        const results = await stateMiddleware.fetchRequest({
            config: config,
            endpoint: config.endpoints.listings,
            params: {
                build_config_data: true
            }
        });
        const listings = results?.data?.listings;
        console.log({listings})
        if (Array.isArray(listings)) {
            setListings(listings);
        }
    }

    async function createListingRequest(data) {
        const results = await stateMiddleware.sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.listings}/create`,
            data
        });
        const listings = results?.data?.listings;
        if (results?.data?.status !== 'success') {
            openNotificationWithIcon('error', 'Error', 'Failed to create listing');
            return;
        }
        openNotificationWithIcon('success', 'Success', 'Listing created successfully');
        if (Array.isArray(listings)) {
            setListings(listings);
        }
    }

    async function updateListingRequest(data) {
        if (!data?.id) {
            console.error('Id not set in data')
            return;
        }
        const id = data.id;
        const results = await stateMiddleware.sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.listings}/${id}/update`,
            data
        });
        const listings = results?.data?.listings;
        if (results?.data?.status !== 'success') {
            openNotificationWithIcon('error', 'Error', 'Failed to update listing');
            return;
        }
        openNotificationWithIcon('success', 'Success', 'Listing updated successfully');
        if (Array.isArray(listings)) {
            setListings(listings);
        }
    }

    useEffect(() => {
        fetchListings();
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
            render: (_, record, index) => {
                console.log({record})
                return (
                    <>
                        <Button
                            onClick={() => {
                                setCurrentRecord(record);
                                setIsEditModalOpen(true);
                            }}
                            type="primary"
                            style={{marginBottom: 16}}>
                            Edit
                        </Button>
                    </>
                )
            },
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
                Add Listing Config
            </Button>
            <Table columns={columns} dataSource={listings}/>
            <Modal title={'Edit Tab Preset'}
                   open={isEditModalOpen}
                   onOk={() => {
                       if (!currentRecord?.id) {
                           console.error('Id not set in currentRecord')
                           return;
                       }
                       updateListingRequest(currentRecord);
                   }}
                   onCancel={() => {
                       setCurrentRecord(null);
                       setIsEditModalOpen(false);
                   }}>

                <ListingsBlockEdit
                    source={'api'}
                    name={'listings'}
                    reducers={{
                        app, session
                    }}
                    apiConfig={tru_fetcher_react.api}
                    attributes={currentRecord?.config_data || {}}
                    setAttributes={(data) => {
                        setCurrentRecord((prevState) => {
                            let cloneState = {...prevState};
                            let cloneConfigData = {...cloneState?.config_data || {}};
                            cloneState.config_data = {
                                ...cloneConfigData,
                                ...data
                            };
                            return cloneState;
                        });
                    }}
                />
            </Modal>
            <Modal title={'Add Listing Config'}
                   open={isAddModalOpen}
                   onOk={() => {
                       createListingRequest(values)
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
                        createListingRequest(values)
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

export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(Listings);
