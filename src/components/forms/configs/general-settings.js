export default [
	{
		rowIndex: 0,
		columnIndex: 0,
		name: "article_source",
		description: "",
		label: "Article Source",
		labelPosition: "",
		placeHolder: "Article Source",
		fieldType: "select",
		options: [
			{
				value: 'post',
				label: 'Post',
			},
			{
				value: 'page',
				label: 'Page',
			},
		],
		validation: {
			rules: [
				{
					type: "alphanumeric"
				},
			]
		}
	},
	{
		rowIndex: 1,
		columnIndex: 0,
		name: "default_theme",
		description: "",
		label: "Default Theme",
		labelPosition: "",
		placeHolder: "Default Theme",
		fieldType: "select",
		options: [
			{
				value: 'light',
				label: 'light',
			},
			{
				value: 'dark',
				label: 'Dark',
			},
		],
		validation: {
			rules: [
				{
					type: "alphanumeric"
				},
			]
		}
	},
	{
		rowIndex: 2,
		columnIndex: 0,
		name: "menu_id",
		description: "",
		label: "Main Menu",
		labelPosition: "",
		placeHolder: "Main Menu",
		fieldType: "select",
		options: [],
		validation: {
			rules: [
				{
					type: "numeric"
				},
			]
		}
	},
]
