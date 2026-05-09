<template>
  <div>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Profit &amp; Loss Statement</h5>
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
            <h5>Profit &amp; Loss Statement</h5>
            <small class="text-muted">{{ filters.start_date || 'Beginning' }} — {{ filters.end_date || 'Today' }}</small>
          </div>

          <div class="row">
            <div class="col-md-6">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr style="background:#1a7e3f; color:#fff;">
                    <th colspan="2">Income</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in report.income" :key="i">
                    <td>{{ row.account_name }}</td>
                    <td class="text-right">{{ fmt(row.amount) }}</td>
                  </tr>
                  <tr v-if="!report.income.length">
                    <td colspan="2" class="text-center text-muted">No income records</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr style="background:#1a7e3f; color:#fff;">
                    <td><strong>Total Income</strong></td>
                    <td class="text-right"><strong>{{ fmt(report.total_income) }}</strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="col-md-6">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr style="background:#9b2335; color:#fff;">
                    <th colspan="2">Expenses</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in report.expenses" :key="i">
                    <td>{{ row.account_name }}</td>
                    <td class="text-right">{{ fmt(row.amount) }}</td>
                  </tr>
                  <tr v-if="!report.expenses.length">
                    <td colspan="2" class="text-center text-muted">No expense records</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr style="background:#9b2335; color:#fff;">
                    <td><strong>Total Expenses</strong></td>
                    <td class="text-right"><strong>{{ fmt(report.total_expense) }}</strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-md-6 offset-md-6">
              <table class="table table-bordered table-sm">
                <tbody>
                  <tr :style="report.is_profit ? 'background:#1a7e3f;color:#fff;' : 'background:#9b2335;color:#fff;'">
                    <td><strong>{{ report.is_profit ? 'Net Profit' : 'Net Loss' }}</strong></td>
                    <td class="text-right"><strong>{{ fmt(Math.abs(report.net_profit)) }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
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

  methods: {
    async generate() {
      this.error = null;
      this.loading = true;
      try {
        const res = await axios.get(`${location.origin}/api/v1/reports/profit-loss`, { params: this.filters });
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
  .card-header button, .row.mb-3 { display: none !important; }
  .card { border: none !important; }
}
</style>
