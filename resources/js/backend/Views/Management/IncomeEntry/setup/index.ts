import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "IncomeEntry";

const setup: setup_type = {
    prefix,
    module_name: "income_entry",
    store_prefix: "income_entry",
    route_prefix: "IncomeEntry",
    route_path: "income-entry",

    permission: ["admin", "super_admin"],

    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "income-entries",

    select_fields: [
        "id",
        "voucher_no",
        "income_source",
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
        "income_source",
        "amount",
        "entry_date",
        "status",
        "created_at",
    ],

    table_header_data: [
        "id",
        "voucher_no",
        "income_source",
        "account",
        "amount",
        "entry_date",
        "status",
        "created_at",
    ],

    table_row_data: [
        "id",
        "voucher_no",
        "income_source",
        "account",
        "amount",
        "entry_date",
        "status",
        "created_at",
    ],

    quick_view_data: [
        "id",
        "voucher_no",
        "income_source",
        "account",
        "amount",
        "entry_date",
        "description",
        "status",
        "created_at",
    ],

    layout_title: prefix + " Management",
    page_title: `${prefix} Management`,
    all_page_title: "All Income Entries",
    details_page_title: "Income Entry Details",
    create_page_title: "Add Income Entry",
    edit_page_title: "Edit Income Entry",
};

export default setup;
