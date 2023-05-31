import React from 'react'
import {Message} from 'semantic-ui-react'

const MessageBox = ({message = '', show = false}) => (
    <>
        {show &&
            <Message>
                <p>
                    {message}
                </p>
            </Message>
        }
    </>
)

export default MessageBox
