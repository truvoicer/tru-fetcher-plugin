import React, {useState, useEffect, useRef, useContext} from 'react';
import {Form, Input, Checkbox} from 'antd';
import EditableContext from "./contexts/EditableContext";
import {isNotEmpty} from "../../../library/helpers/utils-helpers";
const EditableCell = ({
    col,
    children,
    record,
    handleSave,
    ...restProps
}) => {
    const [editing, setEditing] = useState(false);
    const inputRef = useRef(null);
    const form = useContext(EditableContext);

    useEffect(() => {
        switch (record?.type) {
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
            console.log({values});
            // toggleEdit();
            handleSave({row: { ...record, ...values }, col});
        } catch (errInfo) {
            console.log('Save failed:', errInfo);
        }
    };
    function getFormComponent() {
        switch (record?.type) {
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
