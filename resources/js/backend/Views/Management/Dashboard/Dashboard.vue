<template>
  <div class="dashboard-container" :class="{ 'dark-theme': isDarkMode }">

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-2 text-muted">Loading dashboard…</p>
    </div>

    <template v-else>

      <!-- ── Row 1: Primary KPIs ─────────────────────────────────────── -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#667eea,#764ba2)">
              <i class="fa fa-users"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ s.total_members ?? 0 }}</h3>
              <p class="stat-label">Active Members</p>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
              <i class="fa fa-arrow-circle-down"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(s.total_deposits) }}</h3>
              <p class="stat-label">Total Deposits</p>
              <span class="stat-change positive"><i class="fa fa-arrow-up"></i> All time</span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
              <i class="fa fa-arrow-circle-up"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(s.total_withdrawals) }}</h3>
              <p class="stat-label">Total Withdrawals</p>
              <span class="stat-change negative"><i class="fa fa-arrow-down"></i> All time</span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" :style="(s.net_fund??0)>=0 ? 'background:linear-gradient(135deg,#30cfd0,#330867)' : 'background:linear-gradient(135deg,#f5576c,#f093fb)'">
              <i class="fa fa-university"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(s.net_fund) }}</h3>
              <p class="stat-label">Net Fund Balance</p>
              <span class="stat-change" :class="(s.net_fund??0)>=0?'positive':'negative'">
                <i :class="(s.net_fund??0)>=0?'fa fa-arrow-up':'fa fa-arrow-down'"></i>
                {{ (s.net_fund??0)>=0 ? 'Surplus' : 'Deficit' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Row 2: Secondary KPIs ──────────────────────────────────── -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#fa709a,#fee140)">
              <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(s.total_due_unpaid) }}</h3>
              <p class="stat-label">Unpaid Dues</p>
              <span class="stat-change negative"><i class="fa fa-clock-o"></i> Pending</span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">
              <i class="fa fa-plus-square"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(s.total_income) }}</h3>
              <p class="stat-label">Total Income</p>
              <span class="stat-change positive"><i class="fa fa-arrow-up"></i> Revenue</span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#f77062,#fe5196)">
              <i class="fa fa-minus-square"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(s.total_expense) }}</h3>
              <p class="stat-label">Total Expense</p>
              <span class="stat-change negative"><i class="fa fa-arrow-down"></i> Expenditure</span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card">
            <div class="stat-icon" :style="(s.net_profit_loss??0)>=0 ? 'background:linear-gradient(135deg,#0ba360,#3cba92)' : 'background:linear-gradient(135deg,#f5576c,#f093fb)'">
              <i class="fa fa-line-chart"></i>
            </div>
            <div class="stat-content">
              <h3 class="stat-value">{{ fmt(Math.abs(s.net_profit_loss??0)) }}</h3>
              <p class="stat-label">{{ (s.net_profit_loss??0)>=0 ? 'Net Profit' : 'Net Loss' }}</p>
              <span class="stat-change" :class="(s.net_profit_loss??0)>=0?'positive':'negative'">
                <i :class="(s.net_profit_loss??0)>=0?'fa fa-arrow-up':'fa fa-arrow-down'"></i>
                {{ (s.net_profit_loss??0)>=0 ? 'Profit' : 'Loss' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Charts ─────────────────────────────────────────────────── -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
          <div class="dashboard-card">
            <div class="card-header">
              <h5 class="card-title">Monthly Financial Trend (Last 6 Months)</h5>
              <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot" style="background:#3B82F6"></span> Deposits</span>
                <span class="legend-item"><span class="legend-dot" style="background:#EF4444"></span> Withdrawals</span>
                <span class="legend-item"><span class="legend-dot" style="background:#10B981"></span> Income</span>
                <span class="legend-item"><span class="legend-dot" style="background:#F59E0B"></span> Expense</span>
              </div>
            </div>
            <div class="card-body">
              <canvas ref="trendChart" style="max-height:300px"></canvas>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="dashboard-card">
            <div class="card-header">
              <h5 class="card-title">Fund Breakdown</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height:240px">
              <canvas ref="breakdownChart" style="width:220px;height:220px"></canvas>
            </div>
            <div class="card-footer">
              <div class="user-stats">
                <div class="stat-item"><span class="stat-dot" style="background:#3B82F6"></span><span>Deposits: {{ fmt(s.total_deposits) }}</span></div>
                <div class="stat-item"><span class="stat-dot" style="background:#EF4444"></span><span>Withdrawals: {{ fmt(s.total_withdrawals) }}</span></div>
                <div class="stat-item"><span class="stat-dot" style="background:#10B981"></span><span>Income: {{ fmt(s.total_income) }}</span></div>
                <div class="stat-item"><span class="stat-dot" style="background:#F59E0B"></span><span>Expense: {{ fmt(s.total_expense) }}</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Bottom Tables ──────────────────────────────────────────── -->
      <div class="row g-4">
        <!-- Recent Transactions -->
        <div class="col-12 col-lg-8">
          <div class="dashboard-card">
            <div class="card-header">
              <h5 class="card-title">Recent Transactions</h5>
              <router-link :to="{ name: 'AllTransactionLog' }" class="btn btn-sm btn-outline-primary">View All</router-link>
            </div>
            <div class="table-responsive">
              <table class="sellers-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Voucher</th>
                    <th>Type</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Dir.</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(tx, i) in s.recent_transactions" :key="i">
                    <td>{{ (tx.transaction_date||'').split(' ')[0] || '—' }}</td>
                    <td><small class="text-muted">{{ tx.voucher_no }}</small></td>
                    <td><span class="tx-badge" :class="'tx-' + (tx.transaction_type||'').replace('_','-')">{{ txLabel(tx.transaction_type) }}</span></td>
                    <td>
                      <span v-if="tx.member_name">{{ tx.member_name }}</span>
                      <span v-else class="text-muted">Org</span>
                    </td>
                    <td><strong>{{ fmt(tx.amount) }}</strong></td>
                    <td>
                      <span :style="tx.direction==='credit'?'color:#10B981;font-weight:600':'color:#EF4444;font-weight:600'">
                        <i :class="tx.direction==='credit'?'fa fa-arrow-up':'fa fa-arrow-down'"></i>
                        {{ tx.direction }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="!s.recent_transactions?.length">
                    <td colspan="6" class="text-center text-muted py-3">No transactions yet</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Top Depositors -->
        <div class="col-12 col-lg-4">
          <div class="dashboard-card">
            <div class="card-header">
              <h5 class="card-title">Top Depositors</h5>
            </div>
            <div class="card-body p-0">
              <div v-for="(dep, i) in s.top_depositors" :key="i" class="depositor-row">
                <div class="depositor-rank">{{ i + 1 }}</div>
                <div class="depositor-info">
                  <strong>{{ dep.name }}</strong>
                  <small class="text-muted d-block">{{ dep.deposit_count }} deposit(s)</small>
                </div>
                <div class="depositor-amount">{{ fmt(dep.total_deposit) }}</div>
              </div>
              <div v-if="!s.top_depositors?.length" class="p-3 text-center text-muted">No deposit data</div>
            </div>
          </div>
        </div>
      </div>

    </template>
  </div>
</template>

<script>
import axios from 'axios';
import Chart from 'chart.js/auto';

export default {
  name: 'Dashboard',
  data() {
    return {
      s: {},
      loading: true,
      isDarkMode: false,
      trendChart: null,
      breakdownChart: null,
    };
  },

  mounted() {
    if (window.themeManager) {
      this.isDarkMode = window.themeManager.isDarkTheme();
      window.addEventListener('themechange', (e) => {
        this.isDarkMode = e.detail.theme === 'dark-theme';
        this.buildCharts();
      });
    }
    this.loadData();
  },

  beforeUnmount() {
    this.destroyCharts();
  },

  methods: {
    async loadData() {
      try {
        const res = await axios.get(`${location.origin}/api/v1/get-all-dashboard-data`);
        this.s = res.data?.data ?? {};
      } catch (e) {
        console.error('Dashboard load error:', e);
      } finally {
        this.loading = false;
        this.$nextTick(() => this.buildCharts());
      }
    },

    buildCharts() {
      this.destroyCharts();
      this.buildTrendChart();
      this.buildBreakdownChart();
    },

    destroyCharts() {
      if (this.trendChart)     { this.trendChart.destroy();     this.trendChart = null; }
      if (this.breakdownChart) { this.breakdownChart.destroy(); this.breakdownChart = null; }
    },

    buildTrendChart() {
      const ctx = this.$refs.trendChart;
      if (!ctx) return;
      const dark = this.isDarkMode;
      const tc   = dark ? '#d1d5db' : '#6b7280';
      const gc   = dark ? 'rgba(75,85,99,0.2)' : 'rgba(0,0,0,0.06)';

      this.trendChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: this.s.chart_labels || [],
          datasets: [
            { label: 'Deposits',     data: this.s.chart_deposits    || [], borderColor: '#3B82F6', backgroundColor: 'rgba(59,130,246,0.08)',  borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#3B82F6' },
            { label: 'Withdrawals',  data: this.s.chart_withdrawals || [], borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,0.08)',   borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#EF4444' },
            { label: 'Income',       data: this.s.chart_income      || [], borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,0.08)',  borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#10B981' },
            { label: 'Expense',      data: this.s.chart_expense     || [], borderColor: '#F59E0B', backgroundColor: 'rgba(245,158,11,0.08)',  borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#F59E0B' },
          ],
        },
        options: {
          responsive: true, maintainAspectRatio: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, ticks: { color: tc, callback: v => this.fmt(v) }, grid: { color: gc } },
            x: { ticks: { color: tc }, grid: { display: false } },
          },
        },
      });
    },

    buildBreakdownChart() {
      const ctx = this.$refs.breakdownChart;
      if (!ctx) return;
      const dark = this.isDarkMode;

      this.breakdownChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Deposits', 'Withdrawals', 'Income', 'Expense'],
          datasets: [{
            data: [
              this.s.total_deposits    || 0,
              this.s.total_withdrawals || 0,
              this.s.total_income      || 0,
              this.s.total_expense     || 0,
            ],
            backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B'],
            borderColor: dark ? '#2d3748' : '#fff',
            borderWidth: 3,
          }],
        },
        options: {
          responsive: true, maintainAspectRatio: true,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${this.fmt(ctx.raw)}` } },
          },
          cutout: '65%',
        },
      });
    },

    fmt(val) {
      return Number(val ?? 0).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },

    txLabel(type) {
      const map = { share_deposit: 'Deposit', extra_savings: 'Savings', withdrawal: 'Withdrawal', income: 'Income', expense: 'Expense', due_created: 'Due', due_collection: 'Due Coll.' };
      return map[type] || type;
    },
  },
};
</script>

<style scoped>
.dashboard-container { background-color: #f9fafb; padding: 0; transition: background-color .3s; }
.dark-theme          { background-color: #0f172a; }

/* KPI Cards */
.stat-card {
  background: white; border-radius: 12px; padding: 20px;
  display: flex; align-items: center; gap: 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,.1); border: 1px solid #e5e7eb;
  transition: all .3s; cursor: default;
}
.dark-theme .stat-card { background: #2d3748; border-color: #4a5568; }
.stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.12); transform: translateY(-3px); }

.stat-icon {
  width: 60px; height: 60px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; color: white; flex-shrink: 0;
}
.stat-content { flex: 1; }
.stat-value   { font-size: 22px; font-weight: 700; color: #1f2937; margin: 0 0 2px; }
.stat-label   { font-size: 13px; color: #6b7280; margin: 0; }
.dark-theme .stat-value { color: #f3f4f6; }
.dark-theme .stat-label { color: #9ca3af; }
.stat-change  { display: inline-block; font-size: 12px; font-weight: 600; margin-top: 6px; }
.stat-change.positive { color: #10b981; }
.stat-change.negative { color: #ef4444; }

/* Dashboard cards */
.dashboard-card {
  background: white; border-radius: 12px; border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0,0,0,.1); display: flex; flex-direction: column; height: 100%;
}
.dark-theme .dashboard-card { background: #2d3748; border-color: #4a5568; }

.card-header {
  padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
  display: flex; justify-content: space-between; align-items: center;
}
.dark-theme .card-header { border-bottom-color: #4a5568; }
.card-title  { font-size: 15px; font-weight: 700; color: #1f2937; margin: 0; }
.dark-theme .card-title { color: #f3f4f6; }
.card-body   { padding: 20px; flex: 1; }
.card-footer { padding: 16px 20px; border-top: 1px solid #f3f4f6; }
.dark-theme .card-footer { border-top-color: #4a5568; }

/* Chart legend */
.chart-legend { display: flex; gap: 12px; flex-wrap: wrap; }
.legend-item  { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #6b7280; }
.legend-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* User stats (fund breakdown legend) */
.user-stats { display: flex; flex-direction: column; gap: 10px; }
.stat-item  { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; }
.stat-dot   { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Transaction type badge */
.tx-badge {
  display: inline-block; padding: 2px 8px; border-radius: 20px;
  font-size: 11px; font-weight: 600; white-space: nowrap;
  background: #e5e7eb; color: #374151;
}
.tx-share-deposit, .tx-extra-savings { background: #dbeafe; color: #1d4ed8; }
.tx-withdrawal   { background: #fee2e2; color: #b91c1c; }
.tx-income       { background: #d1fae5; color: #065f46; }
.tx-expense      { background: #fef3c7; color: #92400e; }
.tx-due, .tx-due-collection { background: #ede9fe; color: #5b21b6; }

/* Recent transactions table */
.table-responsive { overflow-x: auto; }
.sellers-table { width: 100%; border-collapse: collapse; }
.sellers-table thead { background: #f9fafb; }
.dark-theme .sellers-table thead { background: #374151; }
.sellers-table th { padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
.dark-theme .sellers-table th { color: #9ca3af; }
.sellers-table td { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #374151; }
.dark-theme .sellers-table td { border-bottom-color: #4a5568; color: #d1d5db; }
.sellers-table tbody tr:hover { background: #f9fafb; }
.dark-theme .sellers-table tbody tr:hover { background: #374151; }

/* Top depositors */
.depositor-row {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 16px; border-bottom: 1px solid #f3f4f6;
  transition: background .2s;
}
.depositor-row:hover { background: #f9fafb; }
.dark-theme .depositor-row { border-bottom-color: #4a5568; }
.dark-theme .depositor-row:hover { background: #374151; }
.depositor-rank {
  width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.depositor-info { flex: 1; }
.depositor-info strong { font-size: 13px; color: #1f2937; }
.dark-theme .depositor-info strong { color: #f3f4f6; }
.depositor-amount { font-size: 13px; font-weight: 700; color: #3b82f6; white-space: nowrap; }

@media (max-width: 768px) {
  .stat-card { padding: 14px; }
  .card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
  .chart-legend { flex-direction: column; }
}
</style>
