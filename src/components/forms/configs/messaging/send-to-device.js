export default [
	{
		rowIndex: 0,
		columnIndex: 0,
		name: "all_devices",
		description: "",
		label: "All Devices?",
		labelPosition: "",
		placeHolder: "All Devices?",
		fieldType: "checkbox",
		checkboxType: "true_false",
		value: false,
		subFields: [
			{
				dependsOn: {
					field: "all_devices",
					value: false
				},
				rowIndex: 0,
				columnIndex: 0,
				name: "devices",
				description: "",
				label: "Select Devices",
				labelPosition: "",
				placeHolder: "Select Devices",
				fieldType: "select",
				type: "select",
				options: [],
				props: {
					isMulti: true
				},
				validation: {
					rules: [
						{
							type: "alphanumeric"
						},
					]
				}
			},
			{
				dependsOn: {
					field: "all_devices",
					value: false
				},
				rowIndex: 1,
				columnIndex: 0,
				name: "title",
				description: "",
				label: "Title",
				labelPosition: "",
				placeHolder: "Title",
				fieldType: "text",
				type: "text",
				validation: {
					rules: [
						{
							type: "alphanumeric"
						},
					]
				}
			},
			{
				dependsOn: {
					field: "all_devices",
					value: false
				},
				rowIndex: 2,
				columnIndex: 0,
				name: "message",
				description: "",
				label: "Message",
				labelPosition: "",
				placeHolder: "Message",
				fieldType: "textarea",
				type: "textarea",
				validation: {
					rules: [
						{
							type: "alphanumeric"
						},
					]
				}
			},
			{
				dependsOn: {
					field: "all_devices",
					value: false
				},
				rowIndex: 3,
				columnIndex: 0,
				name: "image",
				description: "",
				label: "Image",
				labelPosition: "",
				placeHolder: "Image",
				fieldType: "file",
			},
		]
	},

]
