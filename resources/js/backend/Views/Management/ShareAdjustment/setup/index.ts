import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "ShareAdjustment";

const setup: setup_type = {
    prefix,
    module_name: "share_adjustment",
    store_prefix: "share_adjustment",
    route_prefix: "ShareAdjustment",
    route_path: "share-adjustment",

    permission: ["admin", "super_admin"],

    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "share-adjustments",

    select_fields: [
        "id", "user_id", "adjustment_type", "from_shares", "to_shares",
        "adjustment_amount", "effective_date", "note", "slug", "created_at",
    ],

    sort_by_cols: ["id","adjustment_type","adjustment_amount","created_at"],

    table_header_data: [
        "id", "member", "adjustment_type", "from_shares", "to_shares",
        "adjustment_amount", "effective_date", "created_at",
    ],
    table_row_data: [
        "id", "member", "adjustment_type", "from_shares", "to_shares",
        "adjustment_amount", "effective_date", "created_at",
    ],
    quick_view_data: [
        "id", "member", "adjustment_type", "from_shares", "to_shares",
        "adjustment_amount", "effective_date", "note", "created_at",
    ],

    layout_title: "Share Adjustment",
    page_title: "Share Adjustment",
    all_page_title: "Adjustment History",
    details_page_title: "Adjustment Details",
    create_page_title: "New Share Adjustment",
    edit_page_title: "Edit Adjustment",
};

export default setup;
