import React from 'react';
import {useSelect} from '@wordpress/data';
import {SelectControl, ToggleControl} from "@wordpress/components";
import {GutenbergBlockIdHelpers} from "../../../../helpers/gutenberg/gutenberg-block-id-helpers";

const ListingsOptionsTab = (props) => {
    const {getBlocks} = useSelect(
        (select) => {
            return select('core/block-editor');
        }
    );

    const {
        attributes,
        setAttributes,
        className,
    } = props;
    return (
        <div>
            <ToggleControl
                label="Has Listing Relation?"
                checked={attributes?.listing_relation}
                onChange={(value) => {
                    setAttributes({listing_relation: value});
                }}
            />
            {attributes?.listing_relation && (
                <SelectControl
                    label="Select Listings Block"
                    onChange={(value) => {
                        setAttributes({listing_block_id: value});
                    }}
                    value={attributes?.listing_block_id}
                    options={[
                        {
                            disabled: true,
                            label: 'Select a listings block',
                            value: ''
                        },
                        ...GutenbergBlockIdHelpers.buildBlockIdSelect(
                            GutenbergBlockIdHelpers.findBlockById('listings_block')?.name,
                            getBlocks()
                        )
                    ]}
                />
            )}
        </div>
    );
};

export default ListingsOptionsTab;
