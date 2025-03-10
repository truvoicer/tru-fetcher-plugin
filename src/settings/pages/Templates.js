import React, {useContext, useEffect, useState} from 'react';
import SettingsContext from "../contexts/SettingsContext";
import {Button, Form, Input, Table, notification} from 'antd';
import {Modal} from '@wordpress/components'
import config from "../../library/api/wp/config";
import {isObject} from "../../library/helpers/utils-helpers";
import {getBlockAttributesById} from "../../wp/helpers/wp-helpers";
import ListingsBlockEdit from "../../wp/blocks/listings/ListingsBlockEdit";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import {StateMiddleware} from "../../library/api/StateMiddleware";

const Templates = () => {
    const [api, contextHolder] = notification.useNotification();
    const [isStylesModalOpen, setIsStylesModalOpen] = useState(false);
    const [currentRecord, setCurrentRecord] = useState(null);
    const [listings, setListings] = useState([]);
    const [attributes, setAttributes] = useState({});

    const settingsContext = useContext(SettingsContext);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(app);
    stateMiddleware.setSessionState(session);

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
            title: 'Label',
            dataIndex: 'label',
            key: 'label',
            render: (label) => label,
        },
        {
            title: 'Styles',
            key: 'styles',
            render: (_, record, index) => {
                return (
                    <>
                        <Button
                            onClick={() => {
                                setCurrentRecord(record);
                                setIsStylesModalOpen(true);
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
                Add Template
            </Button>
            <Table columns={columns} dataSource={listings}/>
            {isStylesModalOpen &&
            <Modal title={'Edit Tab Preset'}
                   onOk={() => {
                       if (!currentRecord?.id) {
                           console.error('Id not set in currentRecord')
                           return;
                       }
                       updateListingRequest(currentRecord);
                   }}
                   headerActions={
                       <div>
                           <Button variant="secondary"
                                   onClick={() => {
                                       if (!currentRecord?.id) {
                                           console.error('Id not set in currentRecord')
                                           return;
                                       }
                                       updateListingRequest(currentRecord);
                                   }}>
                               Save
                           </Button>
                       </div>
                   }
                   onRequestClose={() => {
                       setCurrentRecord(null);
                       setIsStylesModalOpen(false);
                   }}>

            </Modal>
            }
        </>
    );
};

export default Templates;
