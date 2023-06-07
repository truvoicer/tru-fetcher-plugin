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
    return (
        <>
            <Select
                style={{minWidth: 180}}
                options={selectOptions}
                value={postMetaBoxContext.data.type}
                onChange={(e, data) => {
                    if (typeof onChange === 'function') {
                        postMetaBoxContext.updateData('type', data.value)
                    }
                }}
            />
        </>
    );
};

export default ItemListSingleItem;
