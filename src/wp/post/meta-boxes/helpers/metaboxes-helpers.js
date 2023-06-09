
export function updateInitialValues({fieldGroupId, metaBoxContext, setIsInitialized}) {
    const findMetaFieldConfig = tru_fetcher_react.meta.metaFields.find(item => item?.id === fieldGroupId);
    if (!findMetaFieldConfig) {
        return;
    }
    console.log({findMetaFieldConfig, metaBoxContext})

    let initialValues = {...metaBoxContext.data};
    Object.keys(metaBoxContext.data).forEach(key => {
        const findField = findMetaFieldConfig.fields.find(field => field.id === key);
        if (!findField) {
            return;
        }
        const fieldInput = document.querySelector(`input[name="${findField.field_name}"]`);
        if (!fieldInput) {
            return;
        }
        const fieldValue = fieldInput.value;
        switch(findField.type) {
            case 'array':
                if (typeof fieldValue !== 'string' || fieldValue.length === 0) {
                    return;
                }
                initialValues[key] = JSON.parse(fieldValue);
                break;
            default:
                initialValues[key] = fieldValue;
                break;
        }
    })
    metaBoxContext.updateByKey('data', initialValues);
    setIsInitialized(true);
}

export function updateMetaHiddenFields({fieldGroupId, field, metaBoxContext}) {
    const findMetaFieldConfig = tru_fetcher_react.meta.metaFields.find(item => item?.id === fieldGroupId);
    if (!findMetaFieldConfig) {
        return;
    }
    const findField = findMetaFieldConfig.fields.find(fieldItem => fieldItem.id === field);

    if (!findField) {
        return;
    }
    const fieldName = findField.field_name;

    const hiddenField = document.querySelector(`input[name="${fieldName}"]`);
    if (!hiddenField) {
        return;
    }
    const data = metaBoxContext.data[field];
    if (typeof data === 'object' || Array.isArray(data)) {
        hiddenField.value = JSON.stringify(data);
    } else {
        hiddenField.value = data;
    }
}
