import React from 'react';
import {ToggleControl, SelectControl, RangeControl} from "@wordpress/components";
import {
    findPostTypeIdIdentifier,
    findPostTypeSelectOptions, findTaxonomyIdIdentifier, findTaxonomySelectOptions, getTermsSelectData,
} from "../../../helpers/wp-helpers";
import Grid from "../../components/wp/Grid";
import {useSelect} from "@wordpress/data";

const WordpressDataTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        config
    } = props;

    const {categoriesSelect} = useSelect(
        (select) => {
            return {
                categoriesSelect: getTermsSelectData({select, taxonomy: 'category'})
            }
        }
    );

    const itemListId = findPostTypeIdIdentifier('trf_item_list');
    const categoryId = findTaxonomyIdIdentifier('category');
    return (
        <div>
            <Grid columns={2}>
                <SelectControl
                    label="WordPress Data Source"
                    onChange={(value) => {
                        setAttributes({wordpress_data_source: value});
                    }}
                    value={attributes?.wordpress_data_source}
                    options={[
                        {
                            disabled: false,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            disabled: false,
                            label: 'Item List',
                            value: 'item_list'
                        },
                        {
                            disabled: false,
                            label: 'Posts',
                            value: 'posts'
                        },
                    ]}
                />
            </Grid>
            {attributes?.wordpress_data_source === 'posts' &&
                <>
                    <Grid columns={2}>
                        <ToggleControl
                            label="Show all categories?"
                            checked={attributes?.show_all_categories}
                            onChange={(value) => {
                                setAttributes({show_all_categories: value});
                            }}
                        />
                        {!attributes?.show_all_categories &&
                            <SelectControl
                                label="Post Categories To Display"
                                onChange={(value) => {
                                    setAttributes({[categoryId]: value});
                                }}
                                multiple={true}
                                value={attributes?.[categoryId]}
                                options={[
                                    ...[
                                        {
                                            disabled: true,
                                            label: 'Select an Option',
                                            value: ''
                                        },
                                    ],
                                    ...categoriesSelect
                                ]}
                            />
                        }
                    </Grid>
                </>
            }
            {attributes?.wordpress_data_source === 'item_list' &&
                <Grid columns={1}>
                    <SelectControl
                        label="Item List"
                        onChange={(value) => {
                            setAttributes({[itemListId]: value});
                        }}
                        value={attributes?.[itemListId]}
                        options={[
                            ...[
                                {
                                    disabled: true,
                                    label: 'Select an Option',
                                    value: ''
                                },
                            ],
                            ...findPostTypeSelectOptions('trf_item_list')
                        ]}
                    />
                </Grid>
            }
        </div>
    );
};

export default WordpressDataTab;
