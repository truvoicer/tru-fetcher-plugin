import React, {useEffect, useState} from 'react';
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable from "../tables/DataTable";
import {STATE_CREATE, STATE_UPDATE} from "../../library/constants/constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";
import FormBuilder from "../forms/FormBuilder";
import {Button, Header, Modal, Popup} from "semantic-ui-react";
import MenuItems from "./menu/MenuItems";

const GeneralSettings = ({session}) => {
    return (
        <div>
            <DataTable
                heading={'General Settings'}
                itemStructure={{
                    name: '',
                    value: '',
                }}
                columns={[
                    {
                        name: 'Name',
                        dataKey: 'name',
                    },
                    {
                        name: 'Value',
                        dataKey: 'value',
                    },
                ]}
                stateHandleCallback={({formItem, formData}) => {
                    let state;
                    const findItem = formData.items.find(item => {
                        if (!formItem?.id && !item?.id) {
                            return false;
                        }
                        return formItem?.id === item?.id;
                    });
                    if (findItem) {
                        state = STATE_UPDATE
                    } else {
                        state = STATE_CREATE
                    }
                    return state;
                }}
                fetchEndpoint={[
                    {
                        name: 'settings',
                        endpoint: 'settings',
                        objectListKey: 'settings'
                    },
                ]}
                formDataCallback={({endpointsObject, setItems, setFormData}) => {
                    const settingsResults = endpointsObject?.settings?.data;
                    if (!Array.isArray(settingsResults)) {
                        return;
                    }
                    const settings = settingsResults.map(setting => {
                        let data = {state: STATE_UPDATE};
                        if (!isObjectEmpty(setting)) {
                            data = {...data, ...setting}
                        }
                        return data
                    });
                    // setItems(optionGroups)
                    setFormData({
                        items: settings
                    })
                }}
                updateEndpoint={({data}) => {
                    if (!isNaN(data?.id)) {
                        return `settings/${data.id}/update`;
                    }
                    return false;
                }}
                createEndpoint={'settings/create'}
                deleteEndpoint={'settings/delete'}
                saveBatchEndpoint={'settings/save'}
                deleteItemCompareKeys={['id']}
                objectListKey={'settings'}
                objectItemKey={'settings'}
                idKey={'id'}>
            </DataTable>
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
)(GeneralSettings);
