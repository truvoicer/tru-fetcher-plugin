import {isNotEmpty, isObject, isObjectEmpty} from "../../../library/helpers/utils-helpers";

export function isFormikPropsValid(formikProps) {
    return (isNotEmpty(formikProps) && isObject(formikProps) && !isObjectEmpty(formikProps));
}