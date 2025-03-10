import React, { useContext, useEffect, useState } from 'react';
import SettingsContext from "../contexts/SettingsContext";
import { Button, Table, notification, Form, Input } from 'antd';
import { Modal } from '@wordpress/components'
import { findSetting, getSettingListOptions } from '../../wp/helpers/wp-helpers';
import ListComponent, { FIELD_NAME } from '../../wp/blocks/components/list/ListComponent';

const Templates = () => {
    const [api, contextHolder] = notification.useNotification();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalTitle, setModalTitle] = useState('');
    const [currentRecord, setCurrentRecord] = useState(null);
    const [listings, setListings] = useState([]);
    const [attributes, setAttributes] = useState({});

    const settingsContext = useContext(SettingsContext);

    const openNotificationWithIcon = (type, title, message) => {
        api[type]({
            message: title,
            description: message,
        });
    };

    // openNotificationWithIcon('error', 'Error', 'Failed to update listing');
    // openNotificationWithIcon('success', 'Success', 'Listing updated successfully');


    function fetchTemplates() {
        if (!Array.isArray(settingsContext?.settings)) {
            return [];
        }
        return settingsContext.settings.find(setting => setting.name === 'custom_templates');
    }

    const templateSetting = fetchTemplates();

    const customTemplates = Array.isArray(templateSetting?.value) ? templateSetting.value : [];

    function addTemplate(data) {
        let cloneTemplates = [...customTemplates];
        cloneTemplates.push(data);
        updateTemplate(cloneTemplates);
    }


    function updateTemplate(data) {
        if (!templateSetting) {
            settingsContext.createSingleSetting({
                name: 'custom_templates',
                value: data
            });
            return;
        }
        settingsContext.updateSingleSetting({
            id: templateSetting.id,
            value: data
        });
    }


    const columns = [
        {
            title: 'Name',
            dataIndex: 'name',
            key: 'name',
            render: (text) => <a>{text}</a>,
        },
        {
            title: 'Styles',
            key: 'styles',
            render: (_, record, index) => {
                return (
                    <>
                        <Button
                            onClick={() => {
                                setCurrentRecord({ index, record });
                                setModalTitle('Edit Styles');
                                setModalComponent('styles')
                                setIsModalOpen(true);
                            }}
                            type="primary"
                            style={{ marginBottom: 16 }}>
                            Edit Style
                        </Button>
                    </>
                )
            },
        },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record, index) => {
                return (
                    <>
                        <Button
                            onClick={() => {
                                setCurrentRecord({ index, record });
                                setModalTitle('Edit Template');
                                setModalComponent('edit-template')
                                setIsModalOpen(true);
                            }}
                            type="primary"
                            style={{ marginBottom: 16 }}>
                            Edit Template
                        </Button>
                        <Button
                            style={{ marginLeft: 16 }}
                            onClick={() => {
                                let cloneTemplates = [...customTemplates];
                                cloneTemplates.splice(index, 1);
                                updateTemplate(cloneTemplates);
                            }}
                            type="primary">
                            Delete
                        </Button>
                    </>
                )
            },
        }

    ];

    return (
        <>
            {contextHolder}
            <Button
                onClick={() => {
                    setModalTitle('Add Template');
                    setModalComponent('add-template')
                    setIsModalOpen(true);
                }}
                type="primary"
                style={{ marginBottom: 16 }}>
                Add Template
            </Button>
            <Table columns={columns} dataSource={customTemplates} />
            {isModalOpen &&
                <Modal title={modalTitle || ''}
                    onOk={() => {
                        setCurrentRecord(null);
                    }}
                    onRequestClose={() => {
                        setCurrentRecord(null);
                        setIsModalOpen(false);
                    }}>
                    {modalComponent === 'styles' &&
                        <ListComponent
                            fields={[FIELD_NAME]}
                            heading={'Styles'}
                            data={currentRecord?.record?.styles || []}
                            showSaveButton={true}
                            onSave={(data) => {
                                let clone = { ...currentRecord };
                                clone.record.styles = data;
                                setCurrentRecord(clone);

                                let cloneTemplates = [...customTemplates];
                                cloneTemplates[currentRecord.index] = clone.record;
                                updateTemplate(cloneTemplates);
                            }}
                            onChange={(data) => {
                                let clone = { ...currentRecord };
                                clone.record.styles = data;
                                setCurrentRecord(clone);
                                let cloneTemplates = [...customTemplates];
                                cloneTemplates[currentRecord.index] = clone.record;
                                updateTemplate(cloneTemplates);
                            }} />
                    }
                    {(modalComponent === 'add-template' || modalComponent === 'edit-template') &&
                        <Form
                            name="basic"
                            style={{ maxWidth: 600 }}
                            initialValues={{name: (modalComponent === 'edit-template')? currentRecord?.record?.name || '' : ''}}
                            onFinish={(values) => {
                                if (modalComponent === 'edit-template') {
                                    let cloneTemplates = [...customTemplates];
                                    cloneTemplates[currentRecord.index] = { ...currentRecord.record, ...values };
                                    updateTemplate(cloneTemplates);
                                    return;
                                }
                                addTemplate(values);
                            }}
                            onFinishFailed={errorInfo => {
                                console.error(errorInfo);
                            }}
                            autoComplete="off"
                        >
                            <Form.Item
                                label="Name"
                                name="name"
                                rules={[{ required: true, message: 'Please input name!' }]}
                            >
                                <Input />
                            </Form.Item>
                            <Form.Item>
                                <Button type="primary" htmlType="submit">
                                    Submit
                                </Button>
                            </Form.Item>
                        </Form>
                    }
                </Modal>
            }
        </>
    );
};

export default Templates;
