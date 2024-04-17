import {GutenbergBlockHelpers} from "./gutenberg/gutenberg-block-helpers";

export function findPostTypeData(postType) {
    if (!Array.isArray(tru_fetcher_react?.post_types)) {
        return null;
    }
    return tru_fetcher_react.post_types.find(pt => pt?.name === postType);
}

export function findTaxonomyData(taxonomy) {
    if (!Array.isArray(tru_fetcher_react.taxonomies)) {
        return null;
    }
    return tru_fetcher_react.taxonomies.find(tax => tax?.name === taxonomy);
}

export function findPostTypeSelectOptions(postType) {
    const postTypes = findPostTypeData(postType)
    if (!Array.isArray(postTypes?.posts)) {
        return [];
    }
    return postTypes.posts.map(post => {
        return {
            label: post.post_title,
            value: post.ID
        }
    })
}

export function findTaxonomySelectOptions(taxonomy) {
    const taxonomies = findTaxonomyData(taxonomy)
    if (!taxonomies) {
        return [];
    }
    return taxonomies.terms.map(term => {
        return {
            label: term.name,
            value: term.term_id
        }
    })
}
export function findPostTypeIdIdentifier(postType) {
    const findPostType = findPostTypeData(postType)
    if (!findPostType) {
        return false;
    }
    if (findPostType?.id_identifier) {
        return findPostType.id_identifier;
    }
    return false;
}
export function findTaxonomyIdIdentifier(taxonomy) {
    const taxonomies = findTaxonomyData(taxonomy)
    if (!taxonomies) {
        return false;
    }
    if (taxonomies?.id_identifier) {
        return taxonomies.id_identifier;
    }
    return false;
}

export function addParam({attr, attributes, setAttributes}) {
    let cloneAtts = {...attributes};
    let cloneSearchParam = [];
    if (Array.isArray(cloneAtts[attr])) {
        cloneSearchParam = [...cloneAtts[attr]];
    }
    cloneSearchParam.push({name: '', value: ''});
    setAttributes({[attr]: cloneSearchParam});
}

export function updateParam({attr, index, key, value, attributes, setAttributes}) {
    let cloneAtts = {...attributes};
    let cloneSearchParam = [...cloneAtts[attr]];
    cloneSearchParam[index][key] = value;
    setAttributes({[attr]: cloneSearchParam});
}
export function deleteParam({attr, index, key, value, attributes, setAttributes}) {
    let cloneAtts = {...attributes};
    let cloneSearchParam = [...cloneAtts[attr]];
    cloneSearchParam.splice(index, 1);
    setAttributes({[attr]: cloneSearchParam});
}

export function getChildBlockIds(config) {
    if (!Array.isArray(config?.children)) {
        return []
    }
    return config?.children;
}
export function getChildBlockById(config, id) {
    if (!Array.isArray(config?.children)) {
        return false
    }
    return config.children.find(child => child?.id === id);
}
export function getChildBlockParams({blockEditorStore, select, clientId, callback = false}) {
    const selectBlockEditorStore = select( blockEditorStore );
    const { getBlockOrder, getBlockRootClientId, getBlockParents, getBlockAttributes } =
        selectBlockEditorStore;
    const parents = getBlockParents(clientId);
    const rootId = getBlockRootClientId( clientId );
    const columnsIds = getBlockOrder( clientId )
    // const childBlockAttributes = columnsIds.map((clientId) => {
    //     return getBlockAttributes(clientId);
    // })
    if (typeof callback === 'function') {
        return callback({selectBlockEditorStore, clientId, rootId, parents});
    }
    return {
        hasParents: parents.length > 0,
        hasChildBlocks: getBlockOrder( clientId ).length > 0,
        rootClientId: rootId,
        parentColumnsIds: getBlockOrder( rootId ),
        columnsIds,
        parentAttributes: getBlockAttributes(rootId),
        // childBlockAttributes
    };
}
export function getBlockAttributesById(blockId) {
    const findBlock = GutenbergBlockHelpers.findBlockById(blockId);
    if (!findBlock) {
        return false;
    }
    if (typeof findBlock.attributes !== 'undefined' && Array.isArray(findBlock.attributes)) {
        let attData = {};
        findBlock.attributes.map((attribute) => {
            attData[attribute.id] = attribute.default;
        });
        return attData;
    }
    return false;
}

export function getTermsSelectData({select, taxonomy}) {
    const core = select('core');
    const categories = core.getEntityRecords('taxonomy', taxonomy);
    if (!Array.isArray(categories)) {
        return [];
    }
    return categories.map(category => {
        return {
            label: category.name,
            value: category.id
        };
    });
}
