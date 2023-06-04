import React, {useEffect, useState} from 'react';
import FormBuilder from "../../FormBuilder";
import categoryOptions from "../../configs/category-options";
import {fetchRequest} from "../../../../library/api/middleware";
import {fetchTermsRequest} from "../../../../library/api/wp/requests/term-requests";
import {isNotEmpty, isObject, isObjectEmpty} from "../../../../library/helpers/utils-helpers";
import {
	fetchAllOptionGroupsRequest,
	getOptionGroupSelectData
} from "../../../../library/api/wp/requests/option-group-requests";
import screenOptions from "../../configs/screen-options";
import {buildFormFieldsData, setDatatableFormValues} from "../../../../library/helpers/datatable/formik-helpers";
import {fetchRolesRequest, getRolesSelectData} from "../../../../library/api/wp/requests/auth-requests";
import accessControlOptions from "../../configs/access-control-options";
import {updateMenuItemRolesRequest} from "../../../../library/api/wp/requests/menu-requests";
import {isFunction} from "underscore";

const AccessControlForm = ({
								 modal = false,
								 showFormModal = false,
								 setShowFormModal,
								 formData = {},
								 fieldData = {},
								 formikProps = {},
	updateCallback
							 }) => {
	const [message, setMessage] = useState(null);
	const [showMessage, setShowMessage] = useState(false);

	const [formFields, setFormFields] = useState([]);
	const [roles, setRoles] = useState([]);

	function isFormikPropsValid() {
		return (isNotEmpty(formikProps) && isObject(formikProps) && !isObjectEmpty(formikProps));
	}

	async function fetchRoles() {
		const results = await fetchRolesRequest();

		if (!results) {
			return;
		}
		setRoles(
			results
		)
	}

	async function submitCallbackHandler(values) {
		if (!isFormikPropsValid()) {
			console.error('Formik props error')
			return;
		}
		let cloneValues, requestValues;
		cloneValues = requestValues = {...values};
		if (Array.isArray(cloneValues?.roles)) {
			requestValues.roles = cloneValues.roles.map(role => role.value);
		}
		if (isFunction(updateCallback)) {
			const updateResults = await updateCallback({values: requestValues, fieldData, formData, setMessage});
			cloneValues = updateResults;
			setShowMessage(true);
		}
		setDatatableFormValues({
			formikProps,
			values: cloneValues,
			fieldData,
			parent: 'accessControl'
		})
		// setShowFormModal(false);
	}

	function updateHandler(values) {
		if (
			typeof formData.items[fieldData.index]['state'] === 'undefined'
		) {
			return;
		}
		console.log(formData.items[fieldData.index])
		switch (formData.items[fieldData.index]['state']) {
			case 'update':
				console.log(formData.items[fieldData.index])
				// const results = updateMenuItemRolesRequest({
				// 	menuId: formData.items[fieldData.index]['menu_id'],
				// 	menuItemId: formData.items[fieldData.index]['id'],
				// 	data: values
				// })
		}
		setShowFormModal(false);
	}

	function buildFormFields() {
		let fields = accessControlOptions;
		const rolesIndex = fields.findIndex(field => field?.name === 'roles');
		if (rolesIndex > -1) {
			const rolesOptions = getRolesSelectData(roles);
			if (Array.isArray(rolesOptions)) {
				fields[rolesIndex].options = rolesOptions;
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
			fieldData,
			parent: 'accessControl'
		}))
	}, [showFormModal])

	useEffect(() => {
		setFormFields(buildFormFields())
	}, [roles])

	useEffect(() => {
		fetchRoles()
	}, [])
	return (
		<div>
			{Array.isArray(formFields) && formFields.length
				?
				<FormBuilder
					modal={modal}
					modalTitle={'Access Control Options'}
					modalOpen={showFormModal}
					setShowModal={value => setShowFormModal(value)}
					fields={formFields}
					formType={"single"}
					submitCallback={submitCallbackHandler}
					submitButtonText={'Save'}
					showMessageBox={showMessage}
					messageBoxMessage={message}
				/>
				:
				<div>error</div>
			}
		</div>
	);
};

export default AccessControlForm;
