import React from 'react';
import {TextControl, SelectControl, ToggleControl, Button} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import Grid from "../../wp/Grid";

const FormRowsTab = (props) => {
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

    function deleteRow({rowIndex}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        cloneFormRows.splice(rowIndex, 1);
        onChange({key: 'form_rows', value: cloneFormRows});
    }

    function deleteFormItem({rowIndex, formItemIndex, field, isArray = false, arrayIndex}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        let cloneFormRow = {...cloneFormRows[rowIndex]};
        if (isArray) {
            let formItemsArrayField = cloneFormRow.form_items[formItemIndex][field];
            formItemsArrayField.splice(arrayIndex, 1);
            cloneFormRow.form_items[formItemIndex][field] = formItemsArrayField;
        } else {
            let formItems = cloneFormRow.form_items;
            formItems.splice(formItemIndex, 1);
            cloneFormRow.form_items = formItems;
        }
        cloneFormRows[rowIndex] = cloneFormRow;
        onChange({key: 'form_rows', value: cloneFormRows});
    }

    function moveRow({rowIndex, direction}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        let formRow = cloneFormRows[rowIndex];
        let newIndex = rowIndex + direction;
        if (newIndex < 0) {
            newIndex = 0;
        }
        if (newIndex > cloneFormRows.length - 1) {
            newIndex = cloneFormRows.length - 1;
        }
        cloneFormRows.splice(rowIndex, 1);
        cloneFormRows.splice(newIndex, 0, formRow);
        onChange({key: 'form_rows', value: cloneFormRows});
    }

    function moveFormItem({rowIndex, formItemIndex, direction}) {
        let cloneAtts = {...data};
        let cloneFormRows = [...cloneAtts.form_rows];
        let cloneFormRow = {...cloneFormRows[rowIndex]};
        let formItems = cloneFormRow.form_items;
        let formItem = formItems[formItemIndex];
        let newIndex = formItemIndex + direction;
        if (newIndex < 0) {
            newIndex = 0;
        }
        if (newIndex > formItems.length - 1) {
            newIndex = formItems.length - 1;
        }
        formItems.splice(formItemIndex, 1);
        formItems.splice(newIndex, 0, formItem);
        cloneFormRow.form_items = formItems;
        cloneFormRows[rowIndex] = cloneFormRow;
        onChange({key: 'form_rows', value: cloneFormRows});
    }

    return (
        <div className={'tf--form--rows'}>
            {data?.form_rows.map((row, rowIndex) => {
                return (
                    <div className={'tf--form--row'}>
                        <div className={'tf--form--row--body'}>
                            <div className={'tf--form--row--header'}>
                                {`Row ${rowIndex}`}
                                <div className={'tf--form--actions'}>
                                    <a onClick={() => {
                                        moveRow({rowIndex, direction: -1})
                                    }}>
                                        <Icon icon={chevronUp}/>
                                    </a>
                                    <a onClick={() => {
                                        moveRow({rowIndex, direction: 1})
                                    }}>
                                        <Icon icon={chevronDown}/>
                                    </a>
                                    <a onClick={(e) => {
                                        e.preventDefault()
                                        deleteRow({
                                            rowIndex
                                        });
                                    }}>
                                        <Icon icon={trash}/>
                                    </a>
                                </div>
                            </div>
                            <div className={'tf--form--row--items'}>
                                {row?.form_items.map((formItem, formItemIndex) => {
                                    return (
                                        <div className={'tf--form--row--item'}>

                                            <div className={'tf--form--row--item--header'}>
                                                <h4>{`Item ${formItemIndex}`}</h4>
                                                <div className={'tf--form--actions'}>
                                                    <a onClick={() => {
                                                        moveFormItem({rowIndex, formItemIndex, direction: -1})
                                                    }}>
                                                        <Icon icon={chevronUp}/>
                                                    </a>
                                                    <a onClick={() => {
                                                        moveFormItem({rowIndex, formItemIndex, direction: 1})
                                                    }}>
                                                        <Icon icon={chevronDown}/>
                                                    </a>
                                                    <a onClick={(e) => {
                                                        e.preventDefault()
                                                        deleteRow({
                                                            rowIndex
                                                        });
                                                    }}>
                                                        <Icon icon={trash}/>
                                                    </a>
                                                </div>
                                            </div>
                                            <Grid columns={2}>
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
                                            </Grid>
                                            <Grid columns={2}>
                                                <TextControl
                                                    label="Name"
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
                                                    label="Value"
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
                                            </Grid>
                                            <Grid columns={2}>
                                                <TextControl
                                                    label="Placeholder"
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
                                                <TextControl
                                                    label="Classes"
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
                                            </Grid>
                                            <Grid columns={1}>
                                                <TextControl
                                                    label="Description"
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
                                            </Grid>

                                            {[
                                                'select_data_source',
                                                'select',
                                                'checkbox',
                                                'radio',
                                                'image_upload',
                                                'file_upload'
                                            ].includes(formItem?.form_control) && (
                                                <>
                                                    <h5>Control Settings</h5>

                                                    {['select_data_source', 'select'].includes(formItem?.form_control) && (
                                                        <Grid columns={2}>
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
                                                        </Grid>
                                                    )}
                                                    {['select', 'checkbox', 'radio'].includes(formItem?.form_control) && (
                                                        <div>
                                                            <h5>Select Options</h5>
                                                            {formItem.options.map((option, index) => {
                                                                return (
                                                                    <div>
                                                                        <Grid columns={2}>
                                                                            <TextControl
                                                                                label="Label"
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
                                                                                label="Value"
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
                                                                        </Grid>
                                                                        <Button
                                                                            variant="primary"
                                                                            onClick={(e) => {
                                                                                e.preventDefault()
                                                                                deleteFormItem({
                                                                                    rowIndex,
                                                                                    formItemIndex,
                                                                                    field: 'options',
                                                                                    isArray: true,
                                                                                    arrayIndex: index,
                                                                                })
                                                                            }}
                                                                        >
                                                                            Delete Option
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
                                                    )}

                                                    {['select_data_source'].includes(formItem?.form_control) && (
                                                        <Grid columns={1}>
                                                            <TextControl
                                                                label="Endpoint"
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
                                                        </Grid>
                                                    )}

                                                    {['file_upload', 'image_upload'].includes(formItem?.form_control) && (
                                                        <>
                                                            <Grid columns={2}>
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
                                                                    label="Dropzone Message"
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
                                                            </Grid>
                                                            <Grid columns={1}>
                                                                <TextControl
                                                                    label="Accepted File Types Message"
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
                                                            </Grid>
                                                        </>
                                                    )}


                                                    {['image_upload', 'file_upload'].includes(formItem?.form_control) && (
                                                        <div>
                                                            <h5>Allowed File Types</h5>
                                                            {formItem.allowed_file_types.map((fileType, index) => {
                                                                return (
                                                                    <div>
                                                                        <Grid columns={2}>
                                                                        <TextControl
                                                                            label="Extension"
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
                                                                            label="Mime Type"
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
                                                                        </Grid>
                                                                        <Button
                                                                            variant="primary"
                                                                            onClick={(e) => {
                                                                                e.preventDefault()
                                                                                deleteFormItem({
                                                                                    rowIndex,
                                                                                    formItemIndex,
                                                                                    field: 'allowed_file_types',
                                                                                    isArray: true,
                                                                                    arrayIndex: index,
                                                                                })
                                                                            }}
                                                                        >
                                                                            Delete File Type
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
                                                </>
                                            )}

                                            {['button'].includes(formItem?.form_control) && (
                                                <>
                                                    <h5>Button Settings</h5>
                                                    <Grid columns={2}>
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
                                                        label="Button Text"
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
                                                    </Grid>
                                                </>
                                            )}
                                        </div>
                                    )
                                })}
                            </div>
                        </div>
                        <Button
                            className="tf--form--item__button"
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

export default FormRowsTab;
