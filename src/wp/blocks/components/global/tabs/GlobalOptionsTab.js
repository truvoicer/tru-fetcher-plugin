import React from 'react';
import {SelectControl} from "@wordpress/components";
import Grid from "../../../components/wp/Grid";

const GlobalOptionsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        wpMediaFrame,
    } = props;

    return (
        <Grid columns={2}>
            <SelectControl
                label="Position when sidebar is enabled"
                onChange={(value) => {
                    setAttributes({sidebar_layout_position: value});
                }}
                value={attributes?.sidebar_layout_position}
                options={[
                    {
                        label: 'Default',
                        value: 'default'
                    },
                    {
                        label: 'Outside Sidebar Top',
                        value: 'outside_top'
                    },
                    {
                        label: 'Outside Sidebar Bottom',
                        value: 'outside_bottom'
                    },
                ]}
            />
            <SelectControl
                label="Block style"
                onChange={(value) => {
                    setAttributes({block_style: value});
                }}
                value={attributes?.block_style}
                options={[
                    {
                        label: 'Contained',
                        value: 'contained'
                    },
                    {
                        label: 'Full width',
                        value: 'full_width'
                    },
                ]}
            />
        </Grid>
    );
};

export default GlobalOptionsTab;
