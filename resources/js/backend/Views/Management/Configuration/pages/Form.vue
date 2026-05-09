<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="text-capitalize mb-0">
            System Configuration
          </h5>
          <span v-if="config_loaded" class="badge badge-success px-3 py-2">
            <i class="fa fa-check-circle mr-1"></i> Configuration Loaded
          </span>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <template v-for="(form_field, index) in form_fields" v-bind:key="index">
              <common-input
                :label="form_field.label"
                :type="form_field.type"
                :name="form_field.name"
                :placeholder="form_field.placeholder"
                :multiple="form_field.multiple"
                :value="form_field.value"
                :data_list="form_field.data_list"
                :is_visible="form_field.is_visible"
                :class="form_field.class"
              />
            </template>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-square px-5">
            <i class="icon-lock mr-2"></i>
            {{ param_id ? "Update Configuration" : "Save Configuration" }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { store } from "../store";
import setup from "../setup";
import form_fields from "../setup/form_fields";

export default {
  data: () => ({
    setup,
    form_fields,
    param_id: null,
    config_loaded: false,
  }),

  created: async function () {
    let id = this.$route.params.id;
    this.reset_fields();

    if (id) {
      // Editing via URL param (e.g. /edit/:slug)
      this.param_id = id;
      await this.set_fields(id);
    } else {
      // Settings page — auto-load existing config if present
      await this.load_existing_config();
    }
  },

  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_item: "set_item",
      set_only_latest_data: "set_only_latest_data",
    }),

    reset_fields: function () {
      this.form_fields.forEach((item) => {
        item.value = "";
      });
    },

    load_existing_config: async function () {
      await this.get_all();
      const first = this.all?.data?.[0];
      if (first) {
        this.set_item(first);
        this.param_id = first.slug;
        this.form_fields.forEach((field, index) => {
          Object.entries(first).forEach(([key, val]) => {
            if (field.name === key) {
              this.form_fields[index].value = val;
            }
          });
        });
        this.config_loaded = true;
      }
    },

    set_fields: async function (id) {
      await this.details(id);
      if (this.item) {
        this.form_fields.forEach((field, index) => {
          Object.entries(this.item).forEach(([key, val]) => {
            if (field.name === key) {
              this.form_fields[index].value = val;
            }
          });
        });
        this.config_loaded = true;
      }
    },

    submitHandler: async function ($event) {
      this.set_only_latest_data(true);
      if (this.param_id) {
        let response = await this.update($event);
        if ([200, 201].includes(response?.status)) {
          window.s_alert("Configuration updated successfully");
          await this.load_existing_config();
        }
      } else {
        let response = await this.create($event);
        if ([200, 201].includes(response?.status)) {
          window.s_alert("Configuration saved successfully");
          await this.load_existing_config();
        }
      }
    },
  },

  computed: {
    ...mapState(store, {
      item: "item",
      all: "all",
    }),
  },
};
</script>

<style scoped></style>
