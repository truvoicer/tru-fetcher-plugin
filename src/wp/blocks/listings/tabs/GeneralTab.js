import React from 'react';
import {SelectControl, TextControl} from "@wordpress/components";
import {findTaxonomyIdIdentifier, findTaxonomySelectOptions} from "../../../helpers/wp-helpers";
import {useSelect} from '@wordpress/data';
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import {GutenbergBlockIdHelpers} from "../../../helpers/gutenberg/gutenberg-block-id-helpers";


const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    const {getBlocks} = useSelect(
        (select) => {
            return select('core/block-editor');
        }
    );

    let listingBlockId = attributes?.listing_block_id;
    if (!isNotEmpty(listingBlockId)) {
        const blockIdHelpers = new GutenbergBlockIdHelpers({
            blockName: props?.name,
            blocks: getBlocks(),
            defaultListingBlockIdPrefix: 'listing_block'
        });
        setAttributes({listing_block_id: blockIdHelpers?.buildBlockId(attributes?.listing_block_id)})
    }

    const listingsCategoryId = findTaxonomyIdIdentifier('trf_listings_category')
    return (
        <div>
            <TextControl
                label="Listings Block Id"
                placeholder="Listings Block Id"
                value={attributes?.listing_block_id}
                onChange={(value) => {
                    setAttributes({listing_block_id: value})
                }}
            />
            <SelectControl
                label="Listing Data Source"
                onChange={(value) => {
                    setAttributes({source: value});
                }}
                value={attributes?.source}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Api',
                        value: 'api'
                    },
                    {
                        label: 'Wordpress',
                        value: 'wordpress'
                    },
                ]}
            />
            <SelectControl
                label="Listings Category"
                onChange={(value) => {
                    setAttributes({[listingsCategoryId]: value});
                }}
                value={attributes?.[listingsCategoryId]}
                options={[
                    ...[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                    ],
                    ...findTaxonomySelectOptions('trf_listings_category')
                ]}
            />
        </div>
    );
};

export default GeneralTab;
