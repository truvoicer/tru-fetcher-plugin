import React, {useState, useEffect, useRef, useContext} from 'react';
import {Form, Input, Checkbox, Select} from 'antd';
import EditableContext from "./contexts/EditableContext";
import {isNotEmpty} from "../../../library/helpers/utils-helpers";
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
        form.setFieldsValue({ [col.dataIndex]: record[col.dataIndex] });
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
            case 'checkbox':
                return (
                    <Form.Item
                        style={{ margin: 0 }}
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
                        style={{ margin: 0 }}
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
                        style={{ margin: 0 }}
                        name={col.dataIndex}
                        rules={[
                            {
                                required: true,
                                message: `${col.title} is required.`,
                            },
                        ]}
                    >
                        <Input ref={inputRef} onPressEnter={save} onBlur={save} />
                    </Form.Item>
                )
            default:
                return null;
        }
    }
    let childNode = children;
    if (col?.editable) {
        childNode = editing ? (
            getFormComponent(col)
        ) : (
            <div className="editable-cell-value-wrap" style={{ paddingRight: 24, height: 20 }} onClick={toggleEdit}>
                {children}
            </div>
        );
    }

    return <td {...restProps}>{childNode}</td>;
};

export default EditableCell;
