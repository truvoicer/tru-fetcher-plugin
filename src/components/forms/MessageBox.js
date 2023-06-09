import React from 'react'
import {Alert} from 'antd'

const MessageBox = ({message = '', show = false}) => (
    <>
        {show &&
            <Alert>
                <p>
                    {message}
                </p>
            </Alert>
        }
    </>
)

export default MessageBox
