/**
 * Form Fields Configuration
 */

export default [
	{
		name: "user_id",
		label: "Select Member",
		type: "select",
		multiple: false,
		data_list: [],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "deposit_type",
		label: "Deposit Type",
		type: "select",
		multiple: false,
		data_list: [
			{ label: "Share Deposit (মাসিক শেয়ার)", value: "share_deposit" },
			{ label: "Extra Savings (অতিরিক্ত সঞ্চয়)", value: "extra_savings" },
		],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "amount",
		label: "Amount (পরিমাণ)",
		type: "number",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "for_month",
		label: "For Month (কোন মাসের জন্য)",
		type: "date",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "payment_date",
		label: "Payment Date (পরিশোধের তারিখ)",
		type: "date",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "payment_method",
		label: "Payment Method",
		type: "select",
		multiple: false,
		data_list: [
			{ label: "Cash (নগদ)", value: "cash" },
			{ label: "Bank (ব্যাংক)", value: "bank" },
			{ label: "Mobile Banking", value: "mobile_banking" },
		],
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	
	{
		name: "image",
		label: "Receipt / Image (রসিদ বা ছবি)",
		type: "file",
		accept: "image/*,application/pdf",
		value: "",
		is_visible: true,
		class: "col-md-6",
	},
	{
		name: "note",
		label: "Note (মন্তব্য)",
		type: "textarea",
		rows: 3,
		value: "",
		is_visible: true,
		class: "col-md-12",
	},
];
