import React from 'react';
import {TreeSelect} from "@wordpress/components";
import {useEffect, useState} from '@wordpress/element';
import {isObject} from "../library/helpers/utils-helpers";

const ApiRequestTreeSelect = ({config = [], label, onChange, selectedId}) => {

    const [requestData, setRequestData] = useState({});
    const [treeData, setTreeData] = useState([]);
    const [selectedTreeId, setSelectedTreeId] = useState(null);

    function findSelectedNameData(data, id) {
        if (data?.id === id) {
            return data;
        }
        if (Array.isArray(data.children)) {
            for (const child of data.children) {
                const selected = findSelectedNameData(child, id);
                if (selected) {
                    return selected;
                }
            }
        }
        return null;
    }

    function getSelectedNameData(data, id) {
        for (const child of data) {
            const findSelected = findSelectedNameData(child, id);
            if (findSelected) {
                return findSelected;
            }
        }
        return null;
    }

    function findChildConfigItem(item, name) {
        if (item.name === name) {
            return item;
        }
        if (isObject(item?.child)) {
            const find = findChildConfigItem(item.child, name);
            if (find) {
                return find;
            }
        }
        return null;
    }
    function findConfigItem(data, name) {
        for (const item of data) {
            const findChild = findChildConfigItem(item, name);
            if (findChild) {
                return findChild;
            }
        }
        return null;
    }
    function getConfigItemById(id) {
        const getSelectedData = getSelectedNameData(treeData, id);
        const findCategory = findConfigItem(config, getSelectedData?.configName);
        if (!findCategory) {
            return false;
        }
        return findCategory;
    }
    async function selectHandler(id) {
        const findCategory = getConfigItemById(id);
        if (!findCategory) {
            return;
        }
        if (typeof findCategory?.onSelect !== 'function') {
            return;
        }
        const getSelectedData = getSelectedNameData(treeData, id);
        const fetchData = await findCategory.onSelect(getSelectedData);
        if (!Array.isArray(fetchData)) {
            console.warn('onSelect did not return an array');
            return;
        }
        if (typeof findCategory?.child !== 'object') {
            return;
        }
        setRequestData(prevState => {
            let cloneState = {...prevState};
            if (!isObject(cloneState?.[findCategory?.child.name])) {
                cloneState[findCategory?.child.name] = {};
            }
            if (!Array.isArray(cloneState?.[findCategory?.child.name]?.[id])) {
                cloneState[findCategory?.child.name][id] = [];
            }
            cloneState[findCategory?.child.name][id] = fetchData;
            return cloneState;
        })
    }

    function buildChildItems(item, id = null) {
        if (typeof item?.getId !== 'function') {
            console.warn('getId is not a function');
            return false;
        }
        if (typeof item?.getOptions !== 'function') {
            console.warn('getOptions is not a function');
            return false;
        }
        let itemData;
        if (item?.root) {
             itemData = requestData?.[item.name];
        } else {
            itemData = requestData?.[item.name]?.[id];
        }

        if (!Array.isArray(itemData)) {
            return false;
        }
        const buildOptions = item.getOptions(itemData);

        return buildOptions.map((child) => {
            let cloneChild = {...child};
            cloneChild.rawId = child.id;
            cloneChild.id = item.getId(child);
            cloneChild.configName = item.name;
            if (item?.child) {
                cloneChild.children = buildChildItems(item.child, item.getId(child));
            }
            return cloneChild;
        });
    }

    function buildTreeConfigItem(item) {
        let data = {
            name: item?.label || item?.name,
            id: item.name,
            configName: item.name,
            root: item?.root || false
        };
        const buildChildren = buildChildItems(item);
        if (Array.isArray(buildChildren)) {
            data.children = buildChildren;
        }

        return data;
    }

    function buildTree() {
        return config.map((item) => {
            return buildTreeConfigItem(item);
        });
    }

    async function treeDataInit() {
        let reqData = {};
        for (const item of config) {
            if (typeof item?.getData !== 'function') {
                console.warn('getData is not a function');
                return data;
            }
            const itemData = await item.getData();

            if (!Array.isArray(itemData)) {
                return data;
            }
            reqData[item.name] = itemData;
        }
        setRequestData(prevState => {
            let cloneState = {...prevState};
            Object.keys(reqData).forEach((key) => {
                cloneState[key] = reqData[key];
            });
            return cloneState;
        })
    }

    useEffect(() => {
        treeDataInit();
    }, []);

    useEffect(() => {
        setTreeData(buildTree());
    }, [requestData]);

    useEffect(() => {
        if (!selectedId) {
            return;
        }
        setSelectedTreeId(selectedId);
    }, [selectedId]);

    useEffect(() => {
        if (!selectedTreeId) {
            return;
        }
        selectHandler(selectedTreeId);
        if (typeof onChange === 'function') {
            const selectedData = getSelectedNameData(treeData, selectedTreeId);
            if (!selectedData) {
                return;
            }
            const getConfigItem = getConfigItemById(selectedTreeId);
            if (!getConfigItem) {
                return;
            }

            if (getConfigItem?.returnValue === false) {
                return;
            }
            onChange(selectedData);
        }
    }, [selectedTreeId]);

    return (
        <TreeSelect
            __nextHasNoMarginBottom
            label={label}
            // noOptionLabel="No parent page"
            onChange={(newPage) => setSelectedTreeId(newPage)}
            selectedId={selectedTreeId}
            tree={treeData}
        />
    );

}

export default ApiRequestTreeSelect;
