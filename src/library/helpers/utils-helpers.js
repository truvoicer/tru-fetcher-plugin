/**
 * Checks if variable is set
 * @param item
 * @returns {boolean}
 */
export function isSet(item) {
    return typeof item !== "undefined";
}

/**
 * Checks if variable is empty
 * @param item
 * @returns {boolean}
 */
export function isNotEmpty(item) {
    return typeof item !== "undefined" && item !== null && item !== "" && item !== false;
}

export function uCaseFirst(string)  {
    if (!isNotEmpty(string)) {
        return ""
    }
    if (!isNaN(string)) {
        return string
    }
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export function isObjectEmpty(object)  {
    if (!isNotEmpty(object) || !isObject(object)) {
        return false;
    }
    return Object.keys(object).length === 0 && object.constructor === Object
}

export function isObject(object)  {
    return typeof object === "object" && object !== null;
}

export function objectCount(object)  {
    if (!isObject(object)) {
        return false;
    }
    if (isObjectEmpty(object)) {
        return 0;
    }
    return Object.keys(object).length
}

export function scrollToRef(ref)  {
    window.scrollTo(0, ref.current.offsetTop)
}

export function getAcceptedMimeTypesString(allowedExtArray = null)  {
    if (allowedExtArray === null) {
        return '';
    }
    return allowedExtArray.map(type => type.mime_type).join(", ");
}
export function getAcceptedFileExtString(allowedExtArray = null, allowedMessage)  {
    if (allowedExtArray === null) {
        return '';
    }
    const joinAcceptedFiles = allowedExtArray.map(type => type.extension).join(", ");
    return allowedMessage.replace("[accepted]", joinAcceptedFiles)
}

export function range(start, stop, step)  {
    return Array.from({length: (stop - start) / step + 1}, (_, i) => start + (i * step))
}

export function findObjectInArray(items, key, value, subItemsKey = false) {
    for (let i = 0; i < items.length; i++) {
        if (typeof items[i][key] === 'undefined') {
            continue;
        }
        if (items[i][key] === value) {
            return items[i];
        }
        if (!subItemsKey || typeof items[i][subItemsKey] === "undefined") {
            continue;
        }
        let sub = findObjectInArray(items[i][subItemsKey], key, subItemsKey);
        if (typeof sub === "object") {
            return sub;
        }
    }
}
