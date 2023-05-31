import SelectField from "../forms/fields/SelectField";
import InputField from "../forms/fields/InputField";
import {FIELD_ELEMENT_CHECKBOX, FIELD_ELEMENT_SELECT, FIELD_ELEMENT_TEXT} from "./constants/datatable-constants";
import {Label} from "semantic-ui-react";
import React from "react";
import {STATE_CREATE, STATE_UPDATE} from "../../library/constants/constants";

export function getSelect({fieldData, column, formikProps, formChangeCallback, formModalObject}) {
	const options = column?.select?.options;
	if (!Array.isArray(options)) {
		console.error(`Invalid options for select field at: (${fieldData.name})`)
		return null;
	}
	console.log({options})
	return <SelectField
		defaultValue={column?.select?.defaultValue}
		placeholder={column?.select?.placeholder}
		value={fieldData.value ||column?.select?.value || ''}
		onChangeHandler={(e, b) => {
			let cloneFieldData = {...fieldData}
			cloneFieldData.value = b.value;
			formChangeCallback(cloneFieldData, column, formikProps, e)
		}}
		options={options}
		name={fieldData.name}
	/>
}

export function callOnClickCallback({value = null, fieldData, column, formikProps, event, formModalObject}) {
	column.onClick({value, fieldData, column, formikProps, event, formModalObject});
}

export function getCheckboxInput({
	fieldData,
	column,
	formikProps,
	formChangeCallback,
	formModalObject
}) {
	let selectProps = {};
	if (typeof column?.onClick === 'function') {
		selectProps.onClick = (event) => {
			callOnClickCallback({
				value: event?.target?.checked,
				fieldData, column, formikProps, event, formModalObject
			})
		};
	}
	return <InputField
		name={fieldData.name}
		type={FIELD_ELEMENT_CHECKBOX}
		value={'true'}
		onChangeHandler={(e) => formChangeCallback(fieldData, column, formikProps, e)}
		extraInputProps={{
			checked: fieldData.value
		}}
		{...selectProps}
	/>
}

export function getTextInput({
	fieldData,
	column,
	formikProps,
	formChangeCallback,
	formModalObject
}) {
	let selectProps = {};
	if (typeof column?.onClick === 'function') {
		selectProps.onClick = (event) => {
			callOnClickCallback({
				value: event?.target?.value,
				fieldData, column, formikProps, event, formModalObject
			})
		};
	}
	return <InputField
		name={fieldData.name}
		defaultValue={fieldData.value || ''}
		type={FIELD_ELEMENT_TEXT}
		onChangeHandler={(e) => formChangeCallback(fieldData, column, formikProps, e)}
		placeHolder={column.name}
		{...selectProps}
	/>
}

export function getFormLabel({fieldData, column, formikProps}) {
	return (
		<Label>{fieldData?.value || ''}</Label>
	)
}

