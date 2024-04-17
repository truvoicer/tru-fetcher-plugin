import React from 'react';
import {TextControl, SelectControl, ToggleControl, RangeControl} from "@wordpress/components";
import Grid from "../../components/wp/Grid";

const DisplayTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <div>
            <Grid columns={2}>
                <TextControl
                    label="Heading"
                    value={attributes?.heading}
                    onChange={(value) => setAttributes({heading: value})}
                />
                <SelectControl
                    label="Display As"
                    onChange={(value) => {
                        setAttributes({display_as: value});
                    }}
                    value={attributes?.display_as}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'List',
                            value: 'list'
                        },
                        {
                            label: 'Posts List',
                            value: 'post_list'
                        },
                        {
                            label: 'Comparisons',
                            value: 'comparisons'
                        },
                        {
                            label: 'Tiles',
                            value: 'tiles'
                        },
                    ]}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Grid Layout"
                    onChange={(value) => {
                        setAttributes({grid_layout: value});
                    }}
                    value={attributes?.grid_layout}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'List',
                            value: 'list'
                        },
                        {
                            label: 'Compact',
                            value: 'compact'
                        },
                        {
                            label: 'Detailed',
                            value: 'detailed'
                        },
                        {
                            label: 'Sidebar',
                            value: 'sidebar'
                        },
                    ]}
                />
                <SelectControl
                    label="Select Item View Display"
                    onChange={(value) => {
                        setAttributes({item_view_display: value});
                    }}
                    value={attributes?.item_view_display}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'Modal',
                            value: 'modal'
                        },
                        {
                            label: 'Page',
                            value: 'page'
                        },
                    ]}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Load More Type"
                    onChange={(value) => {
                        setAttributes({load_more_type: value});
                    }}
                    value={attributes?.load_more_type}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'Pagination',
                            value: 'pagination'
                        },
                        {
                            label: 'Infinite Scroll',
                            value: 'infinite_scroll'
                        },
                    ]}
                />
                <RangeControl
                    label="Posts Per Page"
                    initialPosition={50}
                    max={100}
                    min={0}
                    value={attributes?.posts_per_page}
                    onChange={(value) => setAttributes({posts_per_page: value})}
                />
            </Grid>
        </div>
    );
};

export default DisplayTab;
