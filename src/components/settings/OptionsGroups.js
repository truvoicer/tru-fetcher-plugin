import React, {useEffect, useState} from 'react';
import {Button, Modal, Popup, Select} from "semantic-ui-react";
import {fetchRequest} from "../../library/api/middleware";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable, {DATA_SOURCE_LOCAL} from "../tables/DataTable";
import {STATE_CREATE, STATE_UPDATE} from "../../library/constants/constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";
import CategoryOptionsForm from "../forms/CategoryOptionsForm";
import {getCheckboxInput} from "../tables/fields";
import OptionsGroupItems from "./OptionsGroupItems";
import {fetchAllOptionGroupsRequest} from "../../library/api/wp/requests/option-group-requests";

const OptionsGroups = ({session}) => {

	const [showFormModal, setShowFormModal] = useState(false);
	const [modalComponent, setModalComponent] = useState(null);
	const [modalTitle, setModalTitle] = useState('');
	return (
		<div>
			<DataTable
				heading={'Options'}
				itemStructure={{
					name: '',
					default_value: '',
					option_group_items: {
						option_group_id: '',
						option_key: '',
						option_value: '',
						option_text: '',
					}
				}}
				columns={[
					{
						name: 'Name',
						dataKey: 'name',
					},
					{
						name: 'Default Value',
						dataKey: 'default_value',
					},
					{
						name: 'Option Items',
						render: ({formData, fieldData, column, formikProps, formChangeCallback}) => {
							function getOptionGroup() {
								let optionGroup = [];
								if (typeof formikProps?.values?.items[fieldData.index] === 'undefined') {
									return false;
								}
								optionGroup = formikProps.values.items[fieldData.index];
								if (isObjectEmpty(optionGroup)) {
									return false
								}
								return optionGroup;
							}
							const optionGroup = getOptionGroup();
							return (
								<Popup
									content={(optionGroup?.id)? 'Add/Edit/Delete Items' : 'Save option group to modify items'}
									trigger={(
										<div>
										<Button
											disabled={!(optionGroup?.id)}
											type={'button'}
											onClick={e => {
												OptionsGroupItems.defaultProps = {
													formikProps,
													formData,
													fieldData,
													optionGroup
												}
												setModalComponent(<OptionsGroupItems />)
												setShowFormModal(true)
											}}
										>
											Option Group Items
										</Button>
										</div>
									)}
								/>
							);
						}
					}
				]}
				stateHandleCallback={({formItem, formData}) => {
					let state;
					const findItem = formData.items.find(item => {
						if (!formItem?.id && !item?.id) {
							return false;
						}
						return formItem?.id === item?.id;
					});
					if (findItem) {
						state = STATE_UPDATE
					} else {
						state = STATE_CREATE
					}
					return state;
				}}
				fetchEndpoint={[
					{
						name: 'optionGroups',
						endpoint: 'option-group/list',
						objectListKey: 'optionGroups'
					},
				]}
				formDataCallback={({endpointsObject, setItems, setFormData}) => {
					const optionGroupsResults = endpointsObject?.optionGroups?.data;
					if (!Array.isArray(optionGroupsResults)) {
						return;
					}
					const optionGroups = optionGroupsResults.map(optionGroup => {
						let data = {state: STATE_UPDATE};
						if (!isObjectEmpty(optionGroup)) {
							data = {...data, ...optionGroup}
						}
						return data
					});
					// setItems(optionGroups)
					setFormData({
						items: optionGroups
					})
				}}
				updateEndpoint={({data}) => {
					if (!isNaN(data?.id)) {
						return `option-group/${data.id}/update`;
					}
					return false;
				}}
				createEndpoint={'option-group/create'}
				deleteEndpoint={'option-group/delete'}
				saveBatchEndpoint={'option-group/save'}
				deleteItemCompareKeys={['id']}
				objectListKey={'optionGroups'}
				objectItemKey={'optionGroup'}
				idKey={'id'}>
			</DataTable>

			<Modal
				onClose={() => setShowFormModal(false)}
				onOpen={() => setShowFormModal(true)}
				open={showFormModal}
			>
				{isNotEmpty(modalTitle) && <Modal.Header>{modalTitle}</Modal.Header>}
				<Modal.Content>
					{modalComponent}
				</Modal.Content>
				<Modal.Actions>
					<Button color='black' onClick={() => setShowFormModal(false)}>
						Cancel
					</Button>
				</Modal.Actions>
			</Modal>
		</div>
	);
};

export default connect(
	(state) => {
		return {
			session: state[SESSION_STATE]
		}
	},
	null
)(OptionsGroups);
