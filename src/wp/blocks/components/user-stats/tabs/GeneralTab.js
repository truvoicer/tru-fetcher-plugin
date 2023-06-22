import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";

const GeneralTab = (props) => {

    const {
        data,
        onChange
    } = props;

    return (
        <div>
            <ToggleControl
                label="Show Provider Stats?"
                checked={data?.show_provider_stats}
                onChange={(value) => {
                    onChange({key: 'show_provider_stats', value: value});
                }}
            />
            <ToggleControl
                label="Show Item Stats?"
                checked={data?.show_item_stats}
                onChange={(value) => {
                    onChange({key: 'show_item_stats', value: value});
                }}
            />
            <ToggleControl
                label="Show Saved Items Stats"
                checked={data?.show_saved_items_stats}
                onChange={(value) => {
                    onChange({key: 'show_saved_items_stats', value: value});
                }}
            />
            {data?.show_provider_stats && (
                <TextControl
                    placeholder="Provider Heading"
                    value={data?.provider_heading}
                    onChange={(value) => {
                        onChange({key: 'provider_heading', value: value});
                    }}
                />
            )}
            {data?.show_item_stats && (
                <TextControl
                    placeholder="Item Heading"
                    value={data?.item_heading}
                    onChange={(value) => {
                        onChange({key: 'item_heading', value: value});
                    }}
                />
            )}
            {data?.show_saved_items_stats && (
                <TextControl
                    placeholder="Saved Items Heading"
                    value={data?.saved_items_heading}
                    onChange={(value) => {
                        onChange({key: 'saved_items_heading', value: value});
                    }}
                />
            )}
        </div>
    );
};

export default GeneralTab;
