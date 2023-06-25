import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, RangeControl} from "@wordpress/components";
import {findPostTypeSelectOptions, findSingleItemListsPostsSelectOptions} from "../../../../helpers/wp-helpers";

const GeneralTab = (props) => {
    const {
        data,
        onChange
    } = props;
    console.log({data})
    function addArrayItem({key}) {
        let cloneData = {...data};
        let cloneRequestParams = [...cloneData[key]];
        cloneRequestParams.push({
            name: '',
            value: ''
        });
        onChange({key, value: cloneRequestParams});
    }

    function updateArrayItem({key, index, field, value}) {
        let cloneData = {...data};
        let cloneRequestParams = [...cloneData[key]];
        let cloneRequestParamsItem = {...cloneRequestParams[index]};
        cloneRequestParamsItem[field] = value;
        cloneRequestParams[index] = cloneRequestParamsItem;
        onChange({key, value: cloneRequestParams});
    }

    function deleteArrayItem({key, index}) {
        let cloneData = {...data};
        let cloneRequestParams = [...cloneData[key]];
        cloneRequestParams.splice(index, 1);
        onChange({key, value: cloneRequestParams});
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
            {data?.carousel_content === 'items' && (
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
            )}

            <TextControl
                label="Carousel Heading"
                placeholder="Carousel Heading"
                value={data?.carousel_heading}
                onChange={(value) => {
                    onChange({key: 'carousel_heading', value});
                }}
            />
            <TextControl
                label="carousel_sub_heading"
                placeholder="carousel_sub_heading"
                value={data?.carousel_sub_heading}
                onChange={(value) => {
                    onChange({key: 'carousel_sub_heading', value});
                }}
            />
            {data?.carousel_content === 'request' && (
                <div>
                    <h4>Request</h4>

                    <TextControl
                        label="Tab Label"
                        placeholder="Tab Label"
                        value={data?.request_name}
                        onChange={(value) => {
                            onChange({key: 'carousel_heading', value});
                        }}
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
                        {Array.isArray(data?.request_parameters) && data.request_parameters.map((requestParam, requestParamIndex) => {
                            return (
                                <div style={{display: 'flex'}}>
                                    <TextControl
                                        label="Name"
                                        placeholder="Name"
                                        value={requestParam?.name}
                                        onChange={(value) => {
                                            updateArrayItem({
                                                key: 'request_parameters',
                                                index: requestParamIndex,
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
                                            updateArrayItem({
                                                key: 'request_parameters',
                                                index: requestParamIndex,
                                                field: 'value',
                                                value
                                            })
                                        }}
                                    />
                                    <Button
                                        variant="primary"
                                        onClick={(e) => {
                                            e.preventDefault()
                                            deleteArrayItem({
                                                key: 'request_parameters',
                                                index: requestParamIndex
                                            })
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
                                addArrayItem({key: 'request_parameters'})
                            }}
                        >
                            Add Request Param
                        </Button>
                    </div>
                </div>
            )}
            <div>
                <h5>Carousel Settings</h5>
                {Array.isArray(data?.carousel_settings) && data.carousel_settings.map((carouselSetting, carouselSettingIndex) => {
                    return (
                        <div style={{display: 'flex'}}>
                            <TextControl
                                label="Name"
                                placeholder="Name"
                                value={carouselSetting?.name}
                                onChange={(value) => {
                                    updateArrayItem({
                                        key: 'carousel_settings',
                                        index: carouselSettingIndex,
                                        field: 'name',
                                        value
                                    })
                                }}
                            />

                            <TextControl
                                label="Value"
                                placeholder="Value"
                                value={carouselSetting?.value}
                                onChange={(value) => {
                                    updateArrayItem({
                                        key: 'carousel_settings',
                                        index: carouselSettingIndex,
                                        field: 'value',
                                        value
                                    })
                                }}
                            />
                            <Button
                                variant="primary"
                                onClick={(e) => {
                                    e.preventDefault()
                                    deleteArrayItem({
                                        key: 'carousel_settings',
                                        index: carouselSettingIndex,
                                    })
                                }}
                            >
                                Delete Carousel Setting
                            </Button>
                        </div>
                    );
                })}
                <Button
                    variant="primary"
                    onClick={(e) => {
                        e.preventDefault()
                        addArrayItem({key: 'carousel_settings'})
                    }}
                >
                    Add Carousel Setting
                </Button>
            </div>
        </div>
    );
};

export default GeneralTab;
