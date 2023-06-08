import React,{useState, useEffect, useContext} from 'react';
import {Select} from 'antd';
import PostMetaBoxContext from "../../../contexts/PostMetaBoxContext";

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
const ItemListSingleItem = ({onChange = false}) => {
    const postMetaBoxContext = useContext(PostMetaBoxContext);

    function findPostTypeConfig(postTypeSlug) {
        return tru_fetcher_react.postTypes.find(postType => postType.post_type === postTypeSlug);
    }
    function buildSelectOptions() {
        const postTypeConfig = findPostTypeConfig('fetcher_single_item');
        if (!postTypeConfig) {
            return [];
        }
        return postTypeConfig.posts.map(postType => {
            return {
                label: postType.post_name,
                value: postType.ID,
            }
        });
    }
    return (
        <>
            <Select
                style={{minWidth: 180}}
                options={buildSelectOptions()}
                value={''}
                onChange={(e, data) => {
                    if (typeof onChange === 'function') {
                        onChange({value: data.value, item: {name: 'single_item_id'}})
                    }
                }}
            />
        </>
    );
};

export default ItemListSingleItem;
