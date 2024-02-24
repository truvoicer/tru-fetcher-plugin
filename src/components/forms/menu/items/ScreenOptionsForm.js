import React, {useEffect, useState} from 'react';
import FormBuilder from "../../FormBuilder";
import categoryOptions from "../../configs/category-options";
import {fetchRequest} from "../../../../library/api/state-middleware";
import {fetchTermsRequest} from "../../../../library/api/wp/requests/term-requests";
import {isNotEmpty, isObject, isObjectEmpty} from "../../../../library/helpers/utils-helpers";
import {
	fetchAllOptionGroupsRequest,
	getOptionGroupSelectData
} from "../../../../library/api/wp/requests/option-group-requests";
import screenOptions from "../../configs/screen-options";
import {buildFormFieldsData, setDatatableFormValues} from "../../../../library/helpers/datatable/formik-helpers";

const ScreenOptionsForm = ({
								 modal = false,
								 showFormModal = false,
								 setShowFormModal,
								 formData = {},
								 fieldData = {},
								 formikProps = {}
							 }) => {
	const [formFields, setFormFields] = useState([]);
	const [optionGroups, setOptionGroups] = useState([]);

	function isFormikPropsValid() {
		return (isNotEmpty(formikProps) && isObject(formikProps) && !isObjectEmpty(formikProps));
	}

	async function fetchOptionGroups() {
		const results = await fetchAllOptionGroupsRequest();

		if (!results) {
			return;
		}
		setOptionGroups(
			results
		)
	}

	function submitCallbackHandler(values) {
		if (!isFormikPropsValid()) {
			console.error('Formik props error')
			return;
		}
		const cloneValues  = {...values};
		setDatatableFormValues({
			formikProps,
			values: cloneValues,
			fieldData
		})
		setShowFormModal(false)
	}

	function buildFormFields() {
		let fields = screenOptions;
		const screenTypeIndex = fields.findIndex(field => field?.name === 'type');
		if (screenTypeIndex > -1) {
			const screenTypeOptions = getOptionGroupSelectData('screen_type', optionGroups);
			if (Array.isArray(screenTypeOptions?.options)) {
				fields[screenTypeIndex].options = screenTypeOptions.options;
			}
		}
		const screenIndex = fields.findIndex(field => field?.name === 'screen');
		if (screenIndex > -1) {
			const screenOptions = getOptionGroupSelectData('screen', optionGroups);
			if (Array.isArray(screenOptions?.options)) {
				fields[screenIndex].options = screenOptions.options;
			}
		}
		return fields;
	}

	useEffect(() => {
		if (!showFormModal) {
			return;
		}
		setFormFields(buildFormFieldsData({
			formikProps,
			formFields,
			fieldData
		}))
	}, [showFormModal])

	useEffect(() => {
		setFormFields(buildFormFields())
	}, [optionGroups])

	useEffect(() => {
		fetchOptionGroups()
	}, [])

	return (
		<div>
			{Array.isArray(formFields) && formFields.length
				?
				<FormBuilder
					modal={modal}
					modalTitle={'Screen Options'}
					modalOpen={showFormModal}
					setShowModal={value => setShowFormModal(value)}
					fields={formFields}
					formType={"single"}
					submitCallback={submitCallbackHandler}
					submitButtonText={'Save'}
				/>
				:
				<div>error</div>
			}
		</div>
	);
};

export default ScreenOptionsForm;
