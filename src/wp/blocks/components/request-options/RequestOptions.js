import React from 'react';
import {TextControl, RangeControl, Button} from "@wordpress/components";

const RequestOptions = (props) => {

    const {
        data,
        onChange
    } = props;

    function addParam() {
        let cloneData = {...data};
        let cloneParams = [...cloneData.params];
        cloneParams.push({
            name: '',
            value: ''
        });
        onChange({key: 'params', value: cloneParams});
    }

    function updateParam({index, field, value}) {
        let cloneData = {...data};
        let cloneParams = [...cloneData.params];
        cloneParams[index][field] = value;
        onChange({key: 'params', value: cloneParams});
    }
    function deleteParam({index}) {
        let cloneData = {...data};
        let cloneParams = [...cloneData.params];
        cloneParams.splice(index, 1);
        onChange({key: 'params', value: cloneParams});
    }

    return (
        <div>
            <TextControl
                label="Name"
                placeholder="Name"
                value={data?.name}
                onChange={(value) => {
                    onChange({key: 'name', value: value});
                }}
            />

            <RangeControl
                label="Limit"
                initialPosition={50}
                max={100}
                min={0}
                value={data?.limit}
                onChange={(value) => onChange({key: 'limit', value})}
            />

            <h5>Request Params</h5>
            {Array.isArray(data?.params) && data.params.map((param, index) => {
                return (
                    <div style={{display: 'flex'}}>
                        <TextControl
                            label="Param Name"
                            placeholder="Param Name"
                            value={param.name}
                            onChange={(value) => {
                                updateParam({
                                    index,
                                    key: 'name',
                                    value: value,
                                })
                            }}
                        />

                        <TextControl
                            label="Param Value"
                            placeholder="Param Value"
                            value={param.value}
                            onChange={(value) => {
                                updateParam({
                                    index,
                                    key: 'value',
                                    value: value,
                                })
                            }}
                        />
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                deleteParam({index})
                            }}
                        >
                            Delete
                        </Button>
                    </div>
                );
            })}
            <Button
                variant="primary"
                onClick={(e) => {
                    e.preventDefault()
                    addParam()
                }}
            >
                Add New
            </Button>
        </div>
    );
};

export default RequestOptions;
