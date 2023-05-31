export default [
	{
		rowIndex: 0,
		columnIndex: 0,
		name: "term_id",
		description: "",
		label: "Term",
		labelPosition: "",
		placeHolder: "Term",
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
	{
		rowIndex: 1,
		columnIndex: 0,
		name: "category_icon",
		description: "",
		label: "Category Icon",
		labelPosition: "",
		placeHolder: "Category Icon",
		fieldType: "text",
		type: "text",
		validation: {
			rules: [
				{
					type: "alphanumeric"
				},
				{
					type: "length",
					min: 3,
					max: 50
				}
			]
		}
	},
	{
		rowIndex: 2,
		columnIndex: 0,
		name: "category_card_icon_color",
		description: "",
		label: "Category Icon Color",
		labelPosition: "",
		placeHolder: "Category Icon Color",
		fieldType: "text",
		type: "text",
		validation: {
			rules: [
				{
					type: "alphanumeric_symbols"
				},
				{
					type: "length",
					min: 3,
					max: 50
				}
			]
		}
	},
	{
		rowIndex: 3,
		columnIndex: 0,
		name: "category_card_bg_color",
		description: "",
		label: "Category BG Color",
		labelPosition: "",
		placeHolder: "Category BG Color",
		fieldType: "text",
		type: "text",
		validation: {
			rules: [
				{
					type: "alphanumeric_symbols"
				},
				{
					type: "length",
					min: 3,
					max: 50
				}
			]
		}
	},
	{
		rowIndex: 4,
		columnIndex: 0,
		name: "category_card_text_color",
		description: "",
		label: "Category Text Color",
		labelPosition: "",
		placeHolder: "Category Text Color",
		fieldType: "text",
		type: "text",
		validation: {
			rules: [
				{
					type: "alphanumeric_symbols"
				},
				{
					type: "length",
					min: 3,
					max: 50
				}
			]
		}
	},
]
