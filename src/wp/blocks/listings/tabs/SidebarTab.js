import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Filters from "../../components/filters/Filters";
import Grid from "../../../../components/Grid";

const SidebarTab = (props) => {
        const {
            attributes,
            setAttributes,
            className,
        } = props;

        function formChangeHandler({key, value}) {
            setAttributes({
                ...attributes,
                [key]: value
            });
        }

        return (
            <>
                <Grid columns={2}>
                    <ToggleControl
                        label="Show Filters"
                        checked={attributes?.show_filters}
                        onChange={(value) => {
                            setAttributes({show_filters: value});
                        }}
                    />
                        <ToggleControl
                            label="Show Sidebar widgets in filters"
                            checked={attributes?.show_sidebar_widgets_in_filters}
                            onChange={(value) => {
                                setAttributes({show_sidebar_widgets_in_filters: value});
                            }}
                        />
                </Grid>
                    <>
                        <Grid columns={2}>
                            <ToggleControl
                                label="Show filters in sidebar"
                                checked={attributes?.show_filters_in_sidebar}
                                onChange={(value) => {
                                    setAttributes({show_filters_in_sidebar: value});
                                }}
                            />
                        </Grid>
                        <Grid columns={2}>
                            <TextControl
                                label="Filter Heading"
                                placeholder="Filter Heading"
                                value={attributes.filter_heading}
                                onChange={(value) => {
                                    setAttributes({filter_heading: value});
                                }}
                            />
                            <SelectControl
                                label="Filters Position"
                                onChange={(value) => {
                                    setAttributes({filters_position: value});
                                }}
                                value={attributes?.filters_position}
                                options={[
                                    {
                                        label: 'Left',
                                        value: 'left'
                                    },
                                    {
                                        label: 'Right',
                                        value: 'right'
                                    },
                                ]}
                            />
                        </Grid>
                        <Grid columns={1}>
                            <Filters data={attributes.filters} onChange={formChangeHandler}/>
                        </Grid>
                    </>
            </>
        );
    };

export default SidebarTab;
