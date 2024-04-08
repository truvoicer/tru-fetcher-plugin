import React, {useContext} from 'react';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";
import {Switch, Card, Select, Space, Form, Row, Col, Button} from 'antd';
import {findMetaBoxPostType} from "../../helpers/metaboxes-helpers";

const GeneralTab = ({onChange = false, index, formItem}) => {

    const postMetaBoxContext = useContext(PostMetaBoxContext);
    const singleItemPostType = findMetaBoxPostType('trf_single_item', postMetaBoxContext?.config);

    const selectOptions = buildSelectOptions();
    function buildSelectOptions() {
        const singleItemPosts = postMetaBoxContext?.data?.singleItemPosts;
        if (!Array.isArray(singleItemPosts)) {
            return [];
        }
        return singleItemPosts.map(postType => {
            return {
                label: postType.post_name,
                value: postType.id,
            }
        });
    }

    function getSelectValue() {
        if (typeof postMetaBoxContext.formData.item_list[index] === 'undefined') {
            return '';
        }
        const selectedValue = postMetaBoxContext.formData.item_list[index]?.[singleItemPostType?.idIdentifier];
        if (!selectedValue) {
            return {};
        }
        return selectOptions.find(option => option?.value === selectedValue) || {};
    }

    return (
        <>
            <Form.Item label="Single Item">
                <Select
                    style={{minWidth: 180}}
                    options={selectOptions}
                    value={getSelectValue()}
                    onChange={(e, data) => {
                        if (typeof onChange === 'function') {
                            onChange({value: data.value, item: {name: singleItemPostType?.idIdentifier}})
                        }
                    }}
                />
            </Form.Item>
            <Form.Item label="Override">
                <Switch
                    checked={formItem?.override}
                    onChange={(checked) => {
                        onChange({value: checked, item: {name: 'override'}})
                    }}
                />
            </Form.Item>
        </>
    );
};

export default GeneralTab;
