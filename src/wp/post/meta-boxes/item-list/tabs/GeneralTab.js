import React, {useContext} from 'react';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";
import {Modal, Card, Select, Space, Form, Row, Col, Button} from 'antd';

const selectOptions = [
    {
        label: 'Single Item',
        value: 'single_item',
    },
]
const GeneralTab = ({onChange = false, index}) => {

    const postMetaBoxContext = useContext(PostMetaBoxContext);

    function getTypeSelectValue({index}) {
        if (typeof postMetaBoxContext.formData.item_list[index] === 'undefined') {
            return '';
        }
        return postMetaBoxContext.formData.item_list[index].type;
    }
    return (
        <Form.Item label="Type">
            <Select
                style={{minWidth: 180}}
                options={selectOptions}
                value={getTypeSelectValue({index})}
                onChange={(e, data) => {
                    onChange({value: data.value, item: {name: 'type'}})
                }}
            />
        </Form.Item>
    );
};

export default GeneralTab;
