import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const DataTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    return (
        <div>
            <SelectControl
                label="Hero Type"
                onChange={(value) => {
                    setAttributes({hero_type: value});
                }}
                value={attributes?.hero_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Full Hero',
                        value: 'full_hero'
                    },
                    {
                        label: 'Breadcrumb Hero',
                        value: 'breadcrumb_hero'
                    },
                ]}
            />
            <TextControl
                label="Hero Title"
                placeholder="Hero Title"
                value={ attributes?.hero_title }
                onChange={ ( value ) => {
                    setAttributes({hero_title: value});
                } }
            />
            <TextControl
                label="Hero Text"
                placeholder="Hero Text"
                value={ attributes?.hero_text }
                onChange={ ( value ) => {
                    setAttributes({hero_text: value});
                } }
            />
        </div>
    );
};

export default DataTab;
