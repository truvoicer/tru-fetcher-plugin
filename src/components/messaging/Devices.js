import React from 'react';
import {Button, Header, Popup} from "semantic-ui-react";
import DataTable from "../tables/DataTable";
import {STATE_CREATE, STATE_UPDATE} from "../../library/constants/constants";
import {isObjectEmpty} from "../../library/helpers/utils-helpers";
import OptionsGroupItems from "../settings/OptionsGroupItems";

const Devices = () => {
    return (
        <div>
            <DataTable
                heading={'Devices'}
                itemStructure={{
                    user_id: '',
                    register_token: '',
                    date_created: '',
                    date_updated: '',
                }}
                columns={[
                    {
                        name: 'User ID',
                            dataKey: 'user_id',
                    },
                    {
                        name: 'Register Token',
                        dataKey: 'register_token',
                    },
                    {
                        name: 'Date Created',
                        dataKey: 'date_created',
                    },
                    {
                        name: 'Date Updated',
                        dataKey: 'date_updated',
                    },
                ]}
                fetchEndpoint={[
                    {
                        name: 'devices',
                        endpoint: 'firebase/devices',
                        objectListKey: 'devices'
                    },
                ]}
                formDataCallback={({endpointsObject, setItems, setFormData}) => {
                    const devicesResults = endpointsObject?.devices?.data;
                    if (!Array.isArray(devicesResults)) {
                        return;
                    }
                    const devices = devicesResults.map(device => {
                        let data = {state: STATE_UPDATE};
                        if (!isObjectEmpty(device)) {
                            data = {...data, ...device}
                        }
                        return data
                    });
                    // setItems(optionGroups)
                    setFormData({
                        items: devices
                    })
                }}
                updateEndpoint={({data}) => {
                    if (!isNaN(data?.id)) {
                        return `firebase/device/${data.id}/update`;
                    }
                    return false;
                }}
                createEndpoint={'firebase/device/create'}
                deleteEndpoint={'firebase/device/delete'}
                saveBatchEndpoint={'firebase/device/save'}
                deleteItemCompareKeys={['id']}
                objectListKey={'devices'}
                objectItemKey={'device'}
                idKey={'id'}>
            ></DataTable>
        </div>
    );
};

export default Devices;
