<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5>{{ param_id ? setup.edit_page_title : setup.create_page_title }}</h5>
          <div>
            <router-link v-if="item.slug" class="btn btn-outline-info btn-sm mr-2" :to="{ name:`Details${setup.route_prefix}`, params:{id:item.slug} }">{{ setup.details_page_title }}</router-link>
            <router-link class="btn btn-outline-warning btn-sm" :to="{ name:`All${setup.route_prefix}` }">{{ setup.all_page_title }}</router-link>
          </div>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <template v-for="(field, index) in form_fields" :key="index">
              <common-input
                :label="field.label"
                :type="field.type"
                :name="field.name"
                :placeholder="field.placeholder"
                :multiple="field.multiple"
                :value="field.value"
                :data_list="field.data_list"
                :is_visible="field.is_visible"
                :class="field.class"
              />
            </template>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-square px-5">
            <i class="icon-lock mr-2"></i> Submit
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from "pinia";
import { store } from "../store";
import setup from "../setup";
import form_fields from "../setup/form_fields";

export default {
  data: () => ({ setup, form_fields, param_id: null }),

  created: async function () {
    const id = (this.param_id = this.$route.params.id);
    this.reset_fields();
    await this.load_accounts();
    if (id) await this.set_fields(id);
  },

  methods: {
    ...mapActions(store, { create: "create", update: "update", details: "details", set_only_latest_data: "set_only_latest_data" }),

    reset_fields() { this.form_fields.forEach(f => (f.value = "")); },

    async load_accounts() {
      try {
        const res = await axios.get(`${location.origin}/api/v1/accounts`, { params: { get_all: 1, limit: 200, status: "active" } });
        const raw = res.data?.data ?? {};
        const list = Object.values(raw)
          .filter(a => a && typeof a === "object" && a.id)
          .map(a => ({ label: (a.account_code ? `[${a.account_code}] ` : "") + a.account_name, value: a.id }));
        const field = this.form_fields.find(f => f.name === "account_id");
        if (field) field.data_list = list;
      } catch (e) {
        console.warn("Could not load accounts:", e);
      }
    },

    async set_fields(id) {
      await this.details(id);
      if (this.item) {
        this.form_fields.forEach((field, index) => {
          Object.entries(this.item).forEach(([key, val]) => {
            if (field.name === key) this.form_fields[index].value = val;
          });
        });
      }
    },

    async submitHandler($event) {
      this.set_only_latest_data(true);
      if (this.param_id) {
        const response = await this.update($event);
        if ([200, 201].includes(response?.status)) {
          window.s_alert("Income entry updated successfully");
          this.$router.push({ name: `All${this.setup.route_prefix}` });
        } else {
          window.s_warning(response?.data?.message ?? "Something went wrong");
        }
      } else {
        const response = await this.create($event);
        if ([200, 201].includes(response?.status)) {
          window.s_alert("Income entry created successfully");
          this.$router.push({ name: `All${this.setup.route_prefix}` });
        } else {
          window.s_warning(response?.data?.message ?? "Something went wrong");
        }
      }
    },
  },

  computed: {
    ...mapState(store, { item: "item" }),
  },
};
</script>
