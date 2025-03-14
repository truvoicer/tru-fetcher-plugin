import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import tabConfig from "./tab-config";
import BlockEditComponent from '../common/BlockEditComponent';
import { children } from '@wordpress/blocks';

const LoginBlockEdit = (props) => {

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    return (
        <BlockEditComponent
            {...props}
            title='Login Block'
            viewConfig={[
                {
                    title: 'General',
                    children: [
                        { key: 'form_type', name: 'Form Type' },
                        { key: 'email_label', name: 'Email Label' },
                        { key: 'email_placeholder', name: 'Email Placeholder' },
                        { key: 'username_label', name: 'Username Label' },
                        { key: 'username_placeholder', name: 'Username Placeholder' },
                        { key: 'password_label', name: 'Password Label' },
                        { key: 'password_placeholder', name: 'Password Placeholder' },
                        { key: 'submit_text', name: 'Submit Text' },
                        { key: 'cancel_text', name: 'Cancel Text' },
                        { key: 'forgot_password_text', name: 'Forgot Password Text' },
                        { key: 'login_success_message', name: 'Login Success Message' },
                    ]
                }
            ]}
        >
            <Panel>
                <PanelBody title="Hero Block" initialOpen={true}>
                    <TabPanel
                        className="my-tab-panel"
                        activeClass="active-tab"
                        onSelect={(tabName) => {
                            // setTabName(tabName);
                        }}
                        tabs={
                            tabConfig.map((tab) => {
                                return {
                                    name: tab.name,
                                    title: tab.title,
                                }
                            })
                        }>
                        {(tab) => {
                            return (
                                <>
                                    {tabConfig.map((item) => {
                                        if (item.name === tab.name) {
                                            return getTabComponent(item);
                                        }
                                        return null;
                                    })}
                                </>
                            )

                        }}
                    </TabPanel>
                </PanelBody>
            </Panel>
        </BlockEditComponent>
    );
};

export default LoginBlockEdit;
