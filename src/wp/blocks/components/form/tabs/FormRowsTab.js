import React from 'react';
import {TextControl, SelectControl, ToggleControl, Button} from "@wordpress/components";

const EndpointSettingsTab = (props) => {
    const {
        data,
        onChange
    } = props;

    function addFormRow() {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        cloneFormRows.push({form_items: []});
        onChange({key: 'form_rows', value: cloneFormRows});
    }
    function addFormItem({rowIndex}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        let cloneFormRow = {...cloneFormRows[rowIndex]};
        cloneFormRow.form_items.push({
            form_control: '',
            name: '',
            value: '',
            placeholder: '',
            label_position: 'top',
            classes: '',
            description: '',
            multiple: false,
            options: [],
            endpoint: '',
            show_dropzone: false,
            dropzone_message: 'Drop files here',
            accepted_file_types_message: '',
            allowed_file_types: [],
            button_type: 'submit',
            button_text: 'Submit',
        });
        cloneFormRows[rowIndex] = cloneFormRow;
        onChange({key: 'form_rows', value: cloneFormRows});
    }
    function addToFormItem({rowIndex, formItemIndex, field, defaultValues = {}}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        let cloneFormRow = {...cloneFormRows[rowIndex]};
        cloneFormRow.form_items[formItemIndex][field].push(defaultValues);
        cloneFormRows[rowIndex] = cloneFormRow;
        onChange({key: 'form_rows', value: cloneFormRows});
    }

    function updateFormItem({rowIndex, formItemIndex, field, value, isArray = false, arrayIndex, arrayKey}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        let cloneFormRow = {...cloneFormRows[rowIndex]};
        if (isArray) {
            cloneFormRow.form_items[formItemIndex][field][arrayIndex][arrayKey] = value;
        } else {
            cloneFormRow.form_items[formItemIndex][field] = value;
        }
        cloneFormRows[rowIndex] = cloneFormRow;
        onChange({key: 'form_rows', value: cloneFormRows});
    }

    return (
        <div>
            {data?.form_rows.map((row, rowIndex) => {
                return (
                    <div>
                        {row?.form_items.map((formItem, formItemIndex) => {
                            return (
                                <div style={{display: 'flex'}}>
                                    <SelectControl
                                        label="Form Control"
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'form_control',
                                                value
                                            });
                                        }}
                                        value={formItem?.form_control}
                                        options={[
                                            {
                                                disabled: true,
                                                label: 'Select an Option',
                                                value: ''
                                            },
                                            {value: 'text', label: 'Text'},
                                            {value: 'email', label: 'Email'},
                                            {value: 'tel', label: 'Telephone'},
                                            {value: 'password', label: 'Password'},
                                            {value: 'textarea', label: 'Text Area'},
                                            {value: 'select', label: 'Select'},
                                            {value: 'select_data_source', label: 'Select Data Source'},
                                            {value: 'select_countries', label: 'Select Countries'},
                                            {value: 'checkbox', label: 'Checkbox'},
                                            {value: 'radio', label: 'Radio'},
                                            {value: 'image_upload', label: 'Image Upload'},
                                            {value: 'file_upload', label: 'File Upload'},
                                            {value: 'date', label: 'Date'},
                                            {value: 'button', label: 'Button'},
                                        ]}
                                    />
                                    <TextControl
                                        placeholder="Name"
                                        value={formItem?.name}
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'name',
                                                value
                                            });
                                        }}
                                    />
                                    <TextControl
                                        placeholder="Value"
                                        value={formItem?.value}
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'value',
                                                value
                                            });
                                        }}
                                    />
                                    <TextControl
                                        placeholder="Placeholder"
                                        value={formItem?.placeholder}
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'placeholder',
                                                value
                                            });
                                        }}
                                    />
                                    <SelectControl
                                        label="Label Position"
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'label_position',
                                                value
                                            });
                                        }}
                                        value={formItem?.label_position}
                                        options={[
                                            {
                                                disabled: true,
                                                label: 'Select an Option',
                                                value: ''
                                            },
                                            {value: 'top', label: 'Top'},
                                            {value: 'left', label: 'Left'},
                                            {value: 'right', label: 'Right'},
                                        ]}
                                    />
                                    <TextControl
                                        placeholder="Classes"
                                        value={formItem?.classes}
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'classes',
                                                value
                                            });
                                        }}
                                    />
                                    <TextControl
                                        placeholder="Description"
                                        value={formItem?.description}
                                        onChange={(value) => {
                                            updateFormItem({
                                                rowIndex,
                                                formItemIndex,
                                                field: 'description',
                                                value
                                            });
                                        }}
                                    />

                                    <h5>Control Settings</h5>
                                    {['select_data_source', 'select'].includes(formItem?.form_control) && (
                                        <ToggleControl
                                            label="Multiple"
                                            checked={formItem?.multiple}
                                            onChange={(value) => {
                                                updateFormItem({
                                                    rowIndex,
                                                    formItemIndex,
                                                    field: 'multiple',
                                                    value
                                                });
                                            }}
                                        />
                                    )}
                                    {['select_data_source', 'checkbox', 'radio'].includes(formItem?.form_control) && (
                                        <div>
                                            <h5>Options</h5>
                                            {formItem.options.map((option, index) => {
                                                return (
                                                    <div style={{display: 'flex'}}>
                                                        <TextControl
                                                            placeholder="Label"
                                                            value={option?.label}
                                                            onChange={(value) => {
                                                                updateFormItem({
                                                                    rowIndex,
                                                                    formItemIndex,
                                                                    field: 'options',
                                                                    value,
                                                                    isArray: true,
                                                                    arrayIndex: index,
                                                                    arrayKey: 'label'
                                                                });
                                                            }}
                                                        />

                                                        <TextControl
                                                            placeholder="Value"
                                                            value={option?.value}
                                                            onChange={(value) => {
                                                                updateFormItem({
                                                                    rowIndex,
                                                                    formItemIndex,
                                                                    field: 'options',
                                                                    value,
                                                                    isArray: true,
                                                                    arrayIndex: index,
                                                                    arrayKey: 'value'
                                                                });
                                                            }}
                                                        />
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
                                    )}

                                    {['select_data_source'].includes(formItem?.form_control) && (
                                        <TextControl
                                            placeholder="Endpoint"
                                            value={formItem?.endpoint}
                                            onChange={(value) => {
                                                updateFormItem({
                                                    rowIndex,
                                                    formItemIndex,
                                                    field: 'endpoint',
                                                    value
                                                });
                                            }}
                                        />
                                    )}

                                    {['file_upload', 'image_upload'].includes(formItem?.form_control) && (
                                        <>
                                            <ToggleControl
                                                label="Show Dropzone"
                                                checked={formItem?.show_dropzone}
                                                onChange={(value) => {
                                                    updateFormItem({
                                                        rowIndex,
                                                        formItemIndex,
                                                        field: 'show_dropzone',
                                                        value
                                                    });
                                                }}
                                            />

                                            <TextControl
                                                placeholder="Dropzone Message"
                                                value={formItem?.dropzone_message}
                                                onChange={(value) => {
                                                    updateFormItem({
                                                        rowIndex,
                                                        formItemIndex,
                                                        field: 'dropzone_message',
                                                        value
                                                    });
                                                }}
                                            />
                                            <TextControl
                                                placeholder="Accepted File Types Message"
                                                value={formItem?.accepted_file_types_message}
                                                onChange={(value) => {
                                                    updateFormItem({
                                                        rowIndex,
                                                        formItemIndex,
                                                        field: 'accepted_file_types_message',
                                                        value
                                                    });
                                                }}
                                            />
                                        </>
                                    )}


                                    {['image_upload'].includes(formItem?.form_control) && (
                                        <div>
                                            <h5>Allowed File Types</h5>
                                            {formItem.allowed_file_types.map((fileType, index) => {
                                                return (
                                                    <div style={{display: 'flex'}}>
                                                        <TextControl
                                                            placeholder="Extension"
                                                            value={fileType?.extension}
                                                            onChange={(value) => {
                                                                updateFormItem({
                                                                    rowIndex,
                                                                    formItemIndex,
                                                                    field: 'allowed_file_types',
                                                                    value,
                                                                    isArray: true,
                                                                    arrayIndex: index,
                                                                    arrayKey: 'extension'
                                                                });
                                                            }}
                                                        />

                                                        <TextControl
                                                            placeholder="Mime Type"
                                                            value={fileType?.mime_type}
                                                            onChange={(value) => {
                                                                updateFormItem({
                                                                    rowIndex,
                                                                    formItemIndex,
                                                                    field: 'allowed_file_types',
                                                                    value,
                                                                    isArray: true,
                                                                    arrayIndex: index,
                                                                    arrayKey: 'mime_type'
                                                                });
                                                            }}
                                                        />
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
                                                        field: 'allowed_file_types',
                                                        defaultValues: {
                                                            extension: '',
                                                            mime_type: ''
                                                        }
                                                    })
                                                }}
                                            >
                                                Add Option
                                            </Button>
                                        </div>
                                    )}


                                    {['button'].includes(formItem?.form_control) && (
                                        <>
                                            <h5>Button Settings</h5>
                                            <SelectControl
                                                label="Button Type"
                                                onChange={(value) => {
                                                    updateFormItem({
                                                        rowIndex,
                                                        formItemIndex,
                                                        field: 'button_type',
                                                        value
                                                    });
                                                }}
                                                value={formItem?.button_type}
                                                options={[
                                                    {
                                                        disabled: true,
                                                        label: 'Select an Option',
                                                        value: ''
                                                    },
                                                    {value: 'submit', label: 'Submit'},
                                                    {value: 'reset', label: 'Reset'},
                                                ]}
                                            />
                                            <TextControl
                                                placeholder="Button Text"
                                                value={formItem?.button_text}
                                                onChange={(value) => {
                                                    updateFormItem({
                                                        rowIndex,
                                                        formItemIndex,
                                                        field: 'button_text',
                                                        value
                                                    });
                                                }}
                                            />
                                        </>
                                    )}
                                </div>
                            )
                        })}
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                addFormItem({rowIndex})
                            }}
                        >
                            Add Form Item
                        </Button>
                    </div>
                );
            })}
            <Button
                variant="primary"
                onClick={(e) => {
                    e.preventDefault()
                    addFormRow()
                }}
            >
                Add Row
            </Button>
        </div>
    );
};

export default EndpointSettingsTab;
