import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    return (
        <div>
            <SelectControl
                label="Component"
                onChange={(value) => {
                    setAttributes({component: value});
                }}
                value={attributes?.component}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Dashboard',
                        value: 'dashboard'
                    },
                    {
                        label: 'User Profile',
                        value: 'user_profile'
                    },
                    {
                        label: 'Account Details',
                        value: 'account_details'
                    },
                    {
                        label: 'Saved Items',
                        value: 'saved_items'
                    },
                    {
                        label: 'Messages',
                        value: 'messages'
                    },
                ]}
            />
            <TextControl
                label="Tab Label"
                placeholder="Tab Label"
                value={ attributes?.tab_label }
                onChange={ ( value ) => {
                    setAttributes({tab_label: value});
                } }
            />
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={ attributes?.heading }
                onChange={ ( value ) => {
                    setAttributes({heading: value});
                } }
            />
        </div>
    );
};

export default GeneralTab;
