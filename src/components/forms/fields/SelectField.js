import React from 'react';
// import {Select} from "semantic-ui-react";
import Select from 'react-select'
import {isNotEmpty} from "../../../library/helpers/utils-helpers";

const SelectField = ({
	name,
	placeholder = 'Please select',
	options = [],
	value,
	defaultValue ,
	onChangeHandler,
	extraProps = {}
}) => {
	let selectProps = {};
	if (typeof onChangeHandler !== 'function') {
		console.error(`Select onChangeHandler invalid for ${name}`)
		return null;
	}
	if (!isNotEmpty(name)) {
		console.error(`Name invalid for ${name}`)
		return null;
	}

	if (isNotEmpty(value)) {
		selectProps.value = value;
	} else if (isNotEmpty(defaultValue)) {
		selectProps.defaultValue = defaultValue;
	} else {
		selectProps.defaultValue = '';
	}
	return (
		<Select
			placeholder={placeholder}
			id={name}
			options={options}
			onChange={onChangeHandler}
			{...selectProps}
			{...extraProps}
		/>
	);
};

export default SelectField;
