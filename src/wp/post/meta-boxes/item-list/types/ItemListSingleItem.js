import React,{useContext} from 'react';
import {Select} from 'antd';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";

const ItemListSingleItem = ({onChange = false, index}) => {
    const postMetaBoxContext = useContext(PostMetaBoxContext);
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
        return postMetaBoxContext.formData.item_list[index].single_item_id;
    }

    return (
        <>
            <Select
                style={{minWidth: 180}}
                options={buildSelectOptions()}
                value={getSelectValue()}
                onChange={(e, data) => {
                    if (typeof onChange === 'function') {
                        console.log({data})
                        onChange({value: data.value, item: {name: 'single_item_id'}})
                    }
                }}
            />
        </>
    );
};

export default ItemListSingleItem;
