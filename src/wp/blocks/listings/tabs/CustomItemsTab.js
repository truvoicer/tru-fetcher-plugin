import React from 'react';
import {TabPanel, Panel, PanelBody, RangeControl, SelectControl, ToggleControl} from "@wordpress/components";
import {
    findSingleItemListsPostsSelectOptions,
    getListingsCategoryTermsSelectOptions
} from "../../../helpers/wp-helpers";

const CustomItemsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <>
            <div>
                    <ToggleControl
                        label="List Start"
                        checked={attributes?.list_start}
                        onChange={(value) => {
                            setAttributes({list_start: value});
                        }}
                    />
                    {attributes?.list_start && (
                        <SelectControl
                            label="Listings Category"
                            onChange={(value) => {
                                setAttributes({list_start_items: value});
                            }}
                            value={attributes?.list_start_items}
                            options={[
                                ...[
                                    {
                                        disabled: true,
                                        label: 'Select an Option',
                                        value: ''
                                    },
                                ],
                                ...findSingleItemListsPostsSelectOptions()
                            ]}
                        />
                    )}
            </div>
            <div>
                    <ToggleControl
                        label="List End"
                        checked={attributes?.list_end}
                        onChange={(value) => {
                            setAttributes({list_end: value});
                        }}
                    />
                    {attributes?.list_end && (

                        <SelectControl
                            label="Listings Category"
                            onChange={(value) => {
                                setAttributes({list_end_items: value});
                            }}
                            value={attributes?.list_end_items}
                            options={[
                                ...[
                                    {
                                        disabled: true,
                                        label: 'Select an Option',
                                        value: ''
                                    },
                                ],
                                ...findSingleItemListsPostsSelectOptions()
                            ]}
                        />
                    )}
            </div>
            <div>
                    <ToggleControl
                        label="Custom Position"
                        checked={attributes?.custom_position}
                        onChange={(value) => {
                            setAttributes({custom_position: value});
                        }}
                    />
                    {attributes?.custom_position && (
                        <>
                            <RangeControl
                                label="Insert Index"
                                initialPosition={50}
                                max={100}
                                min={0}
                                value={attributes?.custom_position_insert_index}
                                onChange={(value) => setAttributes({custom_position_insert_index: value})}
                            />
                            <RangeControl
                                label="Per Page"
                                initialPosition={50}
                                max={100}
                                min={0}
                                value={attributes?.custom_position_per_page}
                                onChange={(value) => setAttributes({custom_position_per_page: value})}
                            />
                            <SelectControl
                                label="Listings Category"
                                onChange={(value) => {
                                    setAttributes({custom_position_items: value});
                                }}
                                value={attributes?.custom_position_items}
                                options={[
                                    ...[
                                        {
                                            disabled: true,
                                            label: 'Select an Option',
                                            value: ''
                                        },
                                    ],
                                    ...findSingleItemListsPostsSelectOptions()
                                ]}
                            />
                        </>
                    )}
            </div>
        </>
    );
};

export default CustomItemsTab;
