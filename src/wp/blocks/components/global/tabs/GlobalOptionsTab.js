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
                        disabled: true,
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
        </Grid>
    );
};

export default GlobalOptionsTab;
