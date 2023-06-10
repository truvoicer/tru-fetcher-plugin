import React, {useState, useEffect} from 'react';
import {Table, Button, Form, Input, Modal} from 'antd';
import EditableRow from "./EditableRow";
import EditableCell from "./EditableCell";

const Datatable = ({columns = [], dataSource = [], onDelete, onSave, onAdd}) => {

    const [isModalOpen, setIsModalOpen] = useState(false);

    const showModal = () => {
        setIsModalOpen(true);
    };

    const handleOk = () => {
        setIsModalOpen(false);
    };

    const handleCancel = () => {
        setIsModalOpen(false);
    };
    const handleDelete = (key) => {
        const newData = dataSource.filter((item) => item.key !== key);
        onDelete({newData, key});
    };

    const handleSave = ({row, col}) => {
        onSave({row, col});
    };

    function getColumns() {
        return columns.map((col) => {
            if (!col.editable) {
                return col;
            }
            return {
                ...col,
                onCell: (record) => ({
                    record,
                    col,
                    handleSave,
                }),
            };
        });
    }

    const components = {
        body: {
            row: EditableRow,
            cell: EditableCell,
        },
    };
    return (
        <>
            <Button onClick={showModal} type="primary" style={{marginBottom: 16}}>
                Add a row
            </Button>
            <Table
                components={components}
                rowClassName={() => 'editable-row'}
                bordered
                dataSource={dataSource}
                columns={getColumns()}
            />
            <Modal title="Basic Modal" open={isModalOpen} onOk={handleOk} onCancel={handleCancel}>
                <Form
                    name="basic"
                    style={{maxWidth: 600}}
                    initialValues={{name: '', value: ''}}
                    onFinish={(values) => {
                        onAdd({values})
                    }}
                    onFinishFailed={errorInfo => {
                        console.log('Failed:', errorInfo);
                    }}
                    autoComplete="off"
                >
                    <Form.Item
                        label="Name"
                        name="name"
                        rules={[{required: true, message: 'Please input your username!'}]}
                    >
                        <Input/>
                    </Form.Item>
                    <Form.Item
                        label="Value"
                        name="value"
                        rules={[{required: true, message: 'Please input your username!'}]}
                    >
                        <Input/>
                    </Form.Item>
                    <Form.Item>
                        <Button type="primary" htmlType="submit">
                            Submit
                        </Button>
                    </Form.Item>
                </Form>
            </Modal>
        </>
    );
};

export default Datatable;
