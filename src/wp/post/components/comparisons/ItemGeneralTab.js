import React,{useState, useEffect, useContext} from 'react';
import {Select} from "semantic-ui-react";
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";

const selectOptions = [
    {
        text: 'Api Data Keys',
        value: 'api_data_keys',
    },
    {
        text: 'Custom',
        value: 'custom',
    }
]
const ItemGeneralTab = ({onChange = false}) => {
    const postMetaBoxContext = useContext(PostMetaBoxContext);
    return (
        <>
            <Select
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

export default ItemGeneralTab;
