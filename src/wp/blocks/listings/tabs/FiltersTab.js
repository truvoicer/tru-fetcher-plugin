import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Filters from "../../components/filters/Filters";

const FiltersTab = (props) => {
    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <div>
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
        </div>
    );
};

export default FiltersTab;
