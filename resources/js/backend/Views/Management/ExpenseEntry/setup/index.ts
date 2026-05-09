import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "ExpenseEntry";

const setup: setup_type = {
    prefix,
    module_name: "expense_entry",
    store_prefix: "expense_entry",
    route_prefix: "ExpenseEntry",
    route_path: "expense-entry",

    permission: ["admin", "super_admin"],

    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "expense-entries",

    select_fields: [
        "id",
        "voucher_no",
        "expense_category",
        "account_id",
        "amount",
        "entry_date",
        "description",
        "status",
        "slug",
        "created_at",
    ],

    sort_by_cols: [
        "id",
        "voucher_no",
        "expense_category",
        "amount",
        "entry_date",
        "status",
        "created_at",
    ],

    table_header_data: [
        "id",
        "voucher_no",
        "expense_category",
        "account",
        "amount",
        "entry_date",
        "status",
        "created_at",
    ],

    table_row_data: [
        "id",
        "voucher_no",
        "expense_category",
        "account",
        "amount",
        "entry_date",
        "status",
        "created_at",
    ],

    quick_view_data: [
        "id",
        "voucher_no",
        "expense_category",
        "account",
        "amount",
        "entry_date",
        "description",
        "status",
        "created_at",
    ],

    layout_title: prefix + " Management",
    page_title: `${prefix} Management`,
    all_page_title: "All Expense Entries",
    details_page_title: "Expense Entry Details",
    create_page_title: "Add Expense Entry",
    edit_page_title: "Edit Expense Entry",
};

export default setup;
