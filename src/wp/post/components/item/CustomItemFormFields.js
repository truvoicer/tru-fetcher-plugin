import React, {useState, useEffect, useContext} from 'react';
import {Col, Row, Select, Button, Modal, Card, Space, Form} from 'antd';
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import {fetchRequest} from "../../../../library/api/state-middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import buildFormField, {FIELDS} from "../comparisons/fields/field-selector";

export const CONFIG = [
    {label: 'Item Logo BG', name: 'item_logo_bg', field: FIELDS.TEXT},
    {label: 'Item Logo', name: 'item_logo', field: FIELDS.IMAGE},
    {label: 'Item Image', name: 'item_image', field: FIELDS.IMAGE},
    {label: 'Item Header', name: 'item_header', field: FIELDS.TEXT},
    {label: 'Item Content', name: 'item_content', field: FIELDS.HTML},
    {label: 'Item Excerpt', name: 'item_excerpt', field: FIELDS.HTML},
    {label: 'Item Rating', name: 'item_rating', field: FIELDS.NUMBER},
    {label: 'Item Offer', name: 'item_offer', field: FIELDS.TEXT},
    {label: 'Item Link Text', name: 'item_link_text', field: FIELDS.TEXT},
    {label: 'Item Link', name: 'item_link', field: FIELDS.URL},
    {label: 'Item Ribbon Text', name: 'item_ribbon_text', field: FIELDS.TEXT},
    {label: 'Item Ribbon Link', name: 'item_ribbon_link', field: FIELDS.URL},
    {label: 'Item Badge Text', name: 'item_badge_text', field: FIELDS.TEXT},
    {label: 'Item Badge Link', name: 'item_badge_link', field: FIELDS.URL},
    {label: 'Provider', name: 'provider', field: FIELDS.TEXT},
]
const CustomItemFormFields = ({onChange, formItem}) => {
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
                            value: formItem[item.name],
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
