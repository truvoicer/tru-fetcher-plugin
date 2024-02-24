import React, {useState, useEffect} from 'react';
import {Modal, Card, Select, Space, Form, Row, Col, Button, Input} from 'antd';
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import {APP_STATE} from "../../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../../../components/auth/Auth";
import CustomItemFormFields from "../../components/item/CustomItemFormFields";
import ItemListSingleItem from "./types/ItemListSingleItem";
import {updateInitialValues, updateMetaHiddenFields} from "../helpers/metaboxes-helpers";
import {fetchRequest} from "../../../../library/api/state-middleware";
import config from "../../../../library/api/wp/config";

const selectOptions = [
    {
        label: 'Single Item',
        value: 'single_item',
    },
    {
        label: 'Custom',
        value: 'custom',
    }
]
const FilterListsMetaBox = ({session}) => {
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const [metaBoxContext, setMetaBoxContext] = useState({
        formData: {
            list_items: [],
        },
        updateFormData: updateFormData,
        updateByKey: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState[key] = value;
                return cloneState;
            })
        }
    });
    const [isInitialized, setIsInitialized] = useState(false);

    function updateFormData(key, value) {
        setMetaBoxContext(state => {
            let cloneState = {...state};
            cloneState.formData[key] = value;
            return cloneState;
        })
    }

    function updateItemListValue({value, key, index}) {
        const itemList = metaBoxContext.formData.list_items;
        const cloneItemList = [...itemList];
        cloneItemList[index][key] = value;
        updateFormData('list_items', cloneItemList);
    }

    useEffect(() => {
        if (!isInitialized) {
            return;
        }
        Object.keys(metaBoxContext.formData).forEach(field => {
            updateMetaHiddenFields({field, metaBoxContext, fieldGroupId: 'filter_lists'});
        })
    }, [metaBoxContext])
    useEffect(() => {
        updateInitialValues({fieldGroupId: 'filter_lists', metaBoxContext, setIsInitialized})
    }, [])

    function getFormGroup({item, index}) {
        return (
            <Card style={{width: '100%'}}>
                <Row>
                    <Col span={6}>
                        <Form.Item label="Name">
                            <Input
                                placeholder={"Name"}
                                value={item?.name || ''}
                                type={'text' }
                                onChange={ ( e ) => {
                                    updateItemListValue({
                                        value: e.target.value,
                                        key: 'name',
                                        index,
                                    })
                                }}
                            />
                        </Form.Item>
                    </Col>
                    <Col span={18}>
                        <Form.Item label="Label">
                            <Input
                                placeholder={"Label"}
                                value={item?.label || ''}
                                type={'text' }
                                onChange={ ( e ) => {
                                    updateItemListValue({
                                        value: e.target.value,
                                        key: 'label',
                                        index,
                                    })
                                }}
                            />
                        </Form.Item>
                    </Col>
                </Row>
            </Card>
        )
    }

    return (
        <Auth>
            <PostMetaBoxContext.Provider value={metaBoxContext}>
                <Row>
                    {metaBoxContext.formData.list_items.map((item, index) => {
                        return (
                            <Col key={index} span={24}>
                                {getFormGroup({item, index})}
                            </Col>
                        )
                    })}
                </Row>
                <Row>
                    <Col>
                        <Button
                            type={'primary'}
                            onClick={(e) => {
                                e.preventDefault();
                                updateFormData('list_items', [
                                    ...metaBoxContext.formData.list_items,
                                    {
                                        name: '',
                                        label: '',
                                    }
                                ])
                            }}
                        >
                            Add Filter
                        </Button>
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
            </PostMetaBoxContext.Provider>
        </Auth>
    );
}

export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(FilterListsMetaBox);
