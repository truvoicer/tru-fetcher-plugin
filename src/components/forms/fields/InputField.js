import React from 'react';

const InputField = ({name, placeHolder, type = 'text', onChangeHandler, onClick, defaultValue, extraInputProps = {}}) => {
	return (
		<input
			name={name}
			placeholder={placeHolder}
			type={type}
			onChange={onChangeHandler}
			onClick={onClick}
			defaultValue={defaultValue || ''}
			{...extraInputProps}
		/>
	)
};

export default InputField;
