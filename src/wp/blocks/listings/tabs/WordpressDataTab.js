import React from 'react';
import {TabPanel, Panel, PanelBody, PanelRow, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const WordpressDataTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        config
    } = props;
    console.log({config})

    function findSingleItemPosts() {
        return tru_fetcher_react.post_types.find(postType => postType?.name === 'fetcher_single_item');
    }
    function findListingsCategoryTerms() {
        return tru_fetcher_react.taxonomies.find(taxonomy => taxonomy?.name === 'listings_categories');
    }

    function getListingsCategoryTermsSelectOptions() {
        let listingsCategoryTerms = findListingsCategoryTerms();
        if (!listingsCategoryTerms) {
            return [];
        }
        return listingsCategoryTerms.terms.map(term => {
            return {
                label: term.name,
                value: term.term_id
            }
        })
    }
    function findSingleItemPostsSelectOptions() {
        let singleItemPosts = findSingleItemPosts();
        if (!singleItemPosts) {
            return [];
        }
        return singleItemPosts.posts.map(post => {
            return {
                label: post.post_title,
                value: post.ID
            }
        })
    }

    return (
        <PanelRow>
            <SelectControl
                label="Listings Category"
                onChange={(value) => {
                    setAttributes({listings_category: value});
                }}
                value={attributes?.listings_category}
                options={[
                    ...[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                    ],
                    ...getListingsCategoryTermsSelectOptions()
                ]}
            />
            <SelectControl
                label="Item List"
                onChange={(value) => {
                    setAttributes({item_list: value});
                }}
                value={attributes?.item_list}
                options={[
                    ...[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                ],
                    ...findSingleItemPostsSelectOptions()
            ]}
            />
        </PanelRow>
    );
};

export default WordpressDataTab;
