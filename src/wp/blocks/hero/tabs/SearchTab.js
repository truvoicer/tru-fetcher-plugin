import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {fetchRequest} from "../../../../library/api/middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {findPostTypeIdIdentifier, findPostTypeSelectOptions} from "../../../helpers/wp-helpers";

const SearchTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;


    const filterListId = findPostTypeIdIdentifier('trf_filter_list')
    return (
        <div>
            <ToggleControl
                label="Hero Search?"
                checked={attributes?.hero_search}
                onChange={(value) => {
                    setAttributes({hero_search: value});
                }}
            />
            {attributes?.hero_search && (
                <div>
                    <SelectControl
                        label="Categories"
                        onChange={(value) => {
                            setAttributes({[filterListId]: value});
                        }}
                        value={attributes?.[filterListId]}
                        options={[
                            ...[
                            {
                                disabled: true,
                                label: 'Select an Option',
                                value: ''
                            },
                        ],
                            ...findPostTypeSelectOptions('trf_filter_list')
                    ]}
                    />
                    <TextControl
                        label="Categories Placeholder"
                        placeholder="Categories Placeholder"
                        value={attributes?.hero_search__categories_placeholder}
                        onChange={(value) => {
                            setAttributes({hero_search__categories_placeholder: value});
                        }}
                    />
                    <TextControl
                        label="Search Placeholder"
                        placeholder="Search Placeholder"
                        value={attributes?.hero_search__search_placeholder}
                        onChange={(value) => {
                            setAttributes({hero_search__search_placeholder: value});
                        }}
                    />
                    <TextControl
                        label="Location Placeholder"
                        placeholder="Location Placeholder"
                        value={attributes?.hero_search__location_placeholder}
                        onChange={(value) => {
                            setAttributes({hero_search__location_placeholder: value});
                        }}
                    />
                    <TextControl
                        label="Search Button Label"
                        placeholder="Search Button Label"
                        value={attributes?.hero_search__search_button_label}
                        onChange={(value) => {
                            setAttributes({hero_search__search_button_label: value});
                        }}
                    />
                    <TextControl
                        label="Featured Categories Label"
                        placeholder="Featured Categories Label"
                        value={attributes?.hero_search__featured_categories_label}
                        onChange={(value) => {
                            setAttributes({hero_search__featured_categories_label: value});
                        }}
                    />
                    <SelectControl
                        label="Featured Categories"
                        onChange={(value) => {
                            setAttributes({hero_search__featured_categories: value});
                        }}
                        value={attributes?.hero_search__featured_categories}
                        options={[
                            ...[
                                {
                                    disabled: true,
                                    label: 'Select an Option',
                                    value: ''
                                },
                            ],
                            ...findPostTypeSelectOptions('trf_filter_list')
                        ]}
                    />
                </div>
            )}
        </div>
    );
};

export default SearchTab;
