import React from 'react';
import {TextControl, Button} from "@wordpress/components";

const ListComponent = ({
    heading, data = [], onChange, showSaveButton = false, onSave = () => {
    }
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

    return (
        <div>
            {heading && <h5>{heading}</h5>}
            {data.map((param, index) => {
                return (
                    <div style={{display: 'flex'}}>
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
