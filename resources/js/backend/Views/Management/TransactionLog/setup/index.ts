import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "TransactionLog";

const setup: setup_type = {
    prefix,
    module_name: "transaction_log",
    store_prefix: "transaction_log",
    route_prefix: "TransactionLog",
    route_path: "transaction-log",

    permission: ["admin", "super_admin"],
    readonly: true,

    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "transaction-logs",

    select_fields: [
        "id",
        "voucher_no",
        "transaction_type",
        "related_type",
        "user_id",
        "amount",
        "direction",
        "balance_after",
        "transaction_date",
        "description",
        "status",
        "slug",
        "created_at",
    ],

    sort_by_cols: [
        "id",
        "voucher_no",
        "transaction_type",
        "amount",
        "direction",
        "transaction_date",
        "created_at",
    ],

    table_header_data: [
        "id",
        "voucher_no",
        "transaction_type",
        "member",
        "amount",
        "direction",
        "balance_after",
        "transaction_date",
        "created_at",
    ],

    table_row_data: [
        "id",
        "voucher_no",
        "transaction_type",
        "member",
        "amount",
        "direction",
        "balance_after",
        "transaction_date",
        "created_at",
    ],

    quick_view_data: [
        "id",
        "voucher_no",
        "transaction_type",
        "related_type",
        "member",
        "amount",
        "direction",
        "balance_after",
        "transaction_date",
        "description",
        "status",
        "created_at",
    ],

    layout_title: "Transaction Log",
    page_title: "Transaction Log",
    all_page_title: "All Transaction Logs",
    details_page_title: "Transaction Details",
    create_page_title: "Add Transaction",
    edit_page_title: "Edit Transaction",
};

export default setup;
