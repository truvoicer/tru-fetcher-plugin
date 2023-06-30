import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import ListComponent from "../../components/list/ListComponent";

const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    return (
        <div>
            <TextControl
                label="Heading"
                placeholder="Heading"
                value={ attributes?.heading }
                onChange={ ( value ) => {
                    setAttributes({heading: value});
                } }
            />
            <ListComponent
                onChange={(params) => {
                    setAttributes({params: params});
                }}
                heading={'Parameters'}
                data={attributes?.params}
            />
        </div>
    );
};

export default GeneralTab;
