import React from 'react';
import {TextControl, Button, SelectControl} from "@wordpress/components";

export const FIELD_NAME = 'text';
export const FIELD_VALUE = 'list';

const ListComponent = ({
    heading, 
    data = [], 
    onChange, 
    fields = [FIELD_VALUE, FIELD_NAME],
    showSaveButton = false, 
    onSave = () => {}
}) => {
    function addParam() {
        let cloneSearchParam = [...data];
        cloneSearchParam.push({name: '', value: ''});
        onChange(cloneSearchParam);
    }

    function updateParam({index, key, value}) {
        let cloneSearchParam = [...data];
        cloneSearchParam[index][key] = value;
        onChange(cloneSearchParam);
    }

    function deleteParam({index}) {
        let cloneSearchParam = [...data];
        cloneSearchParam.splice(index, 1);
        onChange(cloneSearchParam);
    }

    function renderValueControl({param, index}) {
        switch (param?.type) {
            case 'list':
                return (
                    <ListComponent
                        heading={'List'}
                        data={Array.isArray(param?.value) ? param.value : []}
                        showSaveButton={true}
                        onSave={(data) => {
                            updateParam({
                                index,
                                key: 'value',
                                value: data
                            })
                        }}
                        onChange={(data) => {
                            updateParam({
                                index,
                                key: 'value',
                                value: data
                            })
                        }}/>
                    );
            default:
                return (
                    <TextControl
                        label="Value"
                        placeholder="Value"
                        value={param?.value || ''}
                        onChange={(value) => {
                            updateParam({
                                index,
                                key: 'value',
                                value: value
                            })
                        }}
                    />
                );
        }
    }

    function fieldAllowed(field) {
        return fields.includes(field);
    }

    function renderValue({param, index}) {
        return (
            <>
                <SelectControl
                    label="Type"
                    value={param?.type || 'text'}
                    options={[
                        {label: 'Text', value: 'text'},
                        {label: 'List', value: 'list'},
                    ]}
                    onChange={(value) => {
                        updateParam({
                            index,
                            key: 'type',
                            value: value
                        })
                    }}
                />
                {renderValueControl({param, index})}
            </>
        );
    }

    return (
        <div>
            {heading && <h5>{heading}</h5>}
            {data.map((param, index) => {
                return (
                    <div style={{display: 'flex'}}>
                        {fieldAllowed(FIELD_NAME) &&
                            <TextControl
                                label="Name"
                                placeholder="Name"
                                value={param.name}
                                onChange={(value) => {
                                    updateParam({
                                        index,
                                        key: 'name',
                                        value: value
                                    })
                                }}
                            />
                        }
                        {fieldAllowed(FIELD_VALUE) && renderValue({param, index})}
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
            <div style={{display: 'flex'}}>
                <Button
                    variant="primary"
                    onClick={(e) => {
                        e.preventDefault()
                        addParam()
                    }}
                >
                    Add New
                </Button>
                {showSaveButton &&
                <Button
                    variant="primary"
                    onClick={(e) => {
                        e.preventDefault()
                        onSave(data);
                    }}>
                    Save
                </Button>
                }
            </div>
        </div>
    );
};

export default ListComponent;
