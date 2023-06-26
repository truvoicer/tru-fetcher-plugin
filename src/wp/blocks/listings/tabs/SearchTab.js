import React from 'react';
import {TabPanel, Button, TextControl, RangeControl, SelectControl, ToggleControl} from "@wordpress/components";
import {addParam, updateParam} from "../../../helpers/wp-helpers";

const SearchTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;


    return (
        <>
            <div>
                <RangeControl
                    label="Search Limit"
                    initialPosition={50}
                    max={100}
                    min={0}
                    value={attributes?.search_limit}
                    onChange={(value) => setAttributes({search_limit: value})}
                />
            </div>
            <div>
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
                {attributes?.initial_load === 'search' && (
                    <>
                        <h5>Search Params</h5>
                        {attributes.initial_load_search_params.map((param, index) => {
                            return (
                                <div style={{display: 'flex'}}>
                                    <TextControl
                                        label="Param Name"
                                        placeholder="Param Name"
                                        value={attributes.initial_load_search_params[index].name}
                                        onChange={(value) => {
                                            updateParam({
                                                attr: 'initial_load_search_params',
                                                index,
                                                key: 'name',
                                                value: value,
                                                attributes,
                                                setAttributes
                                            })
                                        }}
                                    />

                                    <TextControl
                                        label="Param Value"
                                        placeholder="Param Value"
                                        value={attributes?.initial_load_search_params[index].value}
                                        onChange={(value) => {
                                            updateParam({
                                                attr: 'initial_load_search_params',
                                                index,
                                                key: 'value',
                                                value: value,
                                                attributes,
                                                setAttributes
                                            })
                                        }}
                                    />
                                </div>
                            );
                        })}
                        <Button
                            variant="primary"
                            onClick={(e) => {
                                e.preventDefault()
                                addParam({attr: 'initial_load_search_params}', attributes, setAttributes})
                            }}
                        >
                            Add New
                        </Button>
                    </>
                )}
                {attributes?.initial_load === 'api_request' && (
                    <>
                        <TextControl
                            label="Request Name"
                            value={attributes?.initial_load_request_name}
                            onChange={(value) => setAttributes({initial_load_request_name: value})}
                        />
                        <RangeControl
                            label="Request Limit"
                            initialPosition={50}
                            max={100}
                            min={0}
                            value={attributes?.initial_load_request_limit}
                            onChange={(value) => setAttributes({initial_load_request_limit: value})}
                        />
                        <h5>Request Params</h5>
                        {attributes.initial_load_request_params.map((param, index) => {
                            return (
                                <div style={{display: 'flex'}}>
                                    <TextControl
                                        label="Param Name"
                                        placeholder="Param Name"
                                        value={ attributes.initial_load_request_params[index].name }
                                        onChange={ ( value ) => {
                                            updateParam({
                                                attr: 'initial_load_request_params',
                                                index,
                                                key: 'name',
                                                value: value,
                                                attributes,
                                                setAttributes
                                            })
                                        } }
                                    />

                                    <TextControl
                                        label="Param Value"
                                        placeholder="Param Value"
                                        value={ attributes?.initial_load_request_params[index].value }
                                        onChange={ ( value ) => {
                                            updateParam({
                                                attr: 'initial_load_request_params',
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
                                addParam('initial_load_request_params')
                            }}
                        >
                            Add New
                        </Button>
                    </>
                )}
            </div>
        </>
    );
};

export default SearchTab;
