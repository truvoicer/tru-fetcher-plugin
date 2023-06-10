import React, {useState, useEffect} from 'react';
import {Modal, Card, Select, Space, Form, Row, Col, Button} from 'antd';
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import {APP_STATE} from "../../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../../../components/auth/Auth";
import CustomItemFormFields from "../../components/item/CustomItemFormFields";
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import ItemListSingleItem from "./types/ItemListSingleItem";
import {updateInitialValues, updateMetaHiddenFields} from "../helpers/metaboxes-helpers";

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
const ItemListMetaBoxList = ({session, config}) => {
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const [metaBoxContext, setMetaBoxContext] = useState({
        data: {
            item_list: [],
        },
        updateData: updateData,
        updateByKey: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState[key] = value;
                return cloneState;
            })
        }
    });
    const [isInitialized, setIsInitialized] = useState(false);

    function updateData(key, value) {
        setMetaBoxContext(state => {
            let cloneState = {...state};
            cloneState.data[key] = value;
            return cloneState;
        })
    }

    function updateItemListValue({value, key, index}) {
        const itemList = metaBoxContext.data.item_list;
        const cloneItemList = [...itemList];
        cloneItemList[index][key] = value;
        updateData('item_list', cloneItemList);
    }

    function getTypeSelectValue({index}) {
        if (typeof metaBoxContext.data.item_list[index] === 'undefined') {
            return '';
        }
        return metaBoxContext.data.item_list[index].type;
    }

    function getItemComponent(item, index) {
        switch (item.type) {
            case 'single_item':
                return <ItemListSingleItem
                    onChange={({value, item}) => {
                        updateItemListValue({
                            value,
                            key: item.name,
                            index,
                        })
                    }}
                />
            case 'custom':
                return <CustomItemFormFields
                    onChange={({value, item}) => {
                        updateItemListValue({
                            value,
                            key: item.name,
                            index,
                        })
                    }}
                />
            default:
                return null;
        }
    }


    useEffect(() => {
        if (!isInitialized) {
            return;
        }
        Object.keys(metaBoxContext.data).forEach(field => {
            updateMetaHiddenFields({field, metaBoxContext, fieldGroupId: 'item_list'});
        })
    }, [metaBoxContext])

    useEffect(() => {
        updateInitialValues({fieldGroupId: 'item_list', metaBoxContext, setIsInitialized})
    }, [])
    useEffect(() => {

    }, [])

    function getFormGroup({item, index}) {
        return (
            <Card style={{width: '100%'}}>
                <Row>
                    <Col span={6}>
                        <Form.Item label="Type">
                            <Select
                                style={{minWidth: 180}}
                                options={selectOptions}
                                value={getTypeSelectValue({index})}
                                onChange={(e, data) => {
                                    updateItemListValue({
                                        index,
                                        key: 'type',
                                        value: data.value
                                    })
                                }}
                            />
                        </Form.Item>
                    </Col>
                    <Col span={18}>
                        {getItemComponent(item, index)}
                    </Col>
                </Row>
            </Card>
        )
    }

    return (
        <Auth config={config}>
            <PostMetaBoxContext.Provider value={metaBoxContext}>
                <Row>
                    {metaBoxContext.data.item_list.map((item, index) => {
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
                                updateData('item_list', [
                                    ...metaBoxContext.data.item_list,
                                    {
                                        type: '',
                                        single_item_id: null,
                                        item_image: null,
                                        item_header: null,
                                        item_text: null,
                                        item_rating: null,
                                        item_link_text: null,
                                        item_link: null,
                                        item_badge_text: null,
                                        item_badge_link: null,
                                    }
                                ])
                            }}
                        >
                            Add Row
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
)(ItemListMetaBoxList);