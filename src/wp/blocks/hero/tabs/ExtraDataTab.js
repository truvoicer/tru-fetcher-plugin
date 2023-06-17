import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, Button, ToggleControl} from "@wordpress/components";
import {addParam, updateParam} from "../../../helpers/wp-helpers";

const ExtraDataTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    return (
        <div>
                {attributes.hero_extra_data.map((param, index) => {
                    return (
                        <div style={{display: 'flex'}}>
                            <TextControl
                                placeholder="Param Name"
                                value={ attributes.hero_extra_data[index].name }
                                onChange={ ( value ) => {
                                    updateParam({
                                        attr: 'hero_extra_data',
                                        index,
                                        key: 'name',
                                        value: value,
                                        attributes,
                                        setAttributes
                                    })
                                } }
                            />

                            <TextControl
                                placeholder="Param Value"
                                value={ attributes?.hero_extra_data[index].value }
                                onChange={ ( value ) => {
                                    updateParam({
                                        attr: 'hero_extra_data',
                                        index,
                                        key: 'value',
                                        value: value,
                                        attributes,
                                        setAttributes
                                    })
                                } }
                            />
                        </div>
                    );
                })}
                <Button
                    variant="primary"
                    onClick={ (e) => {
                        e.preventDefault()
                        addParam({attr: 'hero_extra_data', attributes, setAttributes})
                    }}
                >
                    Add New
                </Button>
        </div>
    );
};

export default ExtraDataTab;
