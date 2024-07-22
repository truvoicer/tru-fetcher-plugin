import React from 'react';
import {SelectControl, TextControl, ToggleControl} from "@wordpress/components";
import {findTaxonomyIdIdentifier, findTaxonomySelectOptions} from "../../../helpers/wp-helpers";
import {useSelect, useDispatch} from '@wordpress/data';
import {useBlockProps, store as blockEditorStore} from '@wordpress/block-editor';
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";
import {GutenbergBlockIdHelpers} from "../../../helpers/gutenberg/gutenberg-block-id-helpers";
import Grid from "../../components/wp/Grid";


const GeneralTab = (props) => {
    const { updateBlockAttributes } = useDispatch( blockEditorStore );
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
    const blocks = getBlocks() || [];

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
    console.log({listingsCategoryId})
    return (
        <div>
            <Grid columns={2}>
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
                <TextControl
                    label="Listings Block Id"
                    placeholder="Listings Block Id"
                    value={attributes?.listing_block_id}
                    onChange={(value) => {
                        setAttributes({listing_block_id: value})
                    }}
                />
            </Grid>
            <Grid columns={2}>
                <ToggleControl
                    label="Make primary listing"
                    checked={attributes?.primary_listing}
                    onChange={(value) => {
                        blocks.forEach((block) => {
                            if (props.clientId === block.clientId) {
                                return;
                            }
                            updateBlockAttributes( block.clientId, {
                                primary_listing: false,
                            } );
                        });
                        setAttributes({primary_listing: value});
                    }}
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

            </Grid>
        </div>
    );
};

export default GeneralTab;
