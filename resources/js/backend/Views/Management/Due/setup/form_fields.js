/**
 * Form Fields Configuration
 * Auto-generated — edit data_list / class / is_visible as needed.
 */

export default [
	{
		name: "due_amount",
		label: "Enter Due Amount",
		type: "text",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "paid_amount",
		label: "Enter Paid Amount",
		type: "text",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "remaining_amount",
		label: "Enter Remaining Amount",
		type: "text",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "for_month",
		label: "Enter For Month",
		type: "date",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "due_date",
		label: "Enter Due Date",
		type: "date",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "payment_status",
		label: "Select Payment Status",
		type: "select",
		label: "Select Payment Status",
		multiple: false,
		data_list: [
			{ label: "Unpaid", value: "unpaid" },
			{ label: "Partial", value: "partial" },
			{ label: "Paid", value: "paid" },
		],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "note",
		label: "Enter Note",
		type: "textarea",
		rows: 4,
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
];
