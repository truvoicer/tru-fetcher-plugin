import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const SearchTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <PanelRow>
            <TextControl
                label="Search Limit"
                value={ attributes?.search_limit }
                onChange={ ( value ) => setAttributes({search_limit: value}) }
            />
            <SelectControl
                label="Initial Load"
                onChange={(value) => {
                    setAttributes({initial_load: value});
                }}
                value={attributes?.initial_load}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Search',
                        value: 'search'
                    },
                    {
                        label: 'Api Request',
                        value: 'api_request'
                    },
                ]}
            />

        </PanelRow>
    );
};

export default SearchTab;
