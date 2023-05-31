import {isFormikPropsValid} from "../../../components/forms/helpers/formik-helpers";
import {isNotEmpty} from "../utils-helpers";


export function buildFormFieldsData({formikProps, formFields, fieldData, parent = null}) {
    let fields = [...formFields];
    if (!isFormikPropsValid(formikProps)) {
        return fields;
    }
    let formikValues = {...formikProps.values};
    let formikFieldListValues = formikValues.items[fieldData.index];
    if (isNotEmpty(parent)) {
        formikFieldListValues = formikValues.items[fieldData.index][parent];
    } else {
        formikFieldListValues = formikValues.items[fieldData.index];
    }
    if (!formikFieldListValues) {
        return fields;
    }
    Object.keys(formikFieldListValues).forEach(key => {
        const fieldIndex = fields.findIndex(field => field?.name === key);
        if (fieldIndex === -1) {
            return;
        }
        if (typeof formikFieldListValues[key] === 'undefined') {
            fields[fieldIndex].value = undefined;
            return;
        }
        switch (fields[fieldIndex].type) {
            case 'select':
                const selectOptions = fields[fieldIndex].options;
                let requestValueKey = formikFieldListValues[key];
                if (
                    typeof fields[fieldIndex]['requestValueKey'] !== 'undefined' &&
                    formikFieldListValues[key][fields[fieldIndex]['requestValueKey']] !== 'undefined'
                ) {
                    if (
                        typeof fields[fieldIndex]['props']['isMulti'] !== 'undefined' &&
                        fields[fieldIndex]['props']['isMulti'] === true &&
                        Array.isArray(formikFieldListValues[key])
                    ) {
                        fields[fieldIndex].value = formikFieldListValues[key].map(item => {
                            if (typeof item[fields[fieldIndex]['requestValueKey']] === 'undefined') {
                                return {value: 'Error', label: 'Error'};
                            }
                            const requestValueKey = item[fields[fieldIndex]['requestValueKey']];
                            const selectOptionIndex = selectOptions.findIndex(option => option.value === requestValueKey);
                            if (selectOptionIndex === -1) {
                                return {value: 'Error', label: 'Error'}
                            }
                            return selectOptions[selectOptionIndex];
                        });
                    }
                    break;
                }
                const selectOptionIndex = selectOptions.findIndex(option => option.value === requestValueKey);
                if (selectOptionIndex > -1) {
                    fields[fieldIndex].value = selectOptions[selectOptionIndex];
                }
                break;
            default:
                fields[fieldIndex].value = formikFieldListValues[key];
                break;
        }
    })
    return fields;
}


export function setDatatableFormValues({formikProps, values, fieldData, parent = null}) {
    if (isNotEmpty(parent)) {
        formikProps.setFieldValue(`items.${fieldData.index}.${parent}`, values);
        return;
    }
    Object.keys(values).forEach(key => {
        formikProps.setFieldValue(`items.${fieldData.index}.${key}`, values[key])
    })
}
