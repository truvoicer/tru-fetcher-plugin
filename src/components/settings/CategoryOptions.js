import React, {useEffect, useState} from 'react';
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable from "../tables/DataTable";
import {STATE_CREATE, STATE_INITIAL, STATE_UPDATE} from "../../library/constants/constants";
import {isInteger} from "formik";

const CategoryOptions = ({session}) => {
    return (
        <div>
            <DataTable
                heading={'Category Options'}
                itemStructure={{
                    term: {
                        name: ''
                    },
                    category_options: {
                        category_icon: '',
                        category_card_icon_color: '',
                        category_card_bg_color: '',
                        category_card_text_color: '',
                    }
                }}
                columns={[
                    {
                        name: 'Term',
                        fieldElement: 'label',
                        dataKey: 'term_name',
                        // value: ({item, column}) => {
                        //     return item?.term?.name || '';
                        // }
                    },
                    {
                        name: 'Icon',
                        dataKey: 'category_icon',
                        // value: ({item, column}) => {
                        //     return item?.category_options?.category_icon || '';
                        // }
                    },
                    {
                        name: 'Card Icon Color',
                        dataKey: 'category_card_icon_color',
                        // value: ({item, column}) => {
                        //     return item?.category_options?.category_card_icon_color || '';
                        // }
                    },
                    {
                        name: 'Card BG Color',
                        dataKey: 'category_card_bg_color',
                        // value: ({item, column}) => {
                        //     return item?.category_options?.category_card_bg_color || '';
                        // }
                    },
                    {
                        name: 'Card Text Color',
                        dataKey: 'category_card_text_color',
                        // value: ({item, column}) => {
                        //     return item?.category_options?.category_card_text_color || '';
                        // }
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
                        name: 'terms',
                        endpoint: 'taxonomy/tr_news_app_categories/terms',
                        objectListKey: 'terms'
                    },
                    {
                        name: 'categoryOptions',
                        endpoint: 'category/options',
                        objectListKey: 'categoryOptions'
                    }
                ]}
                formDataCallback={({endpointsObject, setItems, setFormData}) => {
                    const termsResults = endpointsObject?.terms?.data;
                    const categoryOptionsResults = endpointsObject?.categoryOptions?.data;
                    if (!Array.isArray(termsResults)) {
                        return;
                    }
                    if (!Array.isArray(categoryOptionsResults)) {
                        return;
                    }
                    const categoryOptions = termsResults.map(term => {
                        const findCategoryOption = categoryOptionsResults.find(categoryOption => {
                            return categoryOption?.term_id === term?.term_id
                        });
                        let formItem = {
                            term_name: term?.name,
                            term_id: term?.term_id,
                            state: STATE_INITIAL
                        };
                        if (findCategoryOption) {
                            return {
                                ...formItem,
                                ...findCategoryOption
                            };
                        }
                        return formItem;
                    });
                    setFormData({
                        items: categoryOptions
                    })
                }}
                updateEndpoint={({data}) => {
                    if (!isNaN(data?.id)) {
                        console.log(data?.id)
                        return `category/options/${data.id}/update`;
                    }
                    return false;
                }}
                createEndpoint={'category/options/create'}
                deleteEndpoint={'category/options/delete'}
                saveBatchEndpoint={'category/options/save'}
                deleteItemCompareKeys={['id']}
                objectListKey={'categoryOptions'}
                objectItemKey={'categoryOptions'}
                idKey={'term_id'}
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
)(CategoryOptions);
