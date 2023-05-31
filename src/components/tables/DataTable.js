import React, {useEffect, useState} from 'react';
import {Button, Header, Icon, Table} from "semantic-ui-react";
import {fetchRequest, sendRequest} from "../../library/api/middleware";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import {FieldArray, Form, Formik} from "formik";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";
import {STATE_CREATE, STATE_DELETE, STATE_INITIAL, STATE_UPDATE} from "../../library/constants/constants";
import AddItemButton from "../forms/AddItemButton";
import {
    DEFAULT_FIELD_ELEMENT,
    FIELD_ELEMENT_CHECKBOX,
    FIELD_ELEMENT_LABEL,
    FIELD_ELEMENT_SELECT,
    FIELD_ELEMENT_TEXT
} from "./constants/datatable-constants";
import {getCheckboxInput, getFormLabel, getSelect, getTextInput} from "./fields";
import {isFunction} from "underscore";

export const DATA_SOURCE_API = 'api';
export const DATA_SOURCE_LOCAL = 'local';
const DataTable = ({
    heading = '',
    columns = [],
    objectListKey = null,
    objectItemKey = null,
    objectItemDeleteKey = null,
    idKey = null,
    dataSource = DATA_SOURCE_API,
    localData = null,
    fetchEndpoint = [],
    deleteEndpoint = null,
    createEndpoint = null,
    updateEndpoint = null,
    saveBatchEndpoint = null,
    formDataCallback = false,
    addItemButton = {
        positionClass: 'tr-news-app__form--add-btn--left-bottom'
    },
    itemStructure = false,
    showSaveAllButton = true,
    stateHandleCallback = false,
    deleteItemCompareKeys = [],
    onChange,
    crudCallback = null
}) => {

    if (!isNotEmpty(itemStructure) || !isObject(itemStructure) || isObjectEmpty(itemStructure)) {
        console.error('Item structure prop invalid')
        return null
    }
    const reservedItemKeys = ['state', idKey];
    const [endpointsObject, setEndpointsObject] = useState({});
    const [endpoints, setEndpoints] = useState([]);
    const [items, setItems] = useState([]);
    const [formData, setFormData] = useState({items: []})

    function addItemToFormDataState(item) {
        setFormData(formData => {
            let cloneFormData = {...formData};
            cloneFormData.items.push(item);
            return cloneFormData;
        })
    }

    function setFormState(formData) {
        if (Array.isArray(formData)) {
            formData = formData.map(item => {
                let cloneItem = {...item};
                cloneItem.state = STATE_UPDATE
                return cloneItem;
            })
            setItems(formData)
            // setFormData({items: formData})
        }
    }

    function formStateHandler(formData, endpointData) {
        // if (endpoints.length === 1) {
        // 	setFormState(formData);
        // 	return;
        // }
        if (!Array.isArray(formData)) {
            console.error('Result error')
            return;
        }
        let cloneEndpoints = [...endpoints];
        let findEndpoint = false;
        switch (dataSource) {
            case DATA_SOURCE_API:
                findEndpoint = cloneEndpoints.findIndex(endpoint => endpoint?.endpoint === endpointData?.endpoint);
                break;
            case DATA_SOURCE_LOCAL:
                findEndpoint = cloneEndpoints.findIndex(endpoint => endpoint?.name === endpointData?.name);
                break;
        }
        if (findEndpoint === false) {
            console.error('Cant find endpoint in state')
            return;
        }
        cloneEndpoints[findEndpoint].data = formData
        setEndpointsObject(endpointsObject => {
            let cloneObj = {...endpointsObject};
            cloneObj[endpointData.name] = cloneEndpoints[findEndpoint];
            return cloneObj;
        })
    }

    async function fetchFormItemsRequest(endpointData) {
        const results = await fetchRequest({
            endpoint: endpointData.endpoint
        })
        formStateHandler(
            results?.data[endpointData.objectListKey],
            endpointData
        )
    }

    function addToFormData({insert, remove, push}) {
        let cloneItemStructure = {...itemStructure};
        cloneItemStructure.state = STATE_CREATE;
        push(cloneItemStructure)
    }

    function buildSaveData(data) {
        let saveData = {...data};
        const buildData = saveData.items.filter(item => {
            return (isNotEmpty(item) && isObject(item) && !isObjectEmpty(item) && item?.state !== STATE_INITIAL)
        })
        return buildData;
    }

    function getCrudRequestData({data, state, defaultRequestData}) {
        let cloneData = {...data};
        if (typeof crudCallback === 'function') {
            const crudCallbackData = crudCallback({data: cloneData, objectItemKey, defaultRequestData, state})
            return crudCallbackData || defaultRequestData;
        }
        let requestData = {};
        switch (state) {
            case STATE_DELETE:
            case STATE_UPDATE:
            case STATE_INITIAL:
            case STATE_CREATE:
            default:
                requestData = defaultRequestData;
                break;
        }
        return requestData;
    }

    function buildDeleteEndpoint(data) {
        if (isFunction(deleteEndpoint)) {
            return deleteEndpoint({data});
        }
        if (deleteEndpoint) {
            return deleteEndpoint;
        }
        console.error('Delete endpoint is invalid');
        return false;
    }

    function buildCreateEndpoint(data) {
        if (isFunction(createEndpoint)) {
            return createEndpoint({data});
        }
        if (createEndpoint) {
            return createEndpoint;
        }
        console.error('Create endpoint is invalid');
        return false;
    }

    function buildUpdateEndpoint(data) {
        if (isFunction(updateEndpoint)) {
            return updateEndpoint({data});
        }
        if (updateEndpoint) {
            return updateEndpoint;
        }
        console.error('Update endpoint is invalid');
        return false;
    }

    async function saveItemHandler(data) {
        let cloneData = {...data};
        let results;
        let defaultRequestData = cloneData;
        console.log({defaultRequestData})
        return;
        switch (cloneData?.state) {
            case STATE_UPDATE:
            case STATE_INITIAL:
                const buildUpdateEndpointStr = buildUpdateEndpoint(cloneData);
                if (!buildUpdateEndpointStr) {
                    return;
                }
                results = await crudRequest('put', buildUpdateEndpointStr, getCrudRequestData({
                    data: cloneData,
                    state: cloneData.state,
                    defaultRequestData
                }));
                break;
            case STATE_CREATE:
                const buildCreateEndpointStr = buildCreateEndpoint(cloneData);
                if (!buildCreateEndpointStr) {
                    return;
                }
                results = await crudRequest('post', buildCreateEndpointStr, getCrudRequestData({
                    data: cloneData,
                    state: cloneData.state,
                    defaultRequestData
                }));
                break;
        }
        if (typeof results?.data[objectListKey] === 'undefined') {
            return;
        }
        crudResponseHandler(results)
    }

    function crudResponseHandler(results) {
        const data = results?.data[objectListKey];
        let findEndpointData = endpoints.find(endpoint => endpoint?.name === objectListKey);
        if (!isNotEmpty(findEndpointData)) {
            return;
        }
        formStateHandler(
            data,
            findEndpointData
        );
    }

    function buildDeleteRequestData(data) {
        let cloneData = {...data};
        if (!Array.isArray(deleteItemCompareKeys) || !deleteItemCompareKeys.length) {
            console.warn('deleteItemCompareKeys prop invalid');
            return false;
        }
        let deleteRequestBatchData = [];
        if (!Array.isArray(data) && typeof data === 'object') {
            cloneData = [cloneData];
        }
        cloneData.forEach(item => {
            let deleteRequestData = {};
            deleteItemCompareKeys.map(key => {
                if (item.hasOwnProperty(key) && isNotEmpty(item[key])) {
                    deleteRequestData[key] = item[key];
                }
            })
            deleteRequestBatchData.push(deleteRequestData);
        })
        let deleteKey = objectItemDeleteKey;
        if (!objectItemDeleteKey) {
            deleteKey = objectItemKey;
        }
        return {
            [deleteKey]: deleteRequestBatchData
        };
    }

    async function deleteItemHandler({remove, data, itemIndex, formikProps}) {
        console.log()
        let cloneData = {...data};
        const buildRequestData = buildDeleteRequestData(cloneData);
        if (!buildRequestData) {
            return;
        }
        crudRequest('delete', deleteEndpoint, getCrudRequestData({
            data: cloneData,
            state: STATE_DELETE,
            defaultRequestData: buildRequestData
        })).then(results => {
            const resultsData = results?.data[objectListKey];
            const findInResults = resultsData.find(item => item[idKey] === cloneData[idKey]);
            if (!findInResults) {
                remove(itemIndex);
                setFormData(formData => {
                    let cloneFormData = {...formData};
                    let cloneFormDataItems = [...cloneFormData.items];
                    cloneFormData.items = cloneFormDataItems.filter(item => item[idKey] !== cloneData[idKey]);
                    return cloneFormData;
                })
            }
        }).catch(err => {
            console.error(err)
        })
    }

    async function submitHandler(values) {
        const saveData = buildSaveData(values);
        const results = await crudRequest('post', saveBatchEndpoint, saveData);
        if (typeof results?.data[objectListKey] === 'undefined') {
            return;
        }
        crudResponseHandler(results)
    }

    async function crudRequest(method, endpoint, data) {
        if (!isNotEmpty(method)) {
            console.error('Method is invalid')
            return;
        }
        if (!isNotEmpty(endpoint)) {
            console.error('Endpoint is invalid')
            return;
        }
        return await sendRequest({
            method,
            endpoint,
            data
        });
    }

    function getFormModalComponent(componentName) {
        return componentDeps.find(dep => dep?.name === componentName) || null;
    }

    function getColumns() {
        let buildColumns = columns.map(dataItem => dataItem?.name);
        buildColumns.push('Actions');
        return buildColumns;
    }

    function buildFormData() {
        return {
            items: items.map(item => {
                let itemObj = {};
                reservedItemKeys.forEach(key => {
                    if (isNotEmpty(item[key])) {
                        itemObj[key] = item[key];
                    }
                })
                columns.forEach(column => {
                    itemObj[column.dataKey] = item[column.dataKey];
                })
                return itemObj;
            })
        }
    }

    function buildEndpointData() {
        switch (dataSource) {
            case DATA_SOURCE_API:
                return fetchEndpoint.map(endpoint => {
                    let cloneEndpoint = {...endpoint};
                    cloneEndpoint.data = [];
                    return cloneEndpoint;
                })
            case DATA_SOURCE_LOCAL:
                return [localData];
            default:
                return null;
        }
    }


    function formChangeHandler(fieldData, column, formikProps, event) {
        stateHandler(fieldData, formikProps)
        fieldChangeHandler(fieldData, column, formikProps, event)
        if (typeof onChange === 'function') {
            onChange(fieldData, column, formikProps, event);
        }
    }

    function stateHandler(fieldData, formikProps) {
        const formItem = getItemsRow(fieldData, formikProps);
        let state;
        // console.log({fieldData, formItem, formData})
        if (typeof stateHandleCallback === 'function') {
            state = stateHandleCallback({
                formItem,
                formData
            })
        } else {
            const findItem = formData.items.find(item => {
                if (typeof item[idKey] === 'undefined') {
                    return false;
                }
                if (!item[idKey] && !formItem[idKey]) {
                    return false;
                }
                return formItem[idKey] === item[idKey];
            });
            if (findItem) {
                state = STATE_UPDATE
            } else {
                state = STATE_CREATE
            }
        }
        formikProps.setFieldValue(getFieldName(fieldData.index, 'state'), state);
    }

    function fieldChangeHandler(fieldData, column, {handleChange, setFieldValue}, event) {
        switch (column?.fieldElement) {
            case FIELD_ELEMENT_TEXT:
            case FIELD_ELEMENT_CHECKBOX:
                handleChange(event);
                break;
            case FIELD_ELEMENT_SELECT:
                setFieldValue(fieldData.name, fieldData.value)
                break;
        }
    }

    function getFieldName(index, key) {
        return `items.${index}.${key}`;
    }

    function getItemsRow(fieldData, formikProps) {
        const index = fieldData.index;
        // console.log({formikProps, formData})
        return formikProps.values.items[index];
    }

    function getFormItemDataKeyValue(fieldData, column, formikProps) {
        const formItem = getItemsRow(fieldData, formikProps);
        if (!isNotEmpty(formItem) || typeof formItem[column.dataKey] === 'undefined') {
            return '';
        }
        return formItem[column.dataKey];
    }

    function getFormItemValue(fieldData, column, formikProps) {
        const itemsRow = getItemsRow(fieldData, formikProps);
        const dataKeyValue = getFormItemDataKeyValue(fieldData, column, formikProps);
        let fieldValue;
        if (typeof column?.value === 'function' && isNotEmpty(column?.dataKey)) {
            const cloneFieldData = {...fieldData, value: dataKeyValue};
            fieldValue = column.value({itemsRow, fieldData: cloneFieldData, column, formikProps})
        } else if (typeof column?.value === 'function') {
            fieldValue = column.value({itemsRow, fieldData, column, formikProps})
        } else {
            fieldValue = dataKeyValue;
        }
        return fieldValue;
    }

    function getFieldData(fieldData, column, formikProps) {
        const index = fieldData.index;
        let cloneFieldData = {...fieldData};
        cloneFieldData.name = `items.${index}.${column.dataKey}`;
        const fieldValue = getFormItemValue(cloneFieldData, column, formikProps);
        return {
            ...cloneFieldData,
            value: fieldValue,
        }
    }

    function getFieldElement(fieldData, column, formikProps) {
        let cloneColumn = {...column};
        if (!cloneColumn?.fieldElement) {
            cloneColumn.fieldElement = DEFAULT_FIELD_ELEMENT;
        }
        const buildFieldData = getFieldData(fieldData, column, formikProps);
        if (typeof cloneColumn?.render === 'function') {
            return cloneColumn.render({
                formData,
                fieldData: buildFieldData,
                column: cloneColumn,
                formikProps,
                formChangeCallback: formChangeHandler
            })
        }
        switch (cloneColumn?.fieldElement) {
            case FIELD_ELEMENT_TEXT:
                return getTextInput({
                    fieldData: buildFieldData,
                    column: cloneColumn,
                    formikProps,
                    formChangeCallback: formChangeHandler,
                })
            case FIELD_ELEMENT_LABEL:
                return getFormLabel({
                    fieldData: buildFieldData,
                    column: cloneColumn,
                    formikProps,
                    formChangeCallback: formChangeHandler,
                })
            case FIELD_ELEMENT_CHECKBOX:
                return getCheckboxInput({
                    fieldData: buildFieldData,
                    column: cloneColumn,
                    formikProps,
                    formChangeCallback: formChangeHandler,
                })
            case FIELD_ELEMENT_SELECT:
                return getSelect({
                    fieldData: buildFieldData,
                    column: cloneColumn,
                    formikProps,
                    formChangeCallback: formChangeHandler,
                })
        }
    }

    useEffect(() => {
        setEndpoints(buildEndpointData())
    }, [localData]);

    useEffect(() => {
        if (!Array.isArray(endpoints)) {
            return;
        }

        if (endpoints.length > 1 && typeof formDataCallback !== 'function') {
            console.error('Must have a formdata callback')
            return;
        }

        switch (dataSource) {
            case DATA_SOURCE_API:
                if (endpoints.length === 1) {
                    fetchFormItemsRequest(endpoints[0])
                    return;
                }
                endpoints.forEach(endpoint => {
                    fetchFormItemsRequest(endpoint);
                })
                break;
            case DATA_SOURCE_LOCAL:
                const endpointData = endpoints[0];
                if (endpoints.length === 1) {
                    formStateHandler(
                        endpointData?.data,
                        endpointData
                    )
                    return;
                }
                break;
            default:
                return null;
        }

    }, [endpoints]);

                useEffect(() => {
                    switch (dataSource) {
                        case DATA_SOURCE_API:
                            if (endpoints.length && typeof formDataCallback === 'function') {
                                formDataCallback({endpointsObject, setItems, setFormData})
                            }
                            break;
                        case DATA_SOURCE_LOCAL:
                            if (endpoints.length && typeof formDataCallback === 'function') {
                                formDataCallback({endpointsObject, setItems, setFormData})
                            }
                break;
            default:
                return null;
        }

    }, [endpointsObject]);

    useEffect(() => {
        setFormData(buildFormData())
    }, [items]);
    console.log(formData.items)
    return (
        <div className={'tr-news-app--datatable'}>
            <Header as={'h1'}>{heading || ''}</Header>

            <Formik
                initialValues={formData}
                onSubmit={submitHandler}
                enableReinitialize
            >
                {(formikProps) => {
                    return (
                        <div className={'tr-news-app--datatable--form-container'}>
                            <Form className={'tr-news-app__form'}>
                                <Table celled>
                                    <Table.Header>
                                        <Table.Row>
                                            {getColumns().map((column, index) => {
                                                return (
                                                    <Table.HeaderCell key={index}>{column || ''}</Table.HeaderCell>
                                                )
                                            })}
                                        </Table.Row>
                                    </Table.Header>

                                    <Table.Body className={'tr-news-app__table__tbody'}>
                                        <FieldArray name="items">
                                            {({insert, remove, push}) => {
                                                return (
                                                    <>
                                                        {formikProps.values.items.map((item, itemIndex) => {
                                                                return (
                                                                    <Table.Row key={itemIndex}>
                                                                        {columns.map((column, colIndex) => {
                                                                            return (
                                                                                <Table.Cell
                                                                                    key={colIndex}
                                                                                    className={'tr-news-app__table__tr__td'}>
                                                                                    {getFieldElement({
                                                                                        index: itemIndex
                                                                                    }, column, formikProps)}
                                                                                </Table.Cell>
                                                                            )
                                                                        })}
                                                                        <Table.Cell>
                                                                            <Button
                                                                                icon
                                                                                type={'button'}
                                                                                onClick={() => saveItemHandler(formikProps.values.items[itemIndex])}
                                                                            >
                                                                                <Icon name='save'/>
                                                                            </Button>
                                                                            <Button
                                                                                icon
                                                                                type={'button'}
                                                                                onClick={() => {
                                                                                    deleteItemHandler({
                                                                                        data: formikProps.values.items[itemIndex],
                                                                                        remove,
                                                                                        itemIndex,
                                                                                        formikProps
                                                                                    })
                                                                                }}
                                                                            >
                                                                                <Icon name='delete'/>
                                                                            </Button>
                                                                        </Table.Cell>
                                                                    </Table.Row>
                                                                )
                                                            }
                                                        )}
                                                        {isNotEmpty(addItemButton) && isObject(addItemButton) && !isObjectEmpty(addItemButton) &&
                                                            <AddItemButton
                                                                onClick={() => {
                                                                    addToFormData({insert, remove, push})
                                                                }}
                                                                {...addItemButton}
                                                            />
                                                        }

                                                    </>
                                                )
                                            }}
                                        </FieldArray>
                                    </Table.Body>
                                    <Table.Footer>
                                    </Table.Footer>
                                </Table>
                                <Button
                                    primary
                                    type={'submit'}
                                >
                                    Save
                                </Button>
                            </Form>
                        </div>
                    )
                }}
            </Formik>
        </div>
    );
};

export default connect(
    (state) => {
        return {
            session: state[SESSION_STATE]
        }
    },
    null
)(DataTable);
