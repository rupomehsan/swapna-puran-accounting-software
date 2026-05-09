<template>
  <div>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Trial Balance</h5>
        <button v-if="report" class="btn btn-outline-secondary btn-sm" @click="window.print()">
          <i class="fa fa-print mr-1"></i> Print
        </button>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <label>From Date</label>
            <input type="date" class="form-control" v-model="filters.start_date" />
          </div>
          <div class="col-md-4">
            <label>To Date</label>
            <input type="date" class="form-control" v-model="filters.end_date" />
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" @click="generate" :disabled="loading">
              <i class="fa fa-search mr-1"></i> {{ loading ? 'Loading…' : 'Generate' }}
            </button>
          </div>
        </div>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="report" id="print-area">
          <div class="text-center mb-3">
            <h5>Trial Balance</h5>
            <small class="text-muted">{{ filters.start_date || 'Beginning' }} — {{ filters.end_date || 'Today' }}</small>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead>
                <tr style="background:#1a6496; color:#fff;">
                  <th>Account Code</th>
                  <th>Account Name</th>
                  <th>Type</th>
                  <th class="text-right">Debit Balance</th>
                  <th class="text-right">Credit Balance</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in report.accounts" :key="i">
                  <td>{{ row.account_code }}</td>
                  <td>{{ row.account_name }}</td>
                  <td class="text-capitalize">{{ row.account_type }}</td>
                  <td class="text-right">{{ row.debit_balance  ? fmt(row.debit_balance)  : '' }}</td>
                  <td class="text-right">{{ row.credit_balance ? fmt(row.credit_balance) : '' }}</td>
                </tr>
                <tr v-if="!report.accounts.length">
                  <td colspan="5" class="text-center text-muted">No accounts found</td>
                </tr>
              </tbody>
              <tfoot>
                <tr style="background:#222; color:#fff;">
                  <td colspan="3"><strong>Grand Total</strong></td>
                  <td class="text-right"><strong>{{ fmt(report.grand_totals.debit) }}</strong></td>
                  <td class="text-right"><strong>{{ fmt(report.grand_totals.credit) }}</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div v-if="!balanced" class="alert alert-warning mt-2">
            <i class="fa fa-exclamation-triangle mr-1"></i>
            Trial balance does not balance — difference: {{ fmt(Math.abs(report.grand_totals.debit - report.grand_totals.credit)) }}
          </div>
          <div v-else class="alert alert-success mt-2">
            <i class="fa fa-check-circle mr-1"></i> Trial balance is balanced.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data: () => ({
    filters: { start_date: '', end_date: '' },
    report: null,
    loading: false,
    error: null,
  }),

  computed: {
    balanced() {
      if (!this.report) return true;
      return Math.abs(this.report.grand_totals.debit - this.report.grand_totals.credit) < 0.01;
    },
  },

  methods: {
    async generate() {
      this.error = null;
      this.loading = true;
      try {
        const res = await axios.get(`${location.origin}/api/v1/reports/trial-balance`, { params: this.filters });
        this.report = res.data.data;
      } catch (e) {
        this.error = e.response?.data?.message ?? 'Failed to load report.';
      } finally {
        this.loading = false;
      }
    },

    fmt(val) {
      return Number(val ?? 0).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
  },
};
</script>

<style>
@media print {
  .card-header button, .row.mb-3, .alert { display: none !important; }
  .card { border: none !important; }
}
</style>
