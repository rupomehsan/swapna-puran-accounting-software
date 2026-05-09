<template>
  <div>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Balance Sheet</h5>
        <button v-if="report" class="btn btn-outline-secondary btn-sm" @click="window.print()">
          <i class="fa fa-print mr-1"></i> Print
        </button>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <label>As of Date</label>
            <input type="date" class="form-control" v-model="filters.as_of_date" />
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary" @click="generate" :disabled="loading">
              <i class="fa fa-search mr-1"></i> {{ loading ? 'Loading…' : 'Generate' }}
            </button>
          </div>
        </div>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="report" id="print-area">
          <div class="text-center mb-3">
            <h5>Balance Sheet</h5>
            <small class="text-muted">As of {{ report.as_of_date }}</small>
          </div>

          <div class="row">
            <!-- Assets -->
            <div class="col-md-6">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr style="background:#1a6496; color:#fff;">
                    <th colspan="2">Assets</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in report.assets" :key="i">
                    <td>{{ row.account_name }}</td>
                    <td class="text-right">{{ fmt(row.balance) }}</td>
                  </tr>
                  <tr v-if="!report.assets.length">
                    <td colspan="2" class="text-center text-muted">No asset accounts</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr style="background:#1a6496; color:#fff;">
                    <td><strong>Total Assets</strong></td>
                    <td class="text-right"><strong>{{ fmt(report.total_assets) }}</strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- Liabilities + Equity -->
            <div class="col-md-6">
              <table class="table table-bordered table-sm mb-2">
                <thead>
                  <tr style="background:#8a5200; color:#fff;">
                    <th colspan="2">Liabilities</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in report.liabilities" :key="i">
                    <td>{{ row.account_name }}</td>
                    <td class="text-right">{{ fmt(row.balance) }}</td>
                  </tr>
                  <tr v-if="!report.liabilities.length">
                    <td colspan="2" class="text-center text-muted">No liability accounts</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr style="background:#8a5200; color:#fff;">
                    <td><strong>Total Liabilities</strong></td>
                    <td class="text-right"><strong>{{ fmt(report.total_liabilities) }}</strong></td>
                  </tr>
                </tfoot>
              </table>

              <table class="table table-bordered table-sm mb-2">
                <thead>
                  <tr style="background:#1a6a6a; color:#fff;">
                    <th colspan="2">Equity</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in report.equity" :key="i">
                    <td>{{ row.account_name }}</td>
                    <td class="text-right">{{ fmt(row.balance) }}</td>
                  </tr>
                  <tr>
                    <td>Retained Earnings (Net P&amp;L)</td>
                    <td class="text-right" :style="report.retained_earnings >= 0 ? 'color:#1a7e3f;font-weight:bold;' : 'color:#9b2335;font-weight:bold;'">
                      {{ fmt(report.retained_earnings) }}
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr style="background:#1a6a6a; color:#fff;">
                    <td><strong>Total Equity</strong></td>
                    <td class="text-right"><strong>{{ fmt(report.total_equity) }}</strong></td>
                  </tr>
                </tfoot>
              </table>

              <table class="table table-bordered table-sm">
                <tbody>
                  <tr >
                    <td><strong>Total Liabilities + Equity</strong></td>
                    <td class="text-right"><strong>{{ fmt(report.total_liabilities + report.total_equity) }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="!balanced" class="alert alert-warning">
            <i class="fa fa-exclamation-triangle mr-1"></i>
            Balance sheet does not balance — difference: {{ fmt(Math.abs(report.total_assets - (report.total_liabilities + report.total_equity))) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      filters: { as_of_date: new Date().toISOString().split('T')[0] },
      report: null,
      loading: false,
      error: null,
    };
  },

  computed: {
    balanced() {
      if (!this.report) return true;
      const diff = this.report.total_assets - (this.report.total_liabilities + this.report.total_equity);
      return Math.abs(diff) < 0.01;
    },
  },

  methods: {
    async generate() {
      this.error = null;
      this.loading = true;
      try {
        const res = await axios.get(`${location.origin}/api/v1/reports/balance-sheet`, { params: this.filters });
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
