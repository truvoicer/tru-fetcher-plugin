import React, {useEffect, useState} from 'react';
import {Button, Modal, Popup, Select} from "semantic-ui-react";
import {SESSION_STATE} from "../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable, {DATA_SOURCE_LOCAL} from "../../tables/DataTable";
import {STATE_CREATE, STATE_UPDATE} from "../../../library/constants/constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../../library/helpers/utils-helpers";
import MenuItems from "./MenuItems";

const Menus = ({session}) => {
	const [showFormModal, setShowFormModal] = useState(false);
	const [modalComponent, setModalComponent] = useState(null);
	const [modalTitle, setModalTitle] = useState('');
	return (
		<div>
			<DataTable
				heading={'Menus'}
				itemStructure={{
					name: '',
					default_value: ''
				}}
				columns={[
					{
						name: 'Name',
						dataKey: 'name',
					},
					{
						name: 'Menu Items',
						render: ({formData, fieldData, column, formikProps, formChangeCallback}) => {
							function getMenu() {
								let menu = [];
								if (typeof formikProps?.values?.items[fieldData.index] === 'undefined') {
									return false;
								}
								menu = formikProps.values.items[fieldData.index];
								if (isObjectEmpty(menu)) {
									return false
								}
								return menu;
							}
							const menu = getMenu();
							return (
								<Popup
									content={(menu?.id)? 'Add/Edit/Delete Items' : 'Save menu to modify items'}
									trigger={(
										<div>
										<Button
											disabled={!(menu?.id)}
											type={'button'}
											onClick={e => {
												MenuItems.defaultProps = {
													menu
												}
												setModalComponent(<MenuItems />)
												setShowFormModal(true)
											}}
										>
											Menu Items
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
						name: 'menus',
						endpoint: 'menus',
						objectListKey: 'menus'
					},
				]}
				formDataCallback={({endpointsObject, setItems, setFormData}) => {
					const menuResults = endpointsObject?.menus?.data;
					if (!Array.isArray(menuResults)) {
						return;
					}
					const menus = menuResults.map(menu => {
						let data = {state: STATE_UPDATE};
						if (!isObjectEmpty(menu)) {
							data = {...data, ...menu}
						}
						return data
					});
					setFormData({
						items: menus
					})
				}}
				updateEndpoint={({data}) => {
					if (!isNaN(data?.id)) {
						return `menu/${data.id}/update`;
					}
					return false;
				}}
				createEndpoint={'menu/create'}
				deleteEndpoint={'menu/delete'}
				saveBatchEndpoint={'menu/save'}
				deleteItemCompareKeys={['id']}
				objectListKey={'menus'}
				objectItemKey={'menus'}
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
)(Menus);
