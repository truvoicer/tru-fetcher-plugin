import React from 'react';
import { TextControl, Button, SelectControl, RadioControl } from "@wordpress/components";

const ListComponent = ({
    heading, 
    data = [], 
    onChange, 
    showSaveButton = false, 
    associative = true,
    onSave = () => {}
}) => {
    function addParam() {
        let cloneSearchParam = [...data];
        cloneSearchParam.push({ name: '', value: '', type: 'text', list_type: 'assoc' });
        onChange(cloneSearchParam);
    }

    function updateParam({ index, key, value }) {
        let cloneSearchParam = [...data];
        cloneSearchParam[index][key] = value;
        onChange(cloneSearchParam);
    }

    function deleteParam({ index }) {
        let cloneSearchParam = [...data];
        cloneSearchParam.splice(index, 1);
        onChange(cloneSearchParam);
    }

    function renderValueControl({ index, param }) {
        switch (param?.type) {
            case 'list':
                return (
                    <>
                    <RadioControl
                        label="List Type"
                        help="Select the type of list"
                        selected={param?.list_type || 'assoc'}
                        options={ [
                            { label: 'Assoc', value: 'assoc' },
                            { label: 'Indexed', value: 'indexed' }
                        ] }
                        onChange={ ( option ) => {
                            updateParam({
                                index,
                                key: 'list_type',
                                value: option
                            })
                        } }
                    />
                    <ListComponent
                        associative={param?.list_type === 'assoc'}
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
                        }}
                    />
                    </>
                );
            default:
                return (
                    <TextControl
                        label="Value"
                        placeholder="Value"
                        value={param.value}
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

    function renderValue({ param, index }) {
        return (
            <div>
                <SelectControl
                    label="Type"
                    value={param.type || 'text'}
                    options={[
                        { label: 'Text', value: 'text' },
                        { label: 'List', value: 'list' }
                    ]}
                    onChange={(value) => {
                        updateParam({
                            index,
                            key: 'type',
                            value: value
                        })
                    }} />
                {renderValueControl({ param, index })}
            </div>
        )
    }

    return (
        <div>
            {heading && <h5>{heading}</h5>}
            {data.map((param, index) => {
                return (
                    <div style={{ display: 'flex' }}>
                        {associative && (
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
                        )}
                        {renderValue({ param, index })}
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                deleteParam({ index })
                            }}
                        >
                            Delete
                        </Button>
                    </div>
                );
            })}
            <div style={{ display: 'flex' }}>
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
