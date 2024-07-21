export function buildSelectOptions(data, labelKey = 'label', valueKey = 'id') {
    if (!Array.isArray(data)) {
        return [];
    }
    return data.map((category) => {
        return {
            label: category[labelKey],
            value: category[valueKey]
        }
    })
}
