import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const DisplayTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <div>
            <TextControl
                label="Heading"
                value={ attributes?.heading }
                onChange={ ( value ) => setAttributes({heading: value}) }
            />
            <SelectControl
                label="Select Item View Display"
                onChange={(value) => {
                    setAttributes({item_view_display: value});
                }}
                value={attributes?.item_view_display}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Modal',
                        value: 'modal'
                    },
                    {
                        label: 'Page',
                        value: 'page'
                    },
                ]}
            />
            <SelectControl
                label="Load More Type"
                onChange={(value) => {
                    setAttributes({load_more_type: value});
                }}
                value={attributes?.load_more_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Pagination',
                        value: 'pagination'
                    },
                    {
                        label: 'Infinite Scroll',
                        value: 'infinite_scroll'
                    },
                ]}
            />
        </div>
    );
};

export default DisplayTab;
