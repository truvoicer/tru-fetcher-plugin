import React, {useEffect, useState} from 'react';
import FormBuilder from "./FormBuilder";
import categoryOptions from "./configs/category-options";
import {fetchRequest} from "../../library/api/state-middleware";
import {fetchTermsRequest} from "../../library/api/wp/requests/term-requests";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";
import {
	fetchAllOptionGroupsRequest,
	getOptionGroupSelectData
} from "../../library/api/wp/requests/option-group-requests";
import articleOptions from "./configs/article-options";
import {buildFormFieldsData, setDatatableFormValues} from "../../library/helpers/datatable/formik-helpers";

const ArticleOptionsForm = ({
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
		let fields = articleOptions;
		const articlesSortByIndex = fields.findIndex(field => field?.name === 'articles_sort_by');
		if (articlesSortByIndex > -1) {
			const sortByOptions = getOptionGroupSelectData('sort_by', optionGroups);
			if (Array.isArray(sortByOptions?.options)) {
				fields[articlesSortByIndex].options = sortByOptions.options;
			}
		}
		const articlesSortOrderIndex = fields.findIndex(field => field?.name === 'articles_sort_order');
		if (articlesSortOrderIndex > -1) {
			const sortOrderOptions = getOptionGroupSelectData('sort_order', optionGroups);
			if (Array.isArray(sortOrderOptions?.options)) {
				fields[articlesSortOrderIndex].options = sortOrderOptions.options;
			}
		}
		const multipleModeIndex = fields.findIndex(field => field?.name === 'featured_articles_multiple_mode');
		if (multipleModeIndex > -1) {
			const multipleModeOptions = getOptionGroupSelectData('featured_articles_multiple_mode', optionGroups);
			if (Array.isArray(multipleModeOptions?.options)) {
				fields[multipleModeIndex].options = multipleModeOptions.options;
			}
		}

		return fields;
	}

	useEffect(() => {
		setFormFields(buildFormFields())
	}, [optionGroups])

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
		fetchOptionGroups()
	}, [])

	return (
		<div>
			{Array.isArray(formFields) && formFields.length
				?
				<FormBuilder
					modal={modal}
					modalTitle={'Article Options'}
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

export default ArticleOptionsForm;
