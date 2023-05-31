import React, {useState} from 'react';
import {Accordion, Header, Icon} from "semantic-ui-react";
import SendToDeviceForm from "../forms/messaging/SendToDeviceForm";
import SendToTopicForm from "../forms/messaging/SendToTopicForm";

const SendMessage = () => {
    const [activeIndex, setActiveIndex] = useState(0);
    function handleClick(e, titleProps) {
        const {index} = titleProps;
        const newIndex = activeIndex === index ? -1 : index;
        setActiveIndex(newIndex);
    }

    return (
        <div>
            <Header as={'h2'}>Send Messages</Header>
            <Accordion fluid styled>
                <Accordion.Title
                    active={activeIndex === 0}
                    index={0}
                    onClick={handleClick}
                >
                    <Icon name='dropdown' />
                    Send to devices
                </Accordion.Title>
                <Accordion.Content active={activeIndex === 0}>
                    <SendToDeviceForm />
                </Accordion.Content>

                <Accordion.Title
                    active={activeIndex === 1}
                    index={1}
                    onClick={handleClick}
                >
                    <Icon name='dropdown' />
                    Send to topics
                </Accordion.Title>
                <Accordion.Content active={activeIndex === 1}>
                    <SendToTopicForm />
                </Accordion.Content>
            </Accordion>
        </div>
    );
};

export default SendMessage;
