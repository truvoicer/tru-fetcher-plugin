
export function updateInitialValues({fieldGroupId, metaBoxContext, setIsInitialized}) {
    const findMetaFieldConfig = tru_fetcher_react.meta.metaBoxes.find(item => item?.id === fieldGroupId);
    if (!findMetaFieldConfig) {
        return;
    }

    let initialValues = {...metaBoxContext.formData};
    Object.keys(metaBoxContext.formData).forEach(key => {
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
    metaBoxContext.updateByKey('formData', initialValues);
    setIsInitialized(true);
}

export function updateMetaHiddenFields({fieldGroupId, field, metaBoxContext}) {
    const findMetaFieldConfig = tru_fetcher_react.meta.metaBoxes.find(item => item?.id === fieldGroupId);
    if (!findMetaFieldConfig) {
        console.warn('No meta field config found');
        return;
    }
    const findField = findMetaFieldConfig.fields.find(fieldItem => fieldItem.id === field);

    if (!findField) {
        console.warn('No field found');
        return;
    }
    const fieldName = findField.field_name;

    const hiddenField = document.querySelector(`input[name="${fieldName}"]`);
    if (!hiddenField) {
        console.warn('No hidden field found');
        return;
    }
    const data = metaBoxContext.formData[field];
    if (typeof data === 'object' || Array.isArray(data)) {
        hiddenField.value = JSON.stringify(data);
    } else {
        hiddenField.value = data;
    }
}

export function findMetaBoxConfig(id) {
    if (!Array.isArray(tru_fetcher_react.meta.metaBoxes)) {
        return null;
    }
    return tru_fetcher_react.meta.metaBoxes.find(item => item?.id === id);
}
export function findMetaBoxPostType(name, config) {
    if (!Array.isArray(config.post_types)) {
        return null;
    }
    return config.post_types.find(item => item?.name === name);
}
