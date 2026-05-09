/**
 * Form Fields Configuration
 * Auto-generated — edit data_list / class / is_visible as needed.
 */

export default [
	{
		name: "account_code",
		label: "Enter Account Code",
		type: "text",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "account_name",
		label: "Enter Account Name",
		type: "text",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "account_type",
		label: "Select Account Type",
		type: "select",
		label: "Select Account Type",
		multiple: false,
		data_list: [
			{ label: "Asset", value: "asset" },
			{ label: "Liability", value: "liability" },
			{ label: "Equity", value: "equity" },
			{ label: "Income", value: "income" },
			{ label: "Expense", value: "expense" },
		],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "parent_id",
		label: "Select Parent",
		type: "select",
		multiple: false,
		data_list: [],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "opening_balance",
		label: "Enter Opening Balance",
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
