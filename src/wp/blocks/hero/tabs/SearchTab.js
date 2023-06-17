import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {fetchRequest} from "../../../../library/api/middleware";
import fetcherApiConfig from "../../../../library/api/fetcher-api/fetcherApiConfig";
import {findPostTypeSelectOptions} from "../../../helpers/wp-helpers";

const SearchTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

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
                            setAttributes({hero_search__categories: value});
                        }}
                        value={attributes?.hero_search__categories}
                        options={[
                            ...[
                            {
                                disabled: true,
                                label: 'Select an Option',
                                value: ''
                            },
                        ],
                            ...findPostTypeSelectOptions('filter_lists')
                    ]}
                    />
                    <TextControl
                        placeholder="Categories Placeholder"
                        value={attributes?.hero_search__categories_placeholder}
                        onChange={(value) => {
                            setAttributes({hero_search__categories_placeholder: value});
                        }}
                    />
                    <TextControl
                        placeholder="Search Placeholder"
                        value={attributes?.hero_search__search_placeholder}
                        onChange={(value) => {
                            setAttributes({hero_search__search_placeholder: value});
                        }}
                    />
                    <TextControl
                        placeholder="Location Placeholder"
                        value={attributes?.hero_search__location_placeholder}
                        onChange={(value) => {
                            setAttributes({hero_search__location_placeholder: value});
                        }}
                    />
                    <TextControl
                        placeholder="Search Button Label"
                        value={attributes?.hero_search__search_button_label}
                        onChange={(value) => {
                            setAttributes({hero_search__search_button_label: value});
                        }}
                    />
                    <TextControl
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
                            ...findPostTypeSelectOptions('filter_lists')
                        ]}
                    />
                </div>
            )}
        </div>
    );
};

export default SearchTab;
