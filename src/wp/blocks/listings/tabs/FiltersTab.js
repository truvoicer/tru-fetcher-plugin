import React from 'react';
import {TabPanel, Panel, RangeControl, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {findTaxonomySelectOptions} from "../../../helpers/wp-helpers";
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
            <TextControl
                label="Filter Heading"
                placeholder="Filter Heading"
                value={attributes.filter_heading}
                onChange={(value) => {
                    setAttributes({filter_heading: value});
                }}
            />
            <Filters data={attributes.filters} onChange={formChangeHandler} />
        </div>
    );
};

export default FiltersTab;
