<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5>New Share Adjustment</h5>
          <router-link
            :to="{ name: 'AllShareAdjustment' }"
            class="btn btn-outline-warning btn-sm"
          >Adjustment History</router-link>
        </div>

        <div class="card-body">
          <div class="row">

            <!-- Member -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Select Member</label>
                <select-input
                  name="user_id"
                  :value="form.user_id"
                  :data_list="memberList"
                  @change="onMemberChange"
                />
              </div>
            </div>

            <!-- Adjustment Type -->
            <div class="col-md-3">
              <div class="form-group">
                <label>Adjustment Type</label>
                <select-input
                  name="adjustment_type"
                  :value="form.adjustment_type"
                  :data_list="[
                    { label: 'Increase (বাড়ানো)', value: 'increase' },
                    { label: 'Decrease (কমানো)', value: 'decrease' },
                  ]"
                  @change="onTypeChange"
                />
              </div>
            </div>

            <!-- Count -->
            <div class="col-md-3">
              <div class="form-group">
                <label>Number of Shares to Adjust</label>
                <input
                  type="number"
                  min="1"
                  v-model.number="form.count"
                  class="form-control"
                  @input="debouncedPreview"
                />
              </div>
            </div>
          </div>

          <!-- ─── Preview Card ─── -->
          <div v-if="preview" class="preview-card mt-3">
            <div class="preview-card__title">
              <i class="fa fa-calculator"></i> Calculation Preview
            </div>
            <div class="preview-grid">
              <div class="pgrid-row">
                <span>Member</span>
                <strong>{{ preview.user_name }}</strong>
              </div>
              <div class="pgrid-row">
                <span>Current Shares</span>
                <strong>{{ preview.current_shares }}</strong>
              </div>
              <div class="pgrid-row">
                <span>New Shares</span>
                <strong class="text-primary">{{ preview.new_shares }}</strong>
              </div>
              <div class="pgrid-row">
                <span>Months Elapsed</span>
                <strong>{{ preview.months_elapsed }}</strong>
              </div>
              <div class="pgrid-row">
                <span>Share Price (per month)</span>
                <strong>৳ {{ fmt(preview.share_price) }}</strong>
              </div>
              <div class="pgrid-row">
                <span>Expected at Old Rate</span>
                <strong>৳ {{ fmt(preview.expected_old) }}</strong>
              </div>
              <div class="pgrid-row">
                <span>Expected at New Rate</span>
                <strong>৳ {{ fmt(preview.expected_new) }}</strong>
              </div>
              <div class="pgrid-row">
                <span>Already Paid (Share Pool)</span>
                <strong>৳ {{ fmt(preview.paid_so_far) }}</strong>
              </div>
              <div class="pgrid-row pgrid-row--highlight">
                <span>{{ preview.direction_label }}</span>
                <strong class="amt-big" :class="preview.adjustment_type === 'increase' ? 'amt-up' : 'amt-down'">
                  ৳ {{ fmt(preview.adjustment_amount) }}
                </strong>
              </div>
            </div>
          </div>

          <!-- Conditional fields -->
          <div class="row mt-3">
            <div v-if="form.adjustment_type === 'increase'" class="col-md-6">
              <div class="form-group">
                <label>Payment Method</label>
                <select-input
                  name="payment_method"
                  :value="form.payment_method"
                  :data_list="[
                    { label: 'Cash (নগদ)', value: 'cash' },
                    { label: 'Bank (ব্যাংক)', value: 'bank' },
                    { label: 'Mobile Banking', value: 'mobile_banking' },
                  ]"
                  @change="form.payment_method = $event"
                />
              </div>
            </div>

            <div v-if="form.adjustment_type === 'decrease'" class="col-md-6">
              <div class="form-group">
                <label>Refund Destination</label>
                <select-input
                  name="refund_destination"
                  :value="form.refund_destination"
                  :data_list="[
                    { label: 'Withdraw (Cash Refund)', value: 'withdrawal' },
                    { label: 'Transfer to Extra Savings', value: 'extra_savings' },
                  ]"
                  @change="form.refund_destination = $event"
                />
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Effective Date</label>
                <input
                  type="date"
                  v-model="form.effective_date"
                  class="form-control"
                />
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Note (optional)</label>
                <textarea
                  v-model="form.note"
                  rows="2"
                  class="form-control"
                  placeholder="Why this adjustment?"
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
          <button
            type="submit"
            class="btn btn-primary btn-square px-5"
            :disabled="!canSubmit || submitting"
          >
            <i class="icon-lock mr-2"></i>
            {{ submitting ? "Processing..." : "Apply Adjustment" }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import SelectInput from "../../../../GlobalComponents/FormComponents/SelectInput.vue";

export default {
  components: { SelectInput },
  data: () => ({
    memberList: [],
    form: {
      user_id: "",
      adjustment_type: "increase",
      count: 1,
      payment_method: "cash",
      refund_destination: "withdrawal",
      effective_date: new Date().toISOString().slice(0, 10),
      note: "",
    },
    preview: null,
    submitting: false,
    _debounceTimer: null,
  }),

  computed: {
    canSubmit() {
      return (
        this.form.user_id &&
        this.form.adjustment_type &&
        this.form.count >= 1 &&
        this.preview &&
        this.preview.adjustment_amount >= 0
      );
    },
  },

  async created() {
    await this.loadMembers();
  },

  methods: {
    fmt(n) {
      return Number(n || 0).toLocaleString("en-BD", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    async loadMembers() {
      try {
        const res = await axios.get(`${location.origin}/api/v1/users`, {
          params: { get_all: 1, limit: 500, status: "active" },
        });
        const raw = res.data?.data ?? {};
        const users = Object.values(raw).filter(
          (u) => u && typeof u === "object" && u.id && Number(u.role_id) === 2
        );
        this.memberList = users.map((u) => ({
          label: `${u.name} — ${u.number_of_share ?? 0} share${(u.phone ? ' • ' + u.phone : '')}`,
          value: u.id,
        }));
      } catch (e) {
        console.warn("Could not load members:", e);
      }
    },

    onMemberChange(value) {
      this.form.user_id = value;
      this.debouncedPreview();
    },

    onTypeChange(value) {
      this.form.adjustment_type = value;
      this.debouncedPreview();
    },

    debouncedPreview() {
      clearTimeout(this._debounceTimer);
      this._debounceTimer = setTimeout(() => this.fetchPreview(), 250);
    },

    async fetchPreview() {
      if (!this.form.user_id || !this.form.adjustment_type || this.form.count < 1) {
        this.preview = null;
        return;
      }
      try {
        const res = await axios.get(
          `${location.origin}/api/v1/share-adjustments/preview`,
          {
            params: {
              user_id: this.form.user_id,
              adjustment_type: this.form.adjustment_type,
              count: this.form.count,
            },
          }
        );
        this.preview = res.data?.data ?? null;
      } catch (e) {
        this.preview = null;
        const msg = e.response?.data?.message ?? "Failed to calculate";
        window.s_warning?.(msg);
      }
    },

    async submitHandler() {
      const confirmMsg =
        this.preview.adjustment_type === "increase"
          ? `Member ৳${this.fmt(this.preview.adjustment_amount)} পে করবে। Apply করতে চান?`
          : `Member ৳${this.fmt(this.preview.adjustment_amount)} ফেরত পাবে। Apply করতে চান?`;
      const con = await window.s_confirm?.(confirmMsg);
      if (!con) return;

      this.submitting = true;
      try {
        const res = await axios.post(
          `${location.origin}/api/v1/share-adjustments/store`,
          {
            user_id: this.form.user_id,
            adjustment_type: this.form.adjustment_type,
            count: this.form.count,
            payment_method: this.form.payment_method,
            refund_destination: this.form.refund_destination,
            effective_date: this.form.effective_date,
            note: this.form.note,
          }
        );
        if ([200, 201].includes(res.status)) {
          window.s_alert?.(res.data?.message ?? "Adjustment applied");
          this.$router.push({ name: "AllShareAdjustment" });
        } else {
          window.s_warning?.(res.data?.message ?? "Something went wrong");
        }
      } catch (e) {
        const msg = e.response?.data?.message ?? "Failed";
        window.s_warning?.(msg);
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>

<style scoped>
.preview-card {
  border: 1px solid var(--border-color, rgba(0,0,0,.08));
  border-radius: 8px;
  background: var(--bg-card, rgba(99,102,241,0.04));
  padding: 16px 18px;
}
.preview-card__title {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--primary-color, #3b82f6);
}
.preview-card__title i { margin-right: 6px; }

.preview-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 6px 24px;
}
.pgrid-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 6px 0;
  border-bottom: 1px dashed rgba(120,120,140,0.18);
  font-size: 13px;
}
.pgrid-row span { color: var(--text-secondary, #6b7280); }
.pgrid-row strong { color: var(--text-primary, #111827); }
.pgrid-row--highlight {
  grid-column: 1 / -1;
  border-bottom: none;
  border-top: 2px solid rgba(99,102,241,0.4);
  padding-top: 12px;
  margin-top: 6px;
}
.pgrid-row--highlight span { font-size: 14px; font-weight: 600; }
.amt-big { font-size: 22px !important; font-weight: 800 !important; }
.amt-up   { color: #dc2626 !important; }
.amt-down { color: #16a34a !important; }
</style>
