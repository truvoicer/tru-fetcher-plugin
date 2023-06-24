
export function findSingleItemListsPosts() {
    return tru_fetcher_react.post_types.find(postType => postType?.name === 'fetcher_items_lists');
}
export function findSingleItemPosts() {
    return tru_fetcher_react.post_types.find(postType => postType?.name === 'fetcher_single_item');
}
export function findListingsCategoryTerms() {
    return tru_fetcher_react.taxonomies.find(taxonomy => taxonomy?.name === 'listings_categories');
}
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
export function getListingsCategoryTermsSelectOptions() {
    let listingsCategoryTerms = findListingsCategoryTerms();
    if (!listingsCategoryTerms) {
        return [];
    }
    return listingsCategoryTerms.terms.map(term => {
        return {
            label: term.name,
            value: term.term_id
        }
    })
}
export function findSingleItemPostsSelectOptions() {
    let singleItemPosts = findSingleItemPosts();
    if (!singleItemPosts) {
        return [];
    }
    return singleItemPosts.posts.map(post => {
        return {
            label: post.post_title,
            value: post.ID
        }
    })
}
export function findSingleItemListsPostsSelectOptions() {
    let singleItemListsPosts = findSingleItemListsPosts();
    if (!singleItemListsPosts) {
        return [];
    }
    return singleItemListsPosts.posts.map(post => {
        return {
            label: post.post_title,
            value: post.ID
        }
    })
}


export function addParam({attr, attributes, setAttributes}) {
    let cloneAtts = {...attributes};
    let cloneSearchParam = [...cloneAtts[attr]];
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
