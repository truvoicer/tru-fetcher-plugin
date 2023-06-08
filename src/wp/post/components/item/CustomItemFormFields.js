import React, {useState, useEffect, useContext} from 'react';
import {Col, Row, Select, Button, Modal, Card, Space, Form} from 'antd';
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import {fetchRequest} from "../../../../library/api/middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import buildFormField, {FIELDS} from "../comparisons/fields/field-selector";

const CONFIG = [
    {label: 'Item Image', name: 'item_image', field: FIELDS.IMAGE},
    {label: 'Item Header', name: 'item_header', field: FIELDS.TEXT},
    {label: 'Item Text', name: 'item_text', field: FIELDS.HTML},
    {label: 'Item Rating', name: 'item_rating', field: FIELDS.NUMBER},
    {label: 'Item Link Text', name: 'item_link_text', field: FIELDS.TEXT},
    {label: 'Item Link', name: 'item_link', field: FIELDS.URL},
    {label: 'Item Badge Text', name: 'item_badge_text', field: FIELDS.TEXT},
    {label: 'Item Badge Link', name: 'item_badge_link', field: FIELDS.URL},
]
const CustomItemFormFields = ({onChange}) => {
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const postMetaBoxContext = useContext(PostMetaBoxContext);

    return (
        <>
            {CONFIG.map((item, index) => {
                return (
                    <Form.Item label={item.label}>
                        {buildFormField({
                            fieldType: item.field,
                            value: postMetaBoxContext.data[item.name],
                            index,
                            changeHandler: (value) => {
                                if (typeof onChange === 'function') {
                                    onChange({
                                        value,
                                        item,
                                        index
                                    });
                                }
                            },
                            setModalHeader,
                            setModalComponent,
                            setShowModal
                        })}
                    </Form.Item>
                )
            })}

            <Modal
                title={modalHeader}
                open={showModal}
                onOk={() => setShowModal(false)}
                onCancel={() => setShowModal(false)}
            >
                {modalComponent}
            </Modal>
        </>
    );
};

export default CustomItemFormFields
