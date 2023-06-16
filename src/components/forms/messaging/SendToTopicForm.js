import React, {useEffect, useState} from 'react';
import FormBuilder from "../FormBuilder";
import {setFormConfigItemField} from "../helpers/form-helpers";
import {sendRequest} from "../../../library/api/state-middleware";
import {fetchTopicsRequest, getTopicsSelectData} from "../../../library/api/wp/requests/topic-requests";
import sendToTopic from "../configs/messaging/send-to-topic";

const SendToTopicForm = () => {

    const [formFields, setFormFields] = useState([]);
    const [topics, setTopics] = useState([]);
    const [sendReport, setSendReport] = useState({});


    async function getTopics() {
        const topicsReq = await fetchTopicsRequest();
        if (!Array.isArray(topicsReq)) {
            return;
        }
        setTopics(getTopicsSelectData(topicsReq));
    }
    function buildFormFields() {
        return setFormConfigItemField(sendToTopic, 'topics', 'options', topics);
    }

    useEffect(() => {
        getTopics()
    }, [])
    useEffect(() => {
        if (!topics.length) {
            return;
        }
        setFormFields(buildFormFields())
    }, [topics])

    async function formRequest({formData}) {
        const results = await sendRequest({
            endpoint: 'firebase/messaging/topic/send',
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
        if (Array.isArray(requestData?.topics)) {
            requestData.topics = requestData.topics.map(item => item.value);
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

export default SendToTopicForm;
