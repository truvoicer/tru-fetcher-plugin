import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {
    const {
        data,
        onChange
    } = props;

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
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={data?.heading}
                onChange={(value) => {
                    onChange({key: 'heading', value: value});
                }}
            />
            <TextControl
                label="Top Text"
                placeholder="Top Text"
                value={data?.top_text}
                onChange={(value) => {
                    onChange({key: 'top_text', value: value});
                }}
            />
            <TextControl
                label="Bottom Text"
                placeholder="Bottom Text"
                value={data?.bottom_text}
                onChange={(value) => {
                    onChange({key: 'bottom_text', value: value});
                }}
            />
            <TextControl
                label="Complete Text"
                placeholder="Complete Text"
                value={data?.complete_text}
                onChange={(value) => {
                    onChange({key: 'complete_text', value: value});
                }}
            />
            <TextControl
                label="Not Complete Text"
                placeholder="Not Complete Text"
                value={data?.not_complete_text}
                onChange={(value) => {
                    onChange({key: 'not_complete_text', value: value});
                }}
            />
            <div>
                <h5>Form Field Groups</h5>
                {data.form_field_groups.map((group, index) => {
                    return (
                        <div style={{display: 'flex'}}>
                            <TextControl
                                label="Name"
                                placeholder="Name"
                                value={ group.name }
                                onChange={ ( value ) => {
                                    updateArrayItem({
                                        key: 'form_field_groups',
                                        index,
                                        field: 'name',
                                        value: value,
                                    });
                                } }
                            />

                            <TextControl
                                label="Percentage"
                                placeholder="Percentage"
                                value={ group.percentage }
                                onChange={ ( value ) => {
                                    updateArrayItem({
                                        key: 'form_field_groups',
                                        index,
                                        field: 'percentage',
                                        value: value,
                                    });
                                } }
                            />
                            <Button
                                variant="primary"
                                onClick={ (e) => {
                                    e.preventDefault()
                                    deleteArrayItem({key: 'form_field_groups', index})
                                }}
                            >
                                Delete
                            </Button>
                        </div>
                    );
                })}
                <Button
                    variant="primary"
                    onClick={ (e) => {
                        e.preventDefault()
                        addArrayItem({key: 'form_field_groups'})
                    }}
                >
                    Add New
                </Button>
            </div>
        </div>
    );
};

export default GeneralTab;
