import React from 'react';
import Grid from "../../../../components/Grid";
import {Button, TextControl, SelectControl} from "@wordpress/components";
const availableTypes = [
    {label: 'Follow', value: 'follow'},
    {label: 'Share', value: 'share'},
];
const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        reducers
    } = props;

    function getTypes() {
        let types = [];
        if (Array.isArray(attributes?.types)) {
            types = [...attributes.types];
        }
        return types;
    }
    function validateType(key, value) {
        switch (key) {
            case 'type':
                return availableTypes.find((type) => type.value === value) !== undefined;
            case 'title':
                return value !== '';
            default:
                return false;
        }
    }
    function addParam() {
        let types = getTypes();
        types.push({type: '', title: ''});
        setAttributes({types: types});
    }

    function updateParam({index, key, value}) {
        let types = getTypes();
        if (!validateType(key, value)) {
            return;
        }
        types[index][key] = value;
        setAttributes({types: types});
    }

    function deleteParam({index}) {
        let types = getTypes();
        types.splice(index, 1);
        setAttributes({types: types});
    }
    return (
        <div style={{padding: 10}}>
            <h3>Types</h3>
            {Array.isArray(attributes?.types) && attributes.types.map((param, index) => {
                return (
                    <Grid columns={3}>
                        <SelectControl
                            label="Type"
                            value={param.type}
                            options={[
                                {label: 'Select Type', value: '', disabled: true},
                                ...availableTypes
                            ]}
                            onChange={(value) => {
                                updateParam({
                                    index,
                                    key: 'type',
                                    value: value
                                })
                            }}
                        />
                        <TextControl
                            label="Title"
                            placeholder="Title"
                            value={param.title}
                            onChange={(value) => {
                                updateParam({
                                    index,
                                    key: 'title',
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
                    </Grid>
                );
            })}
            <Grid columns={4}>
                <Button
                    variant="primary"
                    onClick={(e) => {
                        e.preventDefault()
                        addParam()
                    }}
                >
                    Add New
                </Button>
            </Grid>
        </div>
    );
};

export default GeneralTab;
