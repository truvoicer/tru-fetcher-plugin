import React, {useEffect, useState} from 'react';
import {FieldArray, Formik, isObject} from "formik";
import {isSet, isNotEmpty} from "../../library/helpers/utils-helpers";
import FieldBuilder from "./fields/builders/FieldBuilder";
import LabelBuilder from "./fields/builders/LabelBuilder";
import {Button, Grid, Modal} from "semantic-ui-react";
import MessageBox from "./MessageBox";

const sprintf = require("sprintf-js").sprintf;
const FormBuilder = ({
	children,
	fields = [],
	formId = 'fields',
	submitCallback,
	formType,
	addListItemButtonText,
	submitButtonText,
	classes,
    showSubmitButton = false,
	setShowModal,
	modal = false,
	modalOpen = false,
	modalTitle = '',
	showMessageBox = false,
	messageBoxMessage = '',
}) => {
	const getInitialDataObject = () => {
		let initialValues = {};
		fields.map((item) => {
			const value = getInitialValue(item);
			if (value !== null) {
				initialValues[item.name] = value;
			}
			if (isSet(item.subFields)) {
				item.subFields.map((subItem) => {
					const subValue = getInitialValue(subItem);
					if (subValue !== null) {
						initialValues[subItem.name] = subValue;
					}
				})
			}
		})
		return initialValues;
	}

	const getInitialValue = (item) => {
		let value;
		switch (item.fieldType) {
			case "form_list":
				value = Array.isArray(item?.value) ? item.value : [];
				break;
			case "text":
			case "textarea":
			case "select":
			case "select_data_source":
			case "radio":
				value = item?.value || null;
				break;
			case "checkbox":
				if (isSet(item.checkboxType) && item.checkboxType === "true_false") {
					value = !!(isSet(item.value) && item.value);
				} else {
					value = isSet(item.value) ? item.value : {};
				}
				break;
		}
		return value;
	}

	const validationRules = (rule, values, key) => {
		switch (rule.type) {
			case "required":
				const field = getFieldByName(key);
				if (!values[key]) {
					return 'Required';
				}
				break;
			case "email_alphanumeric":
				if (!/^[\w_@.-]+$/.test(values[key])) {
					return 'Can only contain letters, numbers and the following characters (@) (_) (-) (.)';
				}
				break;
			case "email":
				if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(values[key])) {
					return 'Invalid email address';
				}
				break;
			case "alphanumeric":
				if (!/^[\w_\- ]+$/.test(values[key])) {
					return 'Can only contain letters and numbers';
				}
				break;
			case "alphanumeric_symbols":
				if (!/^[\w_\-#,.() ]+$/.test(values[key])) {
					return 'Can only contain symbols, letters and numbers';
				}
				break;
			case "numeric":
				if (!/^[\d]+$/.test(values[key])) {
					return 'Can only contain numbers';
				}
				break;
			case "length":
				if (values[key].length < parseInt(rule.min)) {
					return sprintf('Must be more than %d characters', rule.min);
				} else if (values[key].length > parseInt(rule.max)) {
					return sprintf('Must be less than %d characters', rule.max)
				}
				break;
			case "password":
				let conditions = "";
				rule.allowedChars?.map((char) => {
					if (char === "alphanumeric") {
						conditions += "[A-Z0-9.-]";
					}
					if (char === "symbols") {
						conditions += "[*.! @#$%^&(){}[]:;<>,.?/~_+-=|\\]";
					}
				})
				const regEx = new RegExp(sprintf("!/^%s$/i", conditions));
				if (regEx.test(values[key])) {
					return sprintf("Can only contain (%s)", rule.allowedChars.join(", "));
				}
				break;
			case "match":
				if (values[key] !== values[rule.matchField]) {
					return sprintf('Does not match with %s', getFieldByName(rule.matchField).label);
				}
				break;
		}
		return true;
	}

	const getFieldByName = (name) => {
		let fieldObject = {};
		fields.forEach(field => {
			if (isSet(field.subFields)) {
				field.subFields.forEach((subField) => {
					if (subField.name === name) {
						fieldObject = subField
					}
				})
			}
			if (field.name === name) {
				fieldObject = field
			}
		})
		return fieldObject
	}
	const getIgnoredFields = (values) => {
		let ignoredFields = [];
		Object.keys(values).forEach((key) => {
			const field = getFieldByName(key);
			field.subFields?.map((subField) => {
				if ((field.fieldType === "checkbox" && !values[field.name]) ||
					(field.fieldType === "checkbox" && values[field.name] === "")) {
					ignoredFields.push(subField.name);
				}
			})
		});
		return ignoredFields;
	}
	const validateForm = (values) => {
		const errors = {};

		const ignoredFields = getIgnoredFields(values);
		Object.keys(values).forEach((key) => {
			const field = getFieldByName(key);
			if (!ignoredFields.includes(field.name)) {
				const isAllowEmpty = field.validation?.rules?.filter(rule => rule.type === "allow_empty");
				if (!isSet(isAllowEmpty) ||
					(Array.isArray(isAllowEmpty) && isAllowEmpty.length > 0 && values[field.name] !== "") ||
					(Array.isArray(isAllowEmpty) && isAllowEmpty.length === 0)
				) {
					field.validation?.rules?.map((rule) => {
						const validate = validationRules(rule, values, key);
						if (validate !== true) {
							errors[key] = validate
						}
					})
				}
			}
		})
		return errors;
	};

	const formSubmitHandler = (values) => {
		let cloneValues = {...values};
		// console.log(cloneValues)
		// const ignoredFields = getIgnoredFields(cloneValues);
		// Object.keys(cloneValues).map((key) => {
		// 	const field = getFieldByName(key);
		// 	if (field.fieldType === "checkbox" && cloneValues[field.name] === "") {
		// 		cloneValues[field.name] = false;
		// 	}
		// 	if (ignoredFields.includes(key)) {
		// 		cloneValues[key] = "";
		// 	}
		// });
		submitCallback(cloneValues);
	}

	const dependsOnCheck = (field, values, isSubField = false) => {
		let show = false;
		if (isSet(field.dependsOn)) {
			if (isObject(values[field.dependsOn.field]) && field.dependsOn.value === values[field.dependsOn.field].value) {
				show = true;
			} else if (field.dependsOn.value === values[field.dependsOn.field]) {
				show = true;
			}
		} else if (!isSubField && !isSet(field.dependsOn)) {
			show = true;
		}
		return show;
	}
	const getFieldItemLabelPair = (field, errors, touched, handleBlur, handleChange, values, arrayFieldIndex) => {
		let formFieldLabel;
		const formFieldItem = (
			<FieldBuilder
				formId={formId}
				field={field}
				arrayFieldIndex={arrayFieldIndex}
				handleChange={handleChange}
				handleBlur={handleBlur}
			/>
		);
		switch (field?.labelPosition) {
			case "right":
				formFieldLabel = (<LabelBuilder field={field} errors={errors}/>)
				return (
					<>
						<Grid.Column width={12}>
							{formFieldItem}
							{isNotEmpty(field?.description) &&
								<p className={"field-description"}>
									{field.description}
								</p>
							}
						</Grid.Column>
						<Grid.Column width={4}>
							{formFieldLabel}
						</Grid.Column>
					</>
				)

			case "top":
				formFieldLabel = (<LabelBuilder field={field} errors={errors}/>)
				return (
					<Grid.Column width={16}>
						{formFieldLabel}
						{formFieldItem}
					</Grid.Column>
				)
			case "left":
			default:
				formFieldLabel = (<LabelBuilder field={field} errors={errors} showError={false}/>)
				return (
					<>
						<Grid.Column width={4}>
							{formFieldLabel}
						</Grid.Column>
						<Grid.Column width={12}>
							{formFieldItem}
							{isNotEmpty(field?.description) &&
								<p className={"field-description"}>
									{field.description}
								</p>
							}
							<br/>
							<span className={"tr-news-app--red site-form--error--field"}>
								{errors[field.name]}
							</span>
						</Grid.Column>
					</>
				)
		}
	}

	const getFieldRow = (field, errors, touched, handleBlur, handleChange, values, arrayFieldIndex = false, isSubField = false) => {
		const show = dependsOnCheck(field, values, isSubField);
		return (
			<>
				{show &&
					<div className={"select-wrapper"}>
						<Grid>
							{getFieldItemLabelPair(field, errors, touched, handleBlur, handleChange, values, arrayFieldIndex)}
						</Grid>
						{field.subFields && typeof values[field.name] !== 'undefined' &&
							<div className={"form-subfields"}>
								{field.subFields.map((subField, subFieldIndex) => {
									return (
										<React.Fragment key={subFieldIndex}>
											{getFieldRow(subField, errors, touched, handleBlur, handleChange, values, false, true)}
										</React.Fragment>
									)
								})}
							</div>
						}
					</div>
				}
			</>
		)
	}

	const getFields = (fields) => {
		let buildFields = [];
		fields.map((field, index) => {
			let rowIndex = field?.rowIndex;
			let columnIndex = field?.columnIndex;
			if (!isSet(rowIndex)) {
				rowIndex = index;
			}
			if (!isSet(columnIndex)) {
				columnIndex = 0;
			}
			if (!isSet(buildFields[rowIndex])) {
				buildFields[rowIndex] = [];
			}
			buildFields[rowIndex][columnIndex] = field
		})
		return buildFields;
	}

	const getGridColumnClasses = (row) => {
		const columnSize = Math.round(16 / row.length);
		return columnSize;
	}

	const buildFormRows = (fieldData, errors, touched, handleBlur, handleChange, values, arrayFieldIndex = false) => {
		return (
			<Grid>
				<Grid.Row>
					{getFields(fieldData).map((row, rowIndex) => (
						<React.Fragment key={rowIndex}>
							{row.map((field, index) => {
								return (
									<Grid.Column width={getGridColumnClasses(row)} key={index}>
										{getFieldRow(field, errors, touched, handleBlur, handleChange, values, arrayFieldIndex)}
									</Grid.Column>
								)
							})}
						</React.Fragment>
					))}
				</Grid.Row>
			</Grid>
		)
	}

	function getFormData({errors, touched, handleBlur, handleChange, values}) {
		return (
			<>
				<MessageBox message={messageBoxMessage} show={showMessageBox} />
				{formType === "single"
					?
					buildFormRows(fields, errors, touched, handleBlur, handleChange, values)
					:
					<FieldArray
						name={formId}
						render={arrayHelpers => {
							return (
								<div>
									{Array.isArray(values[formId]) && values[formId].map((item, index) => {
										return (
											<React.Fragment key={index}>
												{buildFormRows(fields, errors, touched, handleBlur, handleChange, values, index)}
											</React.Fragment>
										)
									})}
									<button
										type="button"
										onClick={() => arrayHelpers.push(initialValues.dataObject)}
									>
										{addListItemButtonText}
									</button>
								</div>
							)
						}}
					/>
				}
				{showSubmitButton &&
				<div className="row form-group">
					<div className="col-md-12">
						<Button type="submit">{submitButtonText}</Button>
					</div>
				</div>
				}

				{children}

			</>
		)
	}

	const [initialValues, setInitialValues] = useState({})

	useEffect(() => {
		setInitialValues(initialValues => {
			switch (formType) {
				case "list":
					return {fields, ...{dataObject: getInitialDataObject()}};
				case "single":
				default:
					return getInitialDataObject()
			}
		})
	}, [fields, formType])
	return (
		<Formik
			initialValues={initialValues}
			validate={values => validateForm(values)}
			onSubmit={formSubmitHandler}
			enableReinitialize={true}
		>
			{({
				values,
				errors,
				touched,
				handleChange,
				handleBlur,
				handleSubmit,
				submitForm
			}) => {
				return (

					<form
						className={`site-form ${isNotEmpty(classes) ? classes : ""}`}
						onSubmit={handleSubmit}
					>
						{modal
							?
							<Modal
								onClose={() => setShowModal(false)}
								onOpen={() => setShowModal(true)}
								open={modalOpen}
							>
								{isNotEmpty(modalTitle) && <Modal.Header>{modalTitle}</Modal.Header>}
								<Modal.Content>
									{getFormData({errors, touched, handleBlur, handleChange, values})}
								</Modal.Content>
								<Modal.Actions>
									<Button color='black' onClick={() => setShowModal(false)}>
										Cancel
									</Button>
									<Button type="submit" onClick={e => {
										e.preventDefault();
										submitForm().catch(e => console.error(e))
									}}>Save</Button>
								</Modal.Actions>
							</Modal>
							:
							getFormData({errors, touched, handleBlur, handleChange, values})
						}
					</form>
				)
			}}
		</Formik>
	);
}
export default FormBuilder;
