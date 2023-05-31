import React, {useEffect, useState} from 'react';
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable from "../tables/DataTable";
import {STATE_CREATE, STATE_UPDATE} from "../../library/constants/constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";

const Terms = ({session}) => {
    return (
        <div>
            <DataTable
                heading={'Terms'}
                itemStructure={{
                    name: '',
                    slug: ''
                }}
                columns={[
                    {
                        name: 'Name',
                        dataKey: 'name'
                    },
                    {
                        name: 'Slug',
                        dataKey: 'slug'
                    }
                ]}
                fetchEndpoint={[{
                    name: 'terms',
                    endpoint: 'taxonomy/tr_news_app_categories/terms',
                    objectListKey: 'terms'
                }]}
                updateEndpoint={'taxonomy/tr_news_app_categories/term/update'}
                createEndpoint={'taxonomy/tr_news_app_categories/term/create'}
                deleteEndpoint={'taxonomy/tr_news_app_categories/term/delete'}
                saveBatchEndpoint={'taxonomy/tr_news_app_categories/terms/save'}
                deleteItemCompareKeys={['term_id']}
                objectListKey={'terms'}
                objectItemKey={'terms'}
                idKey={'term_id'}
                formDataCallback={({endpointsObject, setItems, setFormData}) => {
                    const termsResults = endpointsObject?.terms?.data;
                    if (!Array.isArray(termsResults)) {
                        return;
                    }
                    setFormData({
                        items: termsResults
                    })
                }}
            ></DataTable>
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
)(Terms);
