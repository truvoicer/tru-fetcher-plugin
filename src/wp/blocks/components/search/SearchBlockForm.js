import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {findPostTypeIdIdentifier, findPostTypeSelectOptions} from "../../../helpers/wp-helpers";
import Grid from "../../../../components/Grid";

const SearchBlockForm = (props) => {
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
                    checked={attributes?.search}
                    onChange={(value) => {
                        setAttributes({search: value});
                    }}
                />
                {attributes?.search && (
                    <>
                        <Grid columns={2}>
                            <TextControl
                                label="Categories Placeholder"
                                placeholder="Categories Placeholder"
                                value={attributes?.search__categories_placeholder}
                                onChange={(value) => {
                                    setAttributes({search__categories_placeholder: value});
                                }}
                            />
                            <TextControl
                                label="Search Placeholder"
                                placeholder="Search Placeholder"
                                value={attributes?.search__search_placeholder}
                                onChange={(value) => {
                                    setAttributes({search__search_placeholder: value});
                                }}
                            />
                        </Grid>
                        <Grid columns={2}>
                            <TextControl
                                label="Location Placeholder"
                                placeholder="Location Placeholder"
                                value={attributes?.search__location_placeholder}
                                onChange={(value) => {
                                    setAttributes({search__location_placeholder: value});
                                }}
                            />
                            <TextControl
                                label="Search Button Label"
                                placeholder="Search Button Label"
                                value={attributes?.search__search_button_label}
                                onChange={(value) => {
                                    setAttributes({search__search_button_label: value});
                                }}
                            />
                        </Grid>
                        <Grid columns={2}>
                            <TextControl
                                label="Featured Categories Label"
                                placeholder="Featured Categories Label"
                                value={attributes?.search__featured_categories_label}
                                onChange={(value) => {
                                    setAttributes({search__featured_categories_label: value});
                                }}
                            />
                        </Grid>
                        <Grid columns={2}>
                            <SelectControl
                                label="Categories"
                                onChange={(value) => {
                                    setAttributes({[`${filterListId}__search__categories`]: value});
                                }}
                                value={attributes?.[`${filterListId}__search__categories`]}
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
                            <SelectControl
                                label="Featured Categories"
                                onChange={(value) => {
                                    setAttributes({[`${filterListId}__search__featured_categories`]: value});
                                }}
                                value={attributes?.[`${filterListId}__search__featured_categories`]}
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
                        </Grid>
                    </>
                )
                }
            </div>
        )
            ;
    }
;

export default SearchBlockForm;
