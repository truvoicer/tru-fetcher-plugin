import React, {useState, useEffect, useRef, useContext} from 'react';
import {Form, Input, Checkbox, Select, Button} from 'antd';
import EditableContext from "./contexts/EditableContext";
import {isNotEmpty} from "../../../library/helpers/utils-helpers";
import ListComponent from "../../../wp/blocks/components/list/ListComponent";
import MediaPicker from "../../../wp/components/media/MediaPicker";
import TableContext from "./contexts/TableContext";

const EditableCell = ({
    col,
    children,
    record,
    handleSave,
    hasGroups,
    selectOptions = [],
    ...restProps
}) => {
    const [editing, setEditing] = useState(false);
    const inputRef = useRef(null);
    const form = useContext(EditableContext);
    const tableContext = useContext(TableContext);

    function getSelectOptions() {
        if (hasGroups && Array.isArray(record?.options)) {
            return record.options;
        }
        if (Array.isArray(col?.options)) {
            return col.options;
        }
        if (Array.isArray(selectOptions)) {
            return selectOptions;
        }
        return [];
    }

    function getColType() {
        if (hasGroups) {
            return record?.type;
        }
        return col?.type;
    }

    useEffect(() => {
        switch (getColType()) {
            case 'text':
                if (editing) {
                    inputRef.current.focus();
                }
        }
    }, [editing]);

    const toggleEdit = () => {
        setEditing(!editing);
        form.setFieldsValue({[col.dataIndex]: record[col.dataIndex]});
    };

    function getDataIndex() {
        switch (col.dataIndex) {

        }
    }

    const save = async () => {
        try {
            const values = await form.validateFields();
            toggleEdit();
            handleSave({row: {...record, ...values}, col});

        } catch (errInfo) {
            console.log('Save failed:', errInfo);
        }
    };

    function getFormComponent() {
        switch (getColType()) {
            case 'image':
                return (
                    <div style={{display: 'flex', flexDirection: 'column'}}>
                    <img src={form.getFieldValue(col.dataIndex)} style={{width: 80, height: 80}}/>
                    <Form.Item
                        style={{margin: 0}}
                        name={col.dataIndex}
                        rules={[
                            {
                                required: false,
                            },
                        ]}
                    >
                        <MediaPicker
                            text="Select Image"
                            onSelect={(media) => {
                                form.setFieldValue(col.dataIndex, media?.url);
                                save();
                            }}
                        />
                    </Form.Item>
                    </div>
                );
            case 'list':
                return (
                    <Form.Item
                        style={{margin: 0}}
                        name={col.dataIndex}
                        rules={[
                            {
                                required: false,
                            },
                        ]}
                    >
                        <ListComponent
                            heading={col.label}
                            data={form.getFieldValue(col.dataIndex) || []}
                            showSaveButton={true}
                            onSave={(data) => {
                                save();
                            }}
                            onChange={(data) => {
                                form.setFieldValue(col.dataIndex, data);
                                save();
                            }}/>
                    </Form.Item>
                );
            case 'checkbox':
                return (
                    <Form.Item
                        style={{margin: 0}}
                        name={col.dataIndex}
                        rules={[
                            {
                                required: false,
                            },
                        ]}
                    >
                        <Checkbox
                            ref={inputRef}
                            checked={(
                                (isNotEmpty(record?.value) && record.value === '1') ||
                                (isNotEmpty(record?.value) && record.value === 'true') ||
                                (isNotEmpty(record?.value) && record.value === true)
                            )}
                            onChange={(values) => {
                                form.setFieldValue(col.dataIndex, values.target.checked)
                                save()
                            }}
                        />
                    </Form.Item>
                );
            case 'select':
                return (
                    <Form.Item
                        style={{margin: 0}}
                        name={col.dataIndex}
                        rules={[
                            {
                                required: false,
                            },
                        ]}
                    >
                        <Select
                            ref={inputRef}
                            placeholder={col?.label || 'Please Select'}
                            style={{minWidth: 180}}
                            options={getSelectOptions()}
                            value={record?.[col.dataIndex]}
                            onChange={(e, data) => {
                                form.setFieldValue(col.dataIndex, data?.value);
                                save();
                            }}
                        />
                    </Form.Item>
                )
            case 'text':
            case 'url':
                return (
                    <Form.Item
                        style={{margin: 0}}
                        name={col.dataIndex}
                        rules={[
                            {
                                required: (col?.required !== false),
                                message: `${col.title} is required.`,
                            },
                        ]}
                    >
                        <Input ref={inputRef} onPressEnter={save} onBlur={save}/>
                    </Form.Item>
                )
            default:
                return null;
        }
    }
    let childNode = children;

    function getDisplay(data) {
        let Component = data;
        let styles = {paddingRight: 24, height: 20};
        switch (record?.type) {
            case 'list':
                if (Array.isArray(Component) && Component.length === 2 && Array.isArray(Component[1])) {
                    Component[1] = Component[1].map((item, index) => {
                        return `[${item?.name}: ${item?.value}]`;
                    }).join(' | ');
                }
                break;
            case 'image':
                if (!record?.[col.dataIndex]) {
                    Component = null;
                }
                styles.height = 80;
                Component = (
                    <img src={record?.[col.dataIndex]} style={{width: 80, height: 80}}/>
                );
                break;

        }
        return (
            <div className="editable-cell-value-wrap" style={styles} onClick={toggleEdit}>
                {Component}
            </div>
        );
    }

    if (col?.editable) {
        childNode = editing ? (
            <div
                style={{display: 'flex', alignItems: 'flex-end'}}
                // onMouseLeave={() => {
                //     console.log('mouse leave')
                //     setEditing(false)
                // }}
            >
                {getFormComponent(col)}
                <a
                    style={{alignSelf: 'flex-end'}}
                    onClick={(e) => {
                    e.preventDefault();
                    setEditing(false);
                }} style={{marginLeft: 8, fontSize: 11}}>
                    Cancel
                </a>
            </div>
        ) : getDisplay(children);
    }

    return <td {...restProps}>{childNode}</td>;
};

export default EditableCell;
