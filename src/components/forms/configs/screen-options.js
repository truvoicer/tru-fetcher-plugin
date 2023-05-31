export default [
	{
		rowIndex: 0,
		columnIndex: 0,
		name: "initial_screen",
		description: "",
		label: "Initial Screen",
		labelPosition: "",
		placeHolder: "Initial Screen",
		fieldType: "checkbox",
		checkboxType: "true_false",
		value: true,
	},
	{
		rowIndex: 1,
		columnIndex: 0,
		name: "active",
		description: "",
		label: "Active?",
		labelPosition: "",
		placeHolder: "Active?",
		fieldType: "checkbox",
		checkboxType: "true_false",
		value: true,
	},
	{
		rowIndex: 2,
		columnIndex: 0,
		name: "type",
		description: "",
		label: "Screen Type",
		labelPosition: "",
		placeHolder: "Screen Type",
		fieldType: "select",
		type: "select",
		options: [],
		validation: {
			rules: [
				{
					type: "alphanumeric"
				},
			]
		}
	},
	{
		rowIndex: 3,
		columnIndex: 0,
		name: "screen",
		description: "",
		label: "Screen",
		labelPosition: "",
		placeHolder: "Screen",
		fieldType: "select",
		type: "select",
		options: [],
		validation: {
			rules: [
				{
					type: "alphanumeric"
				},
			]
		}
	},
]
