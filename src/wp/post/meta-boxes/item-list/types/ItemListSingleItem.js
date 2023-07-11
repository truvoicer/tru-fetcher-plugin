import React,{useContext} from 'react';
import {Select} from 'antd';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";
import {findMetaBoxConfig, findMetaBoxPostType} from "../../helpers/metaboxes-helpers";

const ItemListSingleItem = ({onChange = false, index}) => {
    const postMetaBoxContext = useContext(PostMetaBoxContext);
    const singleItemPostType = findMetaBoxPostType('trf_single_item', postMetaBoxContext?.config);
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
        return postMetaBoxContext.formData.item_list[index]?.[singleItemPostType?.idIdentifier];
    }
    return (
        <>
            <Select
                style={{minWidth: 180}}
                options={buildSelectOptions()}
                value={getSelectValue()}
                onChange={(e, data) => {
                    if (typeof onChange === 'function') {
                        onChange({value: data.value, item: {name: singleItemPostType?.idIdentifier}})
                    }
                }}
            />
        </>
    );
};

export default ItemListSingleItem;
