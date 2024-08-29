import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Grid from "../../../../../components/Grid";

const GeneralTab = (props) => {

    const {
        data,
        onChange
    } = props;

    return (
        <Grid columns={3}>
            <SelectControl
                label="Fetch type"
                onChange={(value) => {
                    onChange({key: 'fetch_type', value: value});
                }}
                value={data?.view}
                options={[
                    {
                        label: 'Database',
                        value: 'database'
                    },
                    {
                        label: 'Api',
                        value: 'api'
                    },
                ]}
            />
            <SelectControl
                label="Order by"
                onChange={(value) => {
                    onChange({key: 'order_by', value: value});
                }}
                value={data?.view}
                options={[
                    {
                        label: 'ID',
                        value: 'id'
                    },
                    {
                        label: 'Title',
                        value: 'title'
                    },
                ]}
            />
            <SelectControl
                label="Order direction"
                onChange={(value) => {
                    onChange({key: 'order_direction', value: value});
                }}
                value={data?.view}
                options={[
                    {
                        label: 'Ascending',
                        value: 'ascending'
                    },
                    {
                        label: 'Descending',
                        value: 'descending'
                    },
                ]}
            />
        </Grid>
    );
};

export default GeneralTab;
