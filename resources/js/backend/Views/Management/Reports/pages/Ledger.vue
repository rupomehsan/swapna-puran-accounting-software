<template>
  <div>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Ledger Report</h5>
        <button v-if="report" class="btn btn-outline-secondary btn-sm" @click="window.print()">
          <i class="fa fa-print mr-1"></i> Print
        </button>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <label>Account</label>
            <select class="form-control" v-model="filters.account_id">
              <option value="">-- Select Account --</option>
              <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                {{ acc.account_code ? '[' + acc.account_code + '] ' : '' }}{{ acc.account_name }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label>From Date</label>
            <input type="date" class="form-control" v-model="filters.start_date" />
          </div>
          <div class="col-md-3">
            <label>To Date</label>
            <input type="date" class="form-control" v-model="filters.end_date" />
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" @click="generate" :disabled="loading">
              <i class="fa fa-search mr-1"></i> {{ loading ? 'Loading…' : 'Generate' }}
            </button>
          </div>
        </div>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="report" id="print-area">
          <div class="text-center mb-3">
            <h5 class="mb-0">Ledger Account</h5>
            <strong>
              {{ report.account.account_code ? '[' + report.account.account_code + '] ' : '' }}{{ report.account.account_name }}
            </strong><br>
            <small class="text-muted">
              {{ filters.start_date || 'Beginning' }} — {{ filters.end_date || 'Today' }}
            </small>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead class="thead-light">
                <tr>
                  <th>Date</th>
                  <th>Voucher No</th>
                  <th>Description</th>
                  <th class="text-right">Debit</th>
                  <th class="text-right">Credit</th>
                  <th class="text-right">Balance</th>
                </tr>
              </thead>
              <tbody>
                <tr style="background:#1a6496; color:#fff;">
                  <td colspan="5"><strong>Opening Balance</strong></td>
                  <td class="text-right"><strong>{{ fmt(report.opening_balance) }}</strong></td>
                </tr>
                <tr v-for="(row, i) in report.entries" :key="i">
                  <td>{{ row.date }}</td>
                  <td>{{ row.voucher_no }}</td>
                  <td>{{ stripHtml(row.description) }}</td>
                  <td class="text-right">{{ row.debit  ? fmt(row.debit)  : '' }}</td>
                  <td class="text-right">{{ row.credit ? fmt(row.credit) : '' }}</td>
                  <td class="text-right">{{ fmt(row.balance) }}</td>
                </tr>
                <tr v-if="!report.entries.length">
                  <td colspan="6" class="text-center text-muted">No transactions in this period</td>
                </tr>
              </tbody>
              <tfoot>
                <tr style="background:#222; color:#fff;">
                  <td colspan="3"><strong>Total / Closing Balance</strong></td>
                  <td class="text-right"><strong>{{ fmt(report.totals.debit) }}</strong></td>
                  <td class="text-right"><strong>{{ fmt(report.totals.credit) }}</strong></td>
                  <td class="text-right"><strong>{{ fmt(report.totals.closing_balance) }}</strong></td>
                </tr>
              </tfoot>
            </table>
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
    accounts: [],
    filters: { account_id: '', start_date: '', end_date: '' },
    report: null,
    loading: false,
    error: null,
  }),

  async created() {
    try {
      const res = await axios.get(`${location.origin}/api/v1/accounts`, {
        params: { get_all: 1, limit: 500, status: 'active' },
      });
      const raw = res.data?.data ?? {};
      this.accounts = Object.values(raw).filter(a => a && a.id);
    } catch (e) {
      console.warn('Could not load accounts', e);
    }
  },

  methods: {
    async generate() {
      if (!this.filters.account_id) { this.error = 'Please select an account.'; return; }
      this.error = null;
      this.loading = true;
      try {
        const res = await axios.get(`${location.origin}/api/v1/reports/ledger`, { params: this.filters });
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

    stripHtml(html) {
      if (!html) return '';
      return html.replace(/<[^>]*>/g, '').trim();
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
