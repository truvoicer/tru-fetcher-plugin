import {fetchRequest} from "../../middleware";

export async function fetchAllOptionGroupsRequest() {
    const results = await fetchRequest({
        endpoint: 'option-group/list'
    });

    const optionGroupsRes = results?.data?.optionGroups;
    if (!Array.isArray(optionGroupsRes)) {
        console.error('Option Groups invalid response')
        return false;
    }
    return optionGroupsRes;
}

export function getOptionGroupByName(name, optionGroups) {
    const findOptionGroup = optionGroups.find(optionsGroupItem => optionsGroupItem?.name === name);
    if (!findOptionGroup) {
        return false;
    }
    return findOptionGroup;
}
export function getOptionGroupItemsByName(name, optionGroups) {
    const optionGroup = getOptionGroupByName(name, optionGroups);
    if (!optionGroup) {
        return false;
    }
    const items = optionGroup?.optionGroupItems;
    if (!Array.isArray(items)) {
        return false;
    }
    return items;
}
export function getOptionGroupSelectItems(name, optionGroups) {
    const items = getOptionGroupItemsByName(name, optionGroups);
    if (!items) {
        return false;
    }
    return items.map(item => {
        return {
            value: item?.option_value,
            label: item?.option_text
        }
    });
}
export function getOptionGroupSelectData(name, optionGroups) {
    const optionGroup = getOptionGroupByName(name, optionGroups);
    const items = getOptionGroupSelectItems(name, optionGroups);
    if (!items) {
        return false;
    }
    if (!optionGroup) {
        return false;
    }
    return {
        defaultValue: optionGroup?.default_value || '',
        options: items
    };
}
