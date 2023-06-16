import React, {useEffect, useState} from 'react';
import FormBuilder from "./FormBuilder";
import categoryOptions from "./configs/category-options";
import {fetchRequest} from "../../library/api/state-middleware";
import {fetchTermsRequest} from "../../library/api/wp/requests/term-requests";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";
import {buildFormFieldsData, setDatatableFormValues} from "../../library/helpers/datatable/formik-helpers";

const CategoryOptionsForm = ({
	modal = false,
	showFormModal = false,
	setShowFormModal,
	formData = {},
	fieldData = {},
	formikProps = {}
}) => {
	const [terms, setTerms] = useState([]);
	const [formFields, setFormFields] = useState([]);

	function isFormikPropsValid() {
		return (isNotEmpty(formikProps) && isObject(formikProps) && !isObjectEmpty(formikProps));
	}

	function submitCallbackHandler(values) {
		console.log({formikProps, fieldData})
		if (!isFormikPropsValid()) {
			console.error('Formik props error')
			return;
		}
		const cloneValues  = {...values};
		setDatatableFormValues({
			formikProps,
			values: cloneValues,
			fieldData,
			parent: 'categoryOptions'
		})
		setShowFormModal(false)
	}

	function buildTermsState(termsData) {
		if (!Array.isArray(termsData)) {
			return;
		}
		setTerms(() => {
			let cloneArr = [...termsData];
			return cloneArr.map(item => {
				return {
					value: item?.term_id,
					label: item?.name,
				}
			})
		})
	}
	async function getTerms() {
		try {
			const results = await fetchTermsRequest();
			const termsRes = results?.data?.terms;
			if (!Array.isArray(termsRes)) {
				return;
			}
			buildTermsState(termsRes)
		} catch (e) {
			console.error(e)
		}
	}

	function buildFormFields() {
		let fields = categoryOptions;
		const termIndex = fields.findIndex(field => field?.name === 'term_id');
		if (termIndex > -1) {
			fields[termIndex].options = terms;
		}

		return fields;
	}

	useEffect(() => {
		getTerms()
	}, [])

	useEffect(() => {
		setFormFields(buildFormFields())
	}, [terms])

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

	return (
		<div>
			{Array.isArray(formFields) && formFields.length
				?
				<FormBuilder
					modal={modal}
					modalTitle={'Category Options'}
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

export default CategoryOptionsForm;
