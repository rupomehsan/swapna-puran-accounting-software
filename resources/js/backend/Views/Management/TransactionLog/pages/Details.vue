<template>
  <div>
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5>{{ setup.details_page_title }}</h5>
        <router-link class="btn btn-outline-secondary btn-sm" :to="{ name:`All${setup.route_prefix}` }">{{ setup.all_page_title }}</router-link>
      </div>
      <div class="card-body card_body_fixed_height">
        <div class="row">
          <div class="col-lg-8">
            <table class="table quick_modal_table table-bordered">
              <tbody><data-detials-table-body :item="item" /></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapWritableState } from "pinia";
import { store } from "../store";
import setup from "../setup";
import DataDetialsTableBody from "@/shared/components/all_data_page/DataDetialsTableBody.vue";

export default {
  components: { DataDetialsTableBody },
  provide() { return { moduleSetup: this.setup }; },
  data: () => ({ setup }),
  created: async function () { await this.details(this.$route.params.id); },
  methods: {
    ...mapActions(store, { details: "details" }),
  },
  computed: {
    ...mapWritableState(store, { item: "item" }),
  },
};
</script>
