import React, {useState, useEffect} from 'react';
import {Table, Button, Form, Input, Modal} from 'antd';
import EditableRow from "./EditableRow";
import EditableCell from "./EditableCell";
import TableContext, {tableContextData} from "./contexts/TableContext";

const NameValueDatatable = ({
    columns = [],
    dataSource = [],
    selectOptions = [],
    groups = [],
    onDelete,
    onSave,
    onAdd,
    showAddButton = true,
    addFormComponent = null
}) => {

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [tableData, setTableData] = useState([]);
    const [hasGroups, setHasGroups] = useState(false);

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
                    hasGroups,
                    selectOptions,
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
                let defaultData = {
                    type: groupNameData?.type,
                    name: groupNameData?.name,
                    value: '',
                }
                const find = cloneDataSource.find((item) => item?.name === groupNameData?.name) || {};

                return {...defaultData, ...find};
            });
            groupDataSource.push({
                title: group.title,
                type: 'group_header',
            })
            groupDataSource = [...groupDataSource, ...groupData];
        })
        let otherData = [];
        cloneDataSource.forEach((item) => {
            if (!item?.type) {
                return;
            }
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

    useEffect(() => {
        if (groups.length && !hasGroups) {
            setHasGroups(true);
        }
    }, [groups]);

    function closeModal() {
        setTableContextState(prevState => {
            let cloneState = {...prevState};
            let cloneModal = {...cloneState.modal};
            cloneModal.show = false;
            return {...cloneState, modal: cloneModal};
        })
    }
    function handelModalOk() {
        if (typeof tableContextState.modal.onOk === 'function') {
            tableContextState?.modal?.onOk();
        }

    }
    function handelModalCancel() {
        if (typeof tableContextState.modal.onCancel === 'function') {
            tableContextState?.modal?.onCancel();
        }
        closeModal();
    }

    const [tableContextState, setTableContextState] = useState({
        ...tableContextData,
        renderModal: ({title = '', component = null, onOk = () => {}, onCancel = () => {}}) => {
            setTableContextState(prevState => {
                let cloneState = {...prevState};
                let cloneModal = {...cloneState.modal};
                cloneModal.show = true;
                cloneModal.title = title;
                cloneModal.component = component;
                cloneModal.onOk = onOk;
                cloneModal.onCancel = onCancel;
                return {...cloneState, modal: cloneModal};
            })
        },
        closeModal: closeModal
    });
    return (
        <TableContext.Provider value={tableContextState}>
            {showAddButton && (
                <Button onClick={showModal} type="primary" style={{marginBottom: 16}}>
                    Add a row
                </Button>
            )}
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
                        onClick: (event) => {
                        }, // click row
                        onDoubleClick: (event) => {
                        }, // double click row
                        onContextMenu: (event) => {
                        }, // right button click row
                        onMouseEnter: (event) => {
                        }, // mouse enter row
                        onMouseLeave: (event) => {
                        }, // mouse leave row
                    };
                }}
            />
            {showAddButton && (
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
                        {addFormComponent
                            ? addFormComponent
                            : (
                                <>
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
                                </>
                            )}
                    </Form>
                </Modal>
            )}
            <Modal
                title={tableContextState?.modal?.title || ''}
                open={tableContextState?.modal?.show}
                onOk={handelModalOk}
                onCancel={handelModalCancel}
            >
                {tableContextState?.modal?.component || null}
            </Modal>
        </TableContext.Provider>
    );
};

export default NameValueDatatable;
