import React, {useEffect, useState} from 'react';
import {Button, Select} from "semantic-ui-react";
import {fetchRequest} from "../../library/api/middleware";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable, {DATA_SOURCE_LOCAL} from "../tables/DataTable";
import {STATE_CREATE, STATE_DELETE, STATE_INITIAL, STATE_UPDATE} from "../../library/constants/constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../library/helpers/utils-helpers";
import CategoryOptionsForm from "../forms/CategoryOptionsForm";
import {getCheckboxInput} from "../tables/fields";

const OptionsGroupItems = ({
	session,
	optionGroup = null
}) => {

	return (
		<div>
			<DataTable
				heading={'Option Group Items'}
				itemStructure={{
					option_key: '',
					option_value: '',
					option_text: '',
				}}
				columns={[
					{
						name: 'Key',
						dataKey: 'option_key',
					},
					{
						name: 'Value',
						dataKey: 'option_value',
					},
					{
						name: 'Text',
						dataKey: 'option_text',
					},
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
						name: 'optionGroupItems',
						endpoint: `option-group/${optionGroup?.id}/items`,
						objectListKey: 'optionGroupItems'
					}
				]}
				formDataCallback={({endpointsObject, setItems, setFormData}) => {
					const optionGroupsResults = endpointsObject?.optionGroupItems?.data;
					if (!Array.isArray(optionGroupsResults)) {
						return;
					}
					const optionGroupItems = optionGroupsResults.map(optionGroupItem => {
						return {
							state: STATE_UPDATE,
							...optionGroupItem
						};
					});
					setFormData({
						items: optionGroupItems
					})
				}}
				updateEndpoint={({data}) => {
					if (!isNaN(data?.id)) {
						return `option-group/${optionGroup.id}/item/${data.id}/update`;
					}
					return false;
				}}
				createEndpoint={`option-group/${optionGroup?.id}/item/create`}
				deleteEndpoint={`option-group/${optionGroup?.id}/item/delete`}
				saveBatchEndpoint={`option-group/${optionGroup?.id}/item/save`}
				deleteItemCompareKeys={['id']}
				objectListKey={'optionGroupItems'}
				objectItemKey={'optionGroupItem'}
				objectItemDeleteKey={'optionGroupItems'}
				idKey={'id'}>
			</DataTable>

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
)(OptionsGroupItems);
