import React from 'react';
import {Header} from "semantic-ui-react";
import {STATE_UPDATE} from "../../library/constants/constants";
import {isObjectEmpty} from "../../library/helpers/utils-helpers";
import DataTable from "../tables/DataTable";

const Topics = () => {
    return (
        <div>
            <DataTable
                heading={'Topics'}
                itemStructure={{
                    topic_name: '',
                    date_created: '',
                    date_updated: '',
                }}
                columns={[
                    {
                        name: 'Topic Name',
                        dataKey: 'topic_name',
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
                        name: 'topics',
                        endpoint: 'firebase/topics',
                        objectListKey: 'topics'
                    },
                ]}
                formDataCallback={({endpointsObject, setItems, setFormData}) => {
                    const topicsResults = endpointsObject?.topics?.data;
                    if (!Array.isArray(topicsResults)) {
                        return;
                    }
                    const topics = topicsResults.map(topic => {
                        let data = {state: STATE_UPDATE};
                        if (!isObjectEmpty(topic)) {
                            data = {...data, ...topic}
                        }
                        return data
                    });
                    // setItems(optionGroups)
                    setFormData({
                        items: topics
                    })
                }}
                updateEndpoint={({data}) => {
                    if (!isNaN(data?.id)) {
                        return `firebase/topic/${data.id}/update`;
                    }
                    return false;
                }}
                createEndpoint={'firebase/topic/create'}
                deleteEndpoint={'firebase/topic/delete'}
                saveBatchEndpoint={'firebase/topic/save'}
                deleteItemCompareKeys={['id']}
                objectListKey={'topics'}
                objectItemKey={'topic'}
                idKey={'id'}>
                ></DataTable>
        </div>
    );
};

export default Topics;
