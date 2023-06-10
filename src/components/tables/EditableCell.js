import React, {useState, useEffect, useRef, useContext} from 'react';
import {Form, Input} from 'antd';
import EditableContext from "./contexts/EditableContext";
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
        if (editing) {
            inputRef.current.focus();
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
            handleSave({row: { ...record, ...values }, col});
        } catch (errInfo) {
            console.log('Save failed:', errInfo);
        }
    };

    let childNode = children;

    if (col?.editable) {
        childNode = editing ? (
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
        ) : (
            <div className="editable-cell-value-wrap" style={{ paddingRight: 24, height: 20 }} onClick={toggleEdit}>
                {children}
            </div>
        );
    }

    return <td {...restProps}>{childNode}</td>;
};

export default EditableCell;
