import React from 'react';
import {SelectControl, TextControl} from "@wordpress/components";
import {findTaxonomyIdIdentifier, findTaxonomySelectOptions} from "../../../helpers/wp-helpers";
import {useSelect} from '@wordpress/data';
import {isNotEmpty} from "../../../../library/helpers/utils-helpers";


const GeneralTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    const {blocks} = useSelect(
        ( select ) => {
            const blockEditor = select( 'core/block-editor' );
            return {
                blocks: blockEditor.getBlocks()
            }
        }
    );

    function getBlockIdPrefix() {
        const splitListingBlockId = props?.name?.split('_');
        return splitListingBlockId[0]
    }

    function getMaxListingBlockId() {
        const listingBlocks = blocks.filter(block => block?.name === props?.name)
            .map((block, index) => {
                const listingBlockId = block?.attributes?.listing_block_id;
                const splitListingBlockId = listingBlockId?.split('_');
                return parseInt(splitListingBlockId[splitListingBlockId.length - 1])
            })
        const maxListingBlockId = Math.max(...listingBlocks)
        return maxListingBlockId
    }

    function findListingBlockId(index) {
        const listingBlocks = blocks.filter(block => block?.name === props?.name);
        return listingBlocks.find(block => block?.attributes?.listing_block_id === `${getBlockIdPrefix()}_${index}`);
    }
    function buildListingBlockId() {
        const blockId = attributes?.listing_block_id
        if (isNotEmpty(blockId)) {
            return blockId;
        }
        const splitName = props?.name?.split('/');
        let listingBlockId = splitName[splitName.length - 1];
        const maxListingBlockId = getMaxListingBlockId();


        console.log({listingBlocks})
        return ''
    }
    console.log({props})
    const listingsCategoryId = findTaxonomyIdIdentifier('trf_listings_category')
    return (
        <div>
            <TextControl
                label="Listings Block Id"
                placeholder="Listings Block Id"
                value={buildListingBlockId()}
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
