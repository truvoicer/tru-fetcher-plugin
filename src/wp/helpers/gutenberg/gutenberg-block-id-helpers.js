import {isNotEmpty, toSnakeCase} from "../../../library/helpers/utils-helpers";
import {GutenbergBase} from "./gutenberg-base";

export class GutenbergBlockIdHelpers extends GutenbergBase {

    constructor({blockName = null, blocks = null, defaultListingBlockIdPrefix = null}) {
        super();
        this.setDefaultBlockIdPrefix(defaultListingBlockIdPrefix);
        this.setBlockName(blockName);
        this.setBlocks(blocks);
        this.validate();
    }

    setDefaultBlockIdPrefix(defaultPrefix) {
        this.defaultListingBlockIdPrefix = defaultPrefix;
    }
    setBlockName(blockName) {
        this.blockName = blockName;
    }
    setBlocks(blocks) {
        this.blocks = blocks;
    }

    getBlockIdPrefix() {
        const splitListingBlockId = this.blockName.split('/');
        if (splitListingBlockId.length === 2) {
            return toSnakeCase(splitListingBlockId[1]);
        }
        return this.defaultListingBlockIdPrefix;
    }

    getMaxListingBlockId() {
        const listingBlocks = this.blocks.filter(block => {
            if (block?.name !== this.blockName) {
                return false;
            }
            return (
                !isNotEmpty(block?.attributes?.listing_block_id) &&
                block.attributes.listing_block_id?.includes(this.getBlockIdPrefix())
            )
        })
            .map((block, index) => {
                const listingBlockId = block?.attributes?.listing_block_id;
                const splitListingBlockId = listingBlockId?.split('_');
                return parseInt(splitListingBlockId[splitListingBlockId.length - 1])
            })
        if (!listingBlocks.length) {
            return 0;
        }
        return Math.max(...listingBlocks)
    }

    findListingBlockId(index) {
        return this.blocks.find(block => block?.attributes?.listing_block_id === `${this.getBlockIdPrefix()}_${index}`);
    }

    generateId(index) {
        const listingBlock = this.findListingBlockId(index);
        if (!listingBlock) {
            return `${this.getBlockIdPrefix()}_${index}`;
        }
        const newIndex = index + 1;
        return this.generateId(newIndex)
    }

    validate() {
        if (!this.blockName) {
            throw new Error('Block Name is required');
        }
        if (!this.defaultListingBlockIdPrefix) {
            throw new Error('Default Listing Block Id Prefix is required');
        }
        if (!Array.isArray(this.blocks)) {
            throw new Error('Blocks must be an array');
        }
    }

    buildBlockId(blockId) {
        this.blocks = GutenbergBase.filterBlocksByBlockName(this.blockName, this.blocks)
        if (isNotEmpty(blockId)) {
            return blockId;
        }
        const maxListingBlockId = this.getMaxListingBlockId();
        return this.generateId(maxListingBlockId);
    }

    static buildBlockIdSelect(blockName, blocks) {
        if (!isNotEmpty(blockName)) {
            console.warn('Block Name is required');
            return [];
        }
        const filterBlocks = this.filterBlocksByBlockName(blockName, blocks);
        return filterBlocks.map(block => {
            return {
                label: block?.attributes?.listing_block_id,
                value: block?.attributes?.listing_block_id
            }
        });
    }
}
