export default [
	{
		rowIndex: 0,
		columnIndex: 0,
		name: "articles_show_all",
		description: "",
		label: "Show All Articles?",
		labelPosition: "",
		placeHolder: "Show All Articles?",
		fieldType: "checkbox",
		checkboxType: "true_false",
		value: true,
	},
	{
		rowIndex: 1,
		columnIndex: 0,
		name: "articles_sort_by",
		description: "",
		label: "Articles Sort By",
		labelPosition: "",
		placeHolder: "Articles Sort By",
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
		rowIndex: 2,
		columnIndex: 0,
		name: "articles_sort_order",
		description: "",
		label: "Articles Sort Order",
		labelPosition: "",
		placeHolder: "Articles Sort Order",
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
		name: "featured_articles_show",
		description: "",
		label: "Show Featured Articles",
		labelPosition: "",
		placeHolder: "Show Featured Articles",
		fieldType: "checkbox",
		checkboxType: "true_false",
		value: true,
	},
	{
		rowIndex: 4,
		columnIndex: 0,
		name: "featured_articles_show_multiple",
		description: "",
		label: "Featured Articles Show Multiple",
		labelPosition: "",
		placeHolder: "Featured Articles Show Multiple",
		fieldType: "checkbox",
		value: true,
	},
	{
		rowIndex: 5,
		columnIndex: 0,
		name: "featured_articles_multiple_mode",
		description: "",
		label: "Featured Articles Multiple Mode",
		labelPosition: "",
		placeHolder: "Featured Articles Multiple Mode",
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
		rowIndex: 5,
		columnIndex: 0,
		name: "featured_articles_slideshow_timer",
		description: "",
		label: "Featured Articles Slideshow Timer",
		labelPosition: "",
		placeHolder: "Featured Articles Slideshow Timer",
		fieldType: "text",
		type: "tel",
		validation: {
			rules: [
				{
					type: "numeric"
				},
			]
		}
	},
]
