import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, RangeControl} from "@wordpress/components";
import {findPostTypeSelectOptions, findSingleItemListsPostsSelectOptions} from "../../../../helpers/wp-helpers";
const GeneralTab = (props) => {
    const {
        data,
        onChange
    } = props;

    function addRequestParam() {
        let cloneData = {...data};
        let cloneRequestParams = [...cloneData.request_parameters];
        cloneRequestParams.push({
            name: '',
            value: ''
        });
        onChange({key: 'request_parameters', value: cloneRequestParams});
    }

    function updateRequestParam({requestParamIndex, field, value}) {
        let cloneData = {...data};
        let cloneRequestParams = [...cloneData.request_parameters];
        let cloneRequestParamsItem = {...cloneRequestParams[requestParamIndex]};
        cloneRequestParamsItem[field] = value;
        cloneRequestParams[requestParamIndex] = cloneRequestParamsItem;
        onChange({key: 'request_parameters', value: cloneRequestParams});
    }
    function deleteRequestParam({requestParamIndex}) {
        let cloneData = {...data};
        let cloneRequestParams = [...cloneData.request_parameters];
        cloneRequestParams.splice(requestParamIndex, 1);
        onChange({key: 'request_parameters', value: cloneRequestParams});
    }

    return (
        <div>
            <SelectControl
                label="Carousel Content"
                onChange={(value) => {
                    onChange({key: 'carousel_content', value});
                }}
                value={data?.carousel_content}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Items',
                        value: 'items'
                    },
                    {
                        label: 'Request',
                        value: 'request'
                    },
                ]}
            />
            <SelectControl
                label="Item List"
                onChange={(value) => {
                    onChange({key: 'item_list', value});
                }}
                value={data?.item_list}
                options={[
                    ...[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                    ],
                    ...findPostTypeSelectOptions('fetcher_items_lists')
                ]}
            />

            <TextControl
                placeholder="Tab Label"
                value={ data?.carousel_heading }
                onChange={ ( value ) => {
                    onChange({key: 'carousel_heading', value});
                } }
            />
            <TextControl
                placeholder="carousel_sub_heading"
                value={ data?.carousel_sub_heading }
                onChange={ ( value ) => {
                    onChange({key: 'carousel_sub_heading', value});
                } }
            />
            {data?.carousel_content === 'request' && (
                <div>
                    <h4>Request</h4>

                    <TextControl
                        placeholder="Tab Label"
                        value={ data?.request_name }
                        onChange={ ( value ) => {
                            onChange({key: 'carousel_heading', value});
                        } }
                    />

                    <RangeControl
                        label="Request Limit"
                        initialPosition={50}
                        max={100}
                        min={0}
                        value={data?.request_limit}
                        onChange={(value) => onChange({key: 'request_limit', value})}
                    />
                    <div>
                        <h5>Request Parameters</h5>
                        {data.request_parameters.map((requestParam, requestParamIndex) => {
                            return (
                                <div style={{display: 'flex'}}>
                                    <TextControl
                                        label="Name"
                                        placeholder="Name"
                                        value={requestParam?.name}
                                        onChange={(value) => {
                                            updateRequestParam({
                                                requestParamIndex,
                                                field: 'name',
                                                value
                                            })
                                        }}
                                    />

                                    <TextControl
                                        label="Value"
                                        placeholder="Value"
                                        value={requestParam?.value}
                                        onChange={(value) => {
                                            updateRequestParam({
                                                requestParamIndex,
                                                field: 'value',
                                                value
                                            })
                                        }}
                                    />
                                    <Button
                                        variant="primary"
                                        onClick={(e) => {
                                            e.preventDefault()
                                            deleteRequestParam({requestParamIndex})
                                        }}
                                    >
                                        Delete Request Param
                                    </Button>
                                </div>
                            );
                        })}
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                addRequestParam()
                            }}
                        >
                            Add Option
                        </Button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GeneralTab;
