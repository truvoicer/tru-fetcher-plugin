import React from 'react';
import { Form, Typography } from 'antd';
import EditableContext from "./contexts/EditableContext";
const { Title } = Typography;

const EditableRow = ({record, rowIndex, groups, ...otherProps}) => {
    const [form] = Form.useForm();
    // console.log({record, rowIndex, groups})
    return (
        <Form form={form} component={false}>
            <EditableContext.Provider value={form}>
                {record?.type === 'group_header'
                    ? (
                        <tr>
                            <td colspan={2}>
                                <Title level={3}>{record?.title || '!Group Header Error'}</Title>
                            </td>
                        </tr>
                    )
                    : (<tr {...otherProps} />)
                }

            </EditableContext.Provider>
        </Form>
    );
};

export default EditableRow;
