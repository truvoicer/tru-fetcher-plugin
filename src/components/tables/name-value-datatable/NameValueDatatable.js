import React, {useState, useEffect} from 'react';
import {Table, Button, Form, Input, Modal} from 'antd';
import EditableRow from "./EditableRow";
import EditableCell from "./EditableCell";

const NameValueDatatable = ({columns = [], dataSource = [], groups = [], onDelete, onSave, onAdd}) => {

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [tableData, setTableData] = useState([]);

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

    function buildTableData() {
        if (!groups.length) {
            setTableData(dataSource);
            return;
        }
        const cloneDataSource = [...dataSource];
        let groupDataSource = [];
        groups.forEach((group) => {
            const groupData = group.names.map((groupNameData) => {
                const defaultData = {
                    type: groupNameData?.type,
                    name: groupNameData?.name,
                    value: '',
                }
                const find = cloneDataSource.find((item) => item?.name === groupNameData?.name);
                if (find) {
                    return {...defaultData, ...find};
                }
                return defaultData
            });
            groupDataSource.push({
                title: group.title,
                type: 'group_header',
            })
            groupDataSource = [...groupDataSource, ...groupData];
        })
        let otherData = [];
        cloneDataSource.forEach((item) => {
            const defaultData = {
                type: item?.type,
                name: item?.name,
                value: '',
            }
            const find = groupDataSource.find((data) => data?.name === item?.name);
            if (!find) {
                otherData.push({...defaultData, ...item});
            }
        });
        if (otherData.length) {
            groupDataSource.push({
                title: 'Other Settings',
                type: 'group_header',
            })
            groupDataSource = [...groupDataSource, ...otherData];
        }
        setTableData(groupDataSource);
    }

    useEffect(() => {
        buildTableData();
    }, [dataSource]);

    return (
        <>
            <Button onClick={showModal} type="primary" style={{marginBottom: 16}}>
                Add a row
            </Button>
            <Table
                pagination={false}
                size={'small'}
                components={components}
                rowClassName={() => 'editable-row'}
                bordered
                dataSource={tableData}
                columns={getColumns()}
                onRow={(record, rowIndex) => {
                    return {
                        groups,
                        record,
                        rowIndex,
                        onClick: (event) => {}, // click row
                        onDoubleClick: (event) => {}, // double click row
                        onContextMenu: (event) => {}, // right button click row
                        onMouseEnter: (event) => {}, // mouse enter row
                        onMouseLeave: (event) => {}, // mouse leave row
                    };
                }}
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

export default NameValueDatatable;
