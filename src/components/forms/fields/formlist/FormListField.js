import React, {useEffect, useState} from 'react';
import {isNotEmpty, isSet} from "../../../../library/helpers/utils-helpers";

const FormListField = ({
	data,
	name = "form_list",
	listItemKeyLabel = "Key",
	listItemValueLabel = "Value",
	addRowLabel = null,
	callback,
	arrayFieldIndex = false
}) => {
	const listClass = "form-list";
	const listGroupClass = "form-list-items";
	const listRowClass = "form-list-row";
	const listItemKeyClass = "form-list-item-key";
	const listItemValueClass = "form-list-item-value";

	const [initialSet, setInitialSet] = useState(false);
	const [formList, setFormList] = useState([]);
	const [addRowLabelText, setAddRowLabelText] = useState("Add New");

	useEffect(() => {
		if (isNotEmpty(addRowLabel)) {
			setAddRowLabelText(addRowLabel)
		}
	}, [addRowLabel])
	useEffect(() => {

		if ((isSet(data) && Array.isArray(data))) {
			setFormList(data)
			setInitialSet(true)
		}
	}, [data])

	const addFormListRow = (e) => {
		let formListState = [...formList];
		formListState.push(formListRow())
		setFormList(formListState)
	}

	const removeFormListRow = (index, e) => {
		setFormList(formList => {
			let formListState = [...formList];
			formListState.splice(index, 1)
			return formListState;
		})
		formChangeHandler()
	}

	const formListRow = (index) => {
		return {
			name: "",
			value: ""
		}
	}

	const formChangeHandler = (e) => {
		let listRows = Array.from(document.getElementsByClassName(listRowClass));
		let queryData = [];
		listRows.map((item, index) => {
			let itemKey = item.getElementsByClassName(listItemKeyClass)[0];
			let itemValue = item.getElementsByClassName(listItemValueClass)[0];
			queryData.push({
				name: itemKey.value,
				value: itemValue.value
			})
		})
		callback(name, queryData, arrayFieldIndex);
	}
	console.log(formList)
	return (
		<div className={listClass}>
			<button className={"btn btn-primary btn-sm add-row-button"}
					onClick={addFormListRow}
					type={"button"}>
				{addRowLabelText ? addRowLabelText : addRowLabelText}
			</button>
			<div className={listGroupClass}>
				{formList.map((item, index) => (
					<div className={listRowClass + " list-item-" + index.toString()}
						 key={index.toString()}>
						{/*<Row>*/}
						{/*	<Col sm={12} md={12} lg={5}>*/}
								<input
									className={listItemKeyClass}
									placeholder={listItemKeyLabel ? listItemKeyLabel : listItemKeyLabel}
									defaultValue={item.name}
									onChange={formChangeHandler}
								/>
							{/*</Col>*/}
							{/*<Col sm={12} md={12} lg={5}>*/}
								<input
									className={listItemValueClass}
									placeholder={listItemValueLabel ? listItemValueLabel : listItemValueLabel}
									defaultValue={item.value}
									onChange={formChangeHandler}
								/>
							{/*</Col>*/}
							{/*<Col sm={12} md={12} lg={2}>*/}
								<a className={"form-list-row--new"}
								   onClick={addFormListRow}
								   style={{
									   cursor: "pointer"
								   }}
								>
									<i className="fas fa-plus-circle"/>
								</a>
								<a
									className={"form-list-row--remove"}
									onClick={removeFormListRow.bind(this, index)}
									style={{
										cursor: "pointer"
									}}
								>
									<i className="fas fa-trash-alt"/>
								</a>
						{/*	</Col>*/}
						{/*</Row>*/}
					</div>
				))}
			</div>
		</div>

	);

}

export default FormListField;
