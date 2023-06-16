import React, {useEffect, useState} from 'react';
import FormBuilder from "../FormBuilder";
import sendToDevice from "../configs/messaging/send-to-device";
import {fetchDevicesRequest, getDevicesSelectData} from "../../../library/api/wp/requests/device-requests";
import {setFormConfigItemField} from "../helpers/form-helpers";
import {isNotEmpty} from "../../../library/helpers/utils-helpers";
import {sendRequest} from "../../../library/api/state-middleware";

const SendToDeviceForm = () => {

    const [formFields, setFormFields] = useState([]);
    const [devices, setDevices] = useState([]);
    const [sendReport, setSendReport] = useState({});


    async function getDevices() {
        const devicesReq = await fetchDevicesRequest();
        if (!Array.isArray(devicesReq)) {
            return;
        }
        setDevices(getDevicesSelectData(devicesReq));
    }
    function buildFormFields() {
        return setFormConfigItemField(sendToDevice, 'devices', 'options', devices);
    }

    useEffect(() => {
        getDevices()
    }, [])
    useEffect(() => {
        if (!devices.length) {
            return;
        }
        setFormFields(buildFormFields())
    }, [devices])

    async function formRequest({formData}) {
        const results = await sendRequest({
            endpoint: 'firebase/messaging/device/send',
            method: 'POST',
            data: formData,
            upload: true
        })
        const report = results?.data?.report;
        if (!report) {
            console.error('No report found');
            return;
        }
        setSendReport(report);
    }
    function submitCallbackHandler(values) {
        const requestData = {...values};
        const formData = new FormData();
        if (Array.isArray(requestData?.devices)) {
            requestData.devices = requestData.devices.map(item => item.value);
        }
        Object.keys(requestData).forEach(key => {
            formData.append(key, requestData[key]);
        });
        formRequest({formData})
    }
    return (
        <div>
            <FormBuilder
                fields={formFields}
                formType={"single"}
                submitCallback={submitCallbackHandler}
                submitButtonText={'Save'}
                showSubmitButton={true}
            />
        </div>
    );
};

export default SendToDeviceForm;
