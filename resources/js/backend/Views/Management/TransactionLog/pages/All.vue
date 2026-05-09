<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-12 col-md-4 mb-md-0">
                <h5 class="text-capitalize mb-0">
                  <i class="fa fa-list-alt mr-2 text-primary"></i>
                  {{ setup.all_page_title }}
                </h5>
              </div>
              <div class="col-12 col-md-5 mb-2 mb-md-0">
                <input class="form-control" @keyup="e=>set_search_key(e)" placeholder="Search by voucher, type, amount…" />
              </div>
              <div class="col-12 col-md-3 text-md-right">
                <button class="btn btn-outline-primary btn-sm mr-2" @click="get_all" title="Reload"><i class="fa fa-refresh"></i></button>
                <button class="btn btn-outline-success btn-sm" @click="set_show_filter_canvas"><i class="fa fa-gear mx-2"></i>Filter</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive table_responsive card_body_fixed_height">
              <table class="table table-hover text-center table-bordered">
                <thead><table-head /></thead>
                <tbody v-if="all?.data?.length">
                  <table-body :data="all?.data" />
                </tbody>
                <tbody v-else>
                  <tr><td colspan="100%" class="py-5 text-muted">No transaction logs found.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="mx-3">
            <pagination :data="all" :set_page="set_page" :get_data="get_all" :set_paginate="set_paginate" />
          </div>
          <div class="card-footer py-2"><all-page-footer-actions /></div>
        </div>
      </div>
    </div>
    <export-loader />
    <quick-view />
    <filter-data />
    <import-modal />
  </div>
</template>

<script>
import { mapActions, mapWritableState } from "pinia";
import setup from "../setup";
import { store as data_store } from "../store";
import debounce from "@/shared/helpers/debounce";
import TableHead           from "@/shared/components/all_data_page/TableHead.vue";
import TableBody           from "@/shared/components/all_data_page/TableBody.vue";
import Pagination          from "@/GlobalComponents/Pagination.vue";
import AllPageFooterActions from "@/shared/components/all_data_page/AllPageFooterActions.vue";
import FilterData          from "@/shared/components/canvas/FilterData.vue";
import ImportModal         from "@/shared/components/canvas/ImportModal.vue";
import QuickView           from "@/shared/components/canvas/QuickView.vue";
import ExportLoader        from "@/shared/components/canvas/ExportLoader.vue";

export default {
  components: { TableHead, TableBody, Pagination, AllPageFooterActions, FilterData, ImportModal, QuickView, ExportLoader },
  provide() { return { moduleSetup: this.setup, dataStoreConstructor: data_store }; },
  data: () => ({ setup }),
  created: async function () { await this.get_all(); },
  methods: {
    ...mapActions(data_store, [
      "get_all","set_show_filter_canvas","set_only_latest_data","set_item",
      "set_filter_criteria","set_page","set_status","set_paginate",
      "clear_selected","import_data","bulk_action","restore","soft_delete",
      "update_status","destroy",
    ]),
    set_search_key: debounce(async function (e) {
      this.search_key = e.target.value;
      this.page = 1;
      this.only_latest_data = true;
      await this.get_all();
      this.only_latest_data = false;
    }, 500),
  },
  computed: {
    ...mapWritableState(data_store, [
      "all","show_filter_canvas","active_data_count","inactive_data_count",
      "trashed_data_count","status","selected","paginate","sort_type",
      "sort_by_cols","sort_by_col","start_date","end_date","search_key","page",
      "import_csv_modal_show",
    ]),
  },
};
</script>
