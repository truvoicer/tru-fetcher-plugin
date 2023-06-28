import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    console.log({attributes})
    return (
        <div>
            <SelectControl
                label="Listing Data Source"
                onChange={(value) => {
                    setAttributes({source: value});
                }}
                value={attributes?.source}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Api',
                        value: 'api'
                    },
                    {
                        label: 'Wordpress',
                        value: 'wordpress'
                    },
                ]}
            />
        </div>
    );
};

export default GeneralTab;
