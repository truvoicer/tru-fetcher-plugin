import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
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
                <ToggleControl
                    label="Show Listings Sidebar"
                    checked={attributes?.show_listings_sidebar}
                    onChange={(value) => {
                        setAttributes({show_listings_sidebar: value});
                    }}
                />
                <TextControl
                    label="Heading"
                    value={attributes?.heading}
                    onChange={(value) => setAttributes({heading: value})}
                />
            </Grid>
            <Grid columns={2}>
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
            </Grid>
        </div>
    );
};

export default DisplayTab;
