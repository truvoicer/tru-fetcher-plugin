import React from 'react';
import {Field} from "formik";
import {useFormikContext} from 'formik';
import {isNotEmpty, isSet} from "../../../../library/helpers/utils-helpers";
import SelectField from "../SelectField";
import FormListField from "../formlist/FormListField";

function FieldBuilder({
	formId, field, handleChange, handleBlur,
	checkboxOptions, radioOptions, arrayFieldIndex = false
}) {
	const {values, setFieldValue} = useFormikContext();

	const getFieldName = () => {
		if (arrayFieldIndex === false) {
			return field.name;
		}
		return `${formId}[${arrayFieldIndex}].${field.name}`;
	}

	const getFieldValue = (fieldName) => {
		if (arrayFieldIndex === false) {
			return values[fieldName];
		}
		return values[formId][arrayFieldIndex][fieldName];
	}


	const getListFieldValue = (name, value) => {
		return values[formId].map((item, itemIndex) => {
			if (itemIndex === arrayFieldIndex) {
				item[name] = value;
			}
			return item;
		})
	}

	const setFormFieldValue = (name, value, index) => {
		if (index === false) {
			setFieldValue(name, value);
			return;
		}
		setFieldValue(formId, getListFieldValue(name, value));
	}

	const getChoiceField = (name, label, value = null, type) => {
		let containerClass = "";
		let fieldProps = {
			type: type,
			name: name
		}
		if (type === "checkbox") {
			fieldProps.className = "form-check-input";
			containerClass = "form-check";
		} else if (type === "radio") {
			fieldProps.className = "form-radio-input";
			containerClass = "form-radio";
		}
		if (value !== null) {
			fieldProps.value = getFieldValue(field.name);
		}
		return (
			<div className={containerClass}>
				<label>
					<Field
						onChange={handleChange}
						{...fieldProps}
					/>
					{label}
				</label>
			</div>
		);
	}

	const getChoiceFieldList = (type, choiceFieldOptions, field) => {
		return (
			<>
				{choiceFieldOptions && choiceFieldOptions.map((item, index) => (
					<React.Fragment key={index}>
						{getChoiceField(getFieldName(), item.label, item.value, type)}
					</React.Fragment>
				))}
			</>
		)
	}

	const getTextField = (type) => {
		let extraProps = {};
		if (isNotEmpty(field?.min) && !isNaN(field.min)) {
			extraProps.min = field.min;
		}
		if (isNotEmpty(field?.max) && !isNaN(field.max)) {
			extraProps.max = field.max;
		}
		return (
			<input
				id={field.name}
				type={type}
				name={getFieldName()}
				className="form-control text-input"
				placeholder={field.placeHolder}
				onChange={handleChange}
				onBlur={handleBlur}
				defaultValue={getFieldValue(field.name)}
				{...extraProps}
			/>
		)
	}
	const getFileField = () => {
		let extraProps = {};
		return (
			<input
				id={field.name}
				type={'file'}
				name={getFieldName()}
				className="form-control text-input"
				placeholder={field.placeHolder}
				onChange={e => {
					setFormFieldValue(field.name, e.target.files[0], arrayFieldIndex)
				}}
				onBlur={handleBlur}
				defaultValue={getFieldValue(field.name)}
				{...extraProps}
			/>
		)
	}
	const getTextAreaField = () => {
		return (
			<textarea
				rows={field.rows ? field.rows : 4}
				id={field.name}
				name={getFieldName()}
				className="form-control text-input"
				placeholder={field.placeHolder}
				onChange={handleChange}
				onBlur={handleBlur}
				value={getFieldValue(field.name)}
			/>
		)
	}
	const getSelectField = () => {
		if (!isSet(field?.options)) {
			return <p>Select error...</p>
		}
		return (
			<SelectField
				name={field.name}
				options={field.options}
				value={getFieldValue(field.name)}
				onChangeHandler={e => {
					setFormFieldValue(field.name, e, arrayFieldIndex)
				}}
				extraProps={field?.props || {}}
			/>
		)
	}
	const getCheckboxField = () => {
		let options;
		if (isSet(checkboxOptions) && isSet(checkboxOptions[field.name])) {
			options = checkboxOptions[field.name];
		}
		if (isSet(field.checkboxType) && field.checkboxType === "true_false") {
			return getChoiceField(field.name, field.label, null, "checkbox");
		} else if (Array.isArray(options)) {
			return getChoiceFieldList("checkbox", options, field);
		}
		return null;
	}
	const getRadioField = () => {
		let options;
		if (isSet(radioOptions) && isSet(radioOptions[field.name])) {
			options = radioOptions[field.name];
		}
		if (Array.isArray(options)) {
			return getChoiceFieldList("radio", options, field);
		}
		return null;
	}

	const buildFormField = ({fieldType, name}) => {
		switch (fieldType) {
			case "file":
				return getFileField();
			case "text":
				return getTextField(field.type);
			case "textarea":
				return getTextAreaField();
			case "select":
			case "select_data_source":
				return getSelectField();
			case "checkbox":
				return getCheckboxField();
			case "radio":
				return getRadioField();
			case "form_list":
				return (
					<FormListField
						name={field.name}
						arrayFieldIndex={arrayFieldIndex}
						callback={setFormFieldValue}
						listItemKeyLabel={field?.listItemKeyLabel}
						listItemValueLabel={field?.listItemValueLabel}
						addRowLabel={field?.addRowLabel}
						data={getFieldValue(field.name)}
					/>
				);
			default:
				return null;
		}
	}

	return buildFormField(field);
}

export default FieldBuilder;
