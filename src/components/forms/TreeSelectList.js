import React from 'react';
import {useEffect, useState} from '@wordpress/element';
import {TreeSelect} from '@wordpress/components';
import {isObject} from "../../library/helpers/utils-helpers";

function TreeSelectList({treeData = {}, onChange = (data) => {}, label = 'Please select', selectedId = null}) {
    const [ selectedName, setSelectedName ] = useState( null );

    function getSelectedNameData(isParent = true) {
        if (!isObject(treeData)) {
            return null;
        }
        for (const key of treeData) {
            if (key.id === selectedName) {
                return {
                    parent: true,
                    name: key.name,
                }
            }
            if (!Array.isArray(key?.children) || key.children.length === 0) {
                continue;
            }
            for (const name of key.children) {
                if (name.id === selectedName) {
                    return {
                        parent: false,
                        name: name.name,
                    }
                }
            }
        }
        return null;
    }

    useEffect(() => {
        if (selectedId) {
            setSelectedName(selectedId);
        }
    }, [selectedId]);

    useEffect(() => {
        if (selectedName) {
            onChange(getSelectedNameData());
        }
    }, [selectedName]);

    return (
        <TreeSelect
            __nextHasNoMarginBottom
            label={label}
            // noOptionLabel="No parent page"
            onChange={ ( newPage ) => setSelectedName( newPage ) }
            selectedId={ selectedName }
            tree={treeData}
        />
    );
}

export default TreeSelectList;
