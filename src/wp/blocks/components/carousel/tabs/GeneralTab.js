import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, RangeControl} from "@wordpress/components";
import {findSingleItemListsPostsSelectOptions} from "../../../../helpers/wp-helpers";
const GeneralTab = (props) => {
    const {
        data,
        onChange
    } = props;

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
                    ...findSingleItemListsPostsSelectOptions()
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
                value={ attributes?.carousel_sub_heading }
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

                                        }}
                                    />

                                    <TextControl
                                        label="Value"
                                        placeholder="Value"
                                        value={requestParam?.value}
                                        onChange={(value) => {

                                        }}
                                    />
                                    <Button
                                        variant="primary"
                                        onClick={(e) => {
                                            e.preventDefault()

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
                                addToFormItem({
                                    rowIndex,
                                    formItemIndex,
                                    field: 'options',
                                    defaultValues: {
                                        label: '',
                                        value: ''
                                    }
                                })
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
