/**
 * Form Fields Configuration
 * Auto-generated — edit data_list / class / is_visible as needed.
 */

export default [
	{
		name: "journal_id",
		label: "Select Journal",
		type: "select",
		multiple: false,
		data_list: [],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "account_id",
		label: "Select Account",
		type: "select",
		multiple: false,
		data_list: [],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "entry_type",
		label: "Select Entry Type",
		type: "select",
		label: "Select Entry Type",
		multiple: false,
		data_list: [
			{ label: "Debit", value: "debit" },
			{ label: "Credit", value: "credit" },
		],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "amount",
		label: "Enter Amount",
		type: "text",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "description",
		label: "Enter Description",
		type: "textarea",
		rows: 4,
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
];
