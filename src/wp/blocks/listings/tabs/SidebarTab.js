import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Filters from "../../components/filters/Filters";
import Sidebar from "../../common/Sidebar";

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
            <ToggleControl
                label="Show Sidebar widgets in listings sidebar"
                checked={attributes?.show_sidebar_widgets_in_listings_sidebar}
                onChange={(value) => {
                    setAttributes({show_sidebar_widgets_in_listings_sidebar: value});
                }}
            />
            <ToggleControl
                label="Show Filters"
                checked={attributes?.show_filters_toggle}
                onChange={(value) => {
                    setAttributes({show_filters_toggle: value});
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
            {attributes?.show_filters_toggle &&
                <>
                    <TextControl
                        label="Filter Heading"
                        placeholder="Filter Heading"
                        value={attributes.filter_heading}
                        onChange={(value) => {
                            setAttributes({filter_heading: value});
                        }}
                    />
                    <Filters data={attributes.filters} onChange={formChangeHandler}/>
                </>
            }
        </>
    );
};

export default SidebarTab;
