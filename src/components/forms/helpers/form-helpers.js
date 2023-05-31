import {isObject} from "../../../library/helpers/utils-helpers";

export function setFormConfigItemField(fields, name, key, value) {
    const cloneFields = JSON.parse(JSON.stringify(fields));
    return cloneFields.map((field) => {
        if (field.name === name) {
            if (field.hasOwnProperty(key)) {
                field[key] = value;
            }
            return field;
        }
        if (field.subFields) {
            field.subFields = setFormConfigItemField(field.subFields, name, key, value);
        }
        return field;
    });
}

