<template>
  <div class="page-root">
    <Head title="Balance Sheet" />

    <section class="page-hero">
      <div class="orb orb--1"></div>
      <div class="orb orb--2"></div>
      <div class="hero-body">
        <Link href="/" class="back-link"><i class="fas fa-chevron-left"></i> Back to Home</Link>
        <div class="hero-icon"><i class="fas fa-file-invoice-dollar"></i></div>
        <h1 class="hero-title">Balance Sheet</h1>
        <p class="hero-sub">Complete financial overview of the organization</p>
      </div>
    </section>

    <!-- Page navigation tabs -->
    <div class="page-nav">
      <div class="page-nav__inner">
        <Link href="/transaction-log" class="pnav">
          <i class="fas fa-history"></i><span>Transaction Log</span>
        </Link>
        <Link href="/income" class="pnav">
          <i class="fas fa-hand-holding-usd"></i><span>Income</span>
        </Link>
        <Link href="/expense" class="pnav">
          <i class="fas fa-receipt"></i><span>Expense</span>
        </Link>
        <Link href="/balance-sheet" class="pnav pnav--active">
          <i class="fas fa-file-invoice-dollar"></i><span>Balance Sheet</span>
        </Link>
      </div>
    </div>

    <div class="content-wrap">
      <div v-if="loading" class="skel-wrap">
        <div class="skel-cards">
          <div class="skel-card" v-for="n in 6" :key="n"></div>
        </div>
        <div class="skel-banner"></div>
        <div class="skel-head mt16"></div>
        <div class="skel-row" v-for="n in 4" :key="'r'+n" :style="{opacity:1-n*0.15}"></div>
      </div>

      <template v-else>
        <div v-if="apiError" class="demo-banner">
          <i class="fas fa-flask"></i> Sample data shown — live data will appear once the API has records.
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
          <div class="kpi kpi--blue">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-body">
              <span class="kpi-label">Total Members</span>
              <span class="kpi-val">{{ summary.total_members }}</span>
            </div>
          </div>
          <div class="kpi kpi--green">
            <div class="kpi-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="kpi-body">
              <span class="kpi-label">Total Deposits</span>
              <span class="kpi-val">৳{{ fmt(summary.total_deposits) }}</span>
            </div>
          </div>
          <div class="kpi kpi--orange">
            <div class="kpi-icon"><i class="fas fa-minus-circle"></i></div>
            <div class="kpi-body">
              <span class="kpi-label">Total Withdrawals</span>
              <span class="kpi-val">৳{{ fmt(summary.total_withdrawals) }}</span>
            </div>
          </div>
          <div class="kpi kpi--teal">
            <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
            <div class="kpi-body">
              <span class="kpi-label">Net Savings</span>
              <span class="kpi-val">৳{{ fmt(summary.net_savings) }}</span>
            </div>
          </div>
          <div class="kpi kpi--indigo">
            <div class="kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="kpi-body">
              <span class="kpi-label">Total Income</span>
              <span class="kpi-val">৳{{ fmt(summary.total_income) }}</span>
            </div>
          </div>
          <div class="kpi kpi--red">
            <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
            <div class="kpi-body">
              <span class="kpi-label">Total Expense</span>
              <span class="kpi-val">৳{{ fmt(summary.total_expense) }}</span>
            </div>
          </div>
        </div>

        <!-- Net Balance -->
        <div :class="['net-banner', summary.net_balance>=0?'net-pos':'net-neg']">
          <div class="net-left">
            <div class="net-icon"><i class="fas fa-chart-line"></i></div>
            <div>
              <div class="net-title">Net Balance</div>
              <div class="net-formula">Net Savings + Income − Expense</div>
            </div>
          </div>
          <div class="net-value">৳{{ fmt(summary.net_balance) }}</div>
        </div>

        <!-- Two column tables -->
        <div class="two-col">
          <!-- Deposit Breakdown -->
          <div>
            <div class="sec-head"><i class="fas fa-piggy-bank"></i> Deposit Breakdown</div>
            <div class="tbl-card">
              <table>
                <thead><tr>
                  <th>Category</th><th class="th-r">Amount</th><th class="th-r">%</th>
                </tr></thead>
                <tbody>
                  <tr class="row-share">
                    <td><span class="dot dot--indigo"></span> Share Deposits</td>
                    <td class="td-amt amt-g">৳{{ fmt(summary.share_deposits) }}</td>
                    <td class="td-pct">{{ pct(summary.share_deposits, summary.total_deposits) }}%</td>
                  </tr>
                  <tr class="row-save">
                    <td><span class="dot dot--teal"></span> Extra Savings</td>
                    <td class="td-amt amt-g">৳{{ fmt(summary.savings_deposits) }}</td>
                    <td class="td-pct">{{ pct(summary.savings_deposits, summary.total_deposits) }}%</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="foot-row">
                    <td class="foot-label">Total Deposits</td>
                    <td class="foot-val">৳{{ fmt(summary.total_deposits) }}</td>
                    <td class="foot-pct">100%</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- P&L Summary -->
          <div>
            <div class="sec-head"><i class="fas fa-balance-scale"></i> P&amp;L Summary</div>
            <div class="tbl-card">
              <table>
                <thead><tr>
                  <th>Item</th><th class="th-r">Amount</th>
                </tr></thead>
                <tbody>
                  <tr>
                    <td><span class="dot dot--green"></span> Total Income</td>
                    <td class="td-amt amt-g">+৳{{ fmt(summary.total_income) }}</td>
                  </tr>
                  <tr>
                    <td><span class="dot dot--red"></span> Total Expense</td>
                    <td class="td-amt amt-r">−৳{{ fmt(summary.total_expense) }}</td>
                  </tr>
                  <tr>
                    <td><span class="dot dot--teal"></span> Net Savings</td>
                    <td class="td-amt amt-g">+৳{{ fmt(summary.net_savings) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr :class="['foot-row', summary.net_balance>=0?'foot-pos':'foot-neg']">
                    <td class="foot-label">Net Balance</td>
                    <td :class="['foot-val', summary.net_balance>=0?'amt-g':'amt-r']">
                      ৳{{ fmt(summary.net_balance) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- Monthly Deposits -->
        <div class="sec-head" style="margin-top:28px"><i class="fas fa-chart-bar"></i> Monthly Deposits — Last 12 Months</div>
        <div class="tbl-card">
          <table>
            <thead><tr>
              <th class="th-n">#</th><th>Month</th><th class="th-r">Deposited</th><th>Trend</th>
            </tr></thead>
            <tbody>
              <tr v-if="monthlyDeposits.length===0">
                <td colspan="4" class="empty-td">
                  <i class="fas fa-inbox"></i><p>No monthly data available</p>
                </td>
              </tr>
              <tr v-for="(row,i) in monthlyDeposits" :key="row.month" class="row-month">
                <td class="td-n">{{ i+1 }}</td>
                <td class="td-month">{{ fMonth(row.month) }}</td>
                <td class="td-amt amt-g">৳{{ fmt(row.total) }}</td>
                <td class="td-bar">
                  <div class="bar-track">
                    <div class="bar-fill" :style="{width:bw(row.total)+'%'}"></div>
                  </div>
                  <span class="bar-pct">{{ Math.round(bw(row.total)) }}%</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
const DS = { total_members:12, total_deposits:186000, share_deposits:120000, savings_deposits:66000, total_withdrawals:28500, net_savings:157500, total_income:41300, total_expense:37050, net_balance:161750 };
const DM = [
  {month:'2024-06',total:12000},{month:'2024-07',total:14500},{month:'2024-08',total:11000},
  {month:'2024-09',total:16000},{month:'2024-10',total:13500},{month:'2024-11',total:18000},
  {month:'2024-12',total:15500},{month:'2025-01',total:17000},{month:'2025-02',total:19500},
  {month:'2025-03',total:22000},{month:'2025-04',total:21000},{month:'2025-05',total:16000},
];
export default {
  name:'BalanceSheetPage',
  data(){ return { loading:true, apiError:false, summary:null, monthlyDeposits:[] }; },
  computed:{
    maxM(){ return this.monthlyDeposits.length?Math.max(...this.monthlyDeposits.map(r=>parseFloat(r.total||0))):1; },
  },
  methods:{
    fmt(n){ return Number(n||0).toLocaleString('en-BD',{minimumFractionDigits:2}); },
    pct(a,b){ return b?((a/b)*100).toFixed(1):'0.0'; },
    bw(v){ return this.maxM?Math.max(3,(parseFloat(v||0)/this.maxM)*100):0; },
    fMonth(m){ if(!m)return'—'; const[y,mo]=m.split('-'); return new Date(y,parseInt(mo)-1).toLocaleDateString('en-BD',{year:'numeric',month:'long'}); },
    applyDummy(){ this.summary=DS; this.monthlyDeposits=DM; this.apiError=true; },
    async fetchData(){
      try{
        const res=await axios.get('/api/public/balance-sheet');
        const d=res.data?.data;
        if(d&&d.summary){this.summary=d.summary;this.monthlyDeposits=d.monthly_deposits||[];}
        else this.applyDummy();
      }catch{this.applyDummy();}
      finally{this.loading=false;}
    },
  },
  mounted(){ this.fetchData(); },
};
</script>

<style scoped>
.page-root { background:#080c18; min-height:100vh; color:#94a3b8; }

.page-hero { position:relative; overflow:hidden; padding:56px 24px 40px; text-align:center; }
.orb { position:absolute; border-radius:50%; filter:blur(90px); pointer-events:none; }
.orb--1 { width:460px;height:460px; background:rgba(99,102,241,0.14); top:-120px; left:50%; transform:translateX(-50%); }
.orb--2 { width:300px;height:300px; background:rgba(52,211,153,0.08); bottom:-40px; right:10%; }
.hero-body { position:relative; max-width:860px; margin:0 auto; }

.back-link {
  display:inline-flex; align-items:center; gap:6px; color:#475569; font-size:12.5px;
  text-decoration:none; margin-bottom:20px; border:1px solid rgba(255,255,255,0.07);
  border-radius:20px; padding:5px 14px; background:rgba(255,255,255,0.03); transition:all 0.2s;
}
.back-link:hover { color:#a5b4fc; border-color:rgba(99,102,241,0.3); }

.hero-icon {
  width:56px; height:56px; border-radius:16px; margin:0 auto 16px;
  background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.3);
  display:flex; align-items:center; justify-content:center; font-size:22px; color:#818cf8;
}
.hero-title {
  font-size:clamp(24px,4vw,42px); font-weight:800; margin:0 0 10px;
  background:linear-gradient(135deg,#818cf8,#34d399);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.hero-sub { color:#475569; font-size:14.5px; margin:0; }

.page-nav {
  background:rgba(10,14,28,0.9); border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
  position:sticky; top:64px; z-index:100; backdrop-filter:blur(16px);
}
.page-nav__inner {
  max-width:1200px; margin:0 auto; display:flex; overflow-x:auto;
  padding:0 20px; scrollbar-width:none;
}
.page-nav__inner::-webkit-scrollbar { display:none; }
.pnav {
  display:flex; align-items:center; gap:7px; flex-shrink:0;
  padding:0 20px; height:46px; font-size:13px; font-weight:600;
  color:#475569; text-decoration:none; white-space:nowrap;
  border-bottom:2px solid transparent; transition:all 0.2s;
}
.pnav i { font-size:12px; }
.pnav:hover { color:#94a3b8; border-bottom-color:rgba(255,255,255,0.15); }
.pnav--active { color:#818cf8; border-bottom-color:#6366f1; background:rgba(99,102,241,0.06); }

.content-wrap { max-width:1200px; margin:0 auto; padding:28px 24px 80px; }

/* Skeleton */
.skel-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:12px; margin-bottom:16px; }
.skel-card { height:80px; border-radius:12px; background:rgba(255,255,255,0.04); animation:shimmer 1.4s infinite; }
.skel-banner { height:80px; border-radius:12px; background:rgba(255,255,255,0.04); margin-bottom:16px; animation:shimmer 1.4s infinite; }
.skel-head { height:44px; background:rgba(255,255,255,0.04); border-radius:8px 8px 0 0; }
.skel-row {
  height:52px; background:linear-gradient(90deg,rgba(255,255,255,0.02) 25%,rgba(255,255,255,0.05) 50%,rgba(255,255,255,0.02) 75%);
  background-size:200% 100%; animation:shimmer 1.4s infinite; border-bottom:1px solid rgba(255,255,255,0.04);
}
.mt16 { margin-top:16px; }
@keyframes shimmer{ to{ background-position:-200% 0; } }

.demo-banner {
  display:flex; align-items:center; gap:8px;
  background:rgba(251,191,36,0.07); border:1px solid rgba(251,191,36,0.15);
  border-radius:10px; color:#fbbf24; font-size:12px; padding:9px 16px; margin-bottom:20px;
}

/* KPI grid */
.kpi-grid {
  display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
  gap:12px; margin-bottom:16px;
}
.kpi {
  display:flex; align-items:center; gap:12px;
  background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);
  border-radius:12px; padding:16px 18px; transition:transform 0.2s, border-color 0.2s;
}
.kpi:hover { transform:translateY(-2px); }
.kpi-icon {
  width:40px; height:40px; border-radius:10px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; font-size:16px;
}
.kpi-body { display:flex; flex-direction:column; gap:3px; min-width:0; }
.kpi-label { font-size:10.5px; color:#334155; text-transform:uppercase; letter-spacing:0.6px; }
.kpi-val   { font-size:15px; font-weight:800; color:#e2e8f0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.kpi--blue   .kpi-icon { background:rgba(96,165,250,0.12);  color:#60a5fa; border:1px solid rgba(96,165,250,0.2);  }
.kpi--green  .kpi-icon { background:rgba(52,211,153,0.12);  color:#34d399; border:1px solid rgba(52,211,153,0.2);  }
.kpi--orange .kpi-icon { background:rgba(251,146,60,0.12);  color:#fb923c; border:1px solid rgba(251,146,60,0.2);  }
.kpi--teal   .kpi-icon { background:rgba(45,212,191,0.12);  color:#2dd4bf; border:1px solid rgba(45,212,191,0.2);  }
.kpi--indigo .kpi-icon { background:rgba(129,140,248,0.12); color:#818cf8; border:1px solid rgba(129,140,248,0.2); }
.kpi--red    .kpi-icon { background:rgba(248,113,113,0.12); color:#f87171; border:1px solid rgba(248,113,113,0.2); }

/* Net banner */
.net-banner {
  border-radius:12px; padding:20px 24px;
  display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
  margin-bottom:24px;
}
.net-pos { background:linear-gradient(135deg,rgba(52,211,153,0.07),rgba(99,102,241,0.07)); border:1px solid rgba(52,211,153,0.2); }
.net-neg { background:linear-gradient(135deg,rgba(248,113,113,0.07),rgba(251,146,60,0.07)); border:1px solid rgba(248,113,113,0.2); }
.net-left { display:flex; align-items:center; gap:14px; }
.net-icon {
  width:44px; height:44px; border-radius:12px;
  background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2);
  display:flex; align-items:center; justify-content:center; font-size:18px; color:#818cf8;
}
.net-title   { font-size:14px; font-weight:700; color:#e2e8f0; margin-bottom:3px; }
.net-formula { font-size:11.5px; color:#475569; }
.net-value {
  font-size:28px; font-weight:900;
  background:linear-gradient(135deg,#34d399,#818cf8);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.net-neg .net-value {
  background:linear-gradient(135deg,#f87171,#fb923c);
  -webkit-background-clip:text; background-clip:text;
}

/* Two-col layout */
.two-col {
  display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:0;
}

/* Section header */
.sec-head {
  display:flex; align-items:center; gap:8px;
  font-size:13px; font-weight:700; color:#64748b;
  margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;
}
.sec-head i { color:#475569; }

/* Table card */
.tbl-card {
  background:#0d1224; border:1px solid rgba(255,255,255,0.08);
  border-radius:12px; overflow:hidden;
}
table { width:100%; border-collapse:collapse; background:#0d1224 !important; }
thead tr { background:rgba(255,255,255,0.04) !important; border-bottom:1px solid rgba(255,255,255,0.08); }
th {
  padding:12px 16px; font-size:10.5px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.8px; color:#64748b;
  text-align:left; white-space:nowrap; background:transparent !important;
}
.th-n { width:40px; text-align:center; }
.th-r { text-align:right; }
td {
  padding:12px 16px; font-size:13px;
  border-bottom:1px solid rgba(255,255,255,0.05);
  vertical-align:middle; background:transparent !important; color:#94a3b8;
}
tbody tr { background:transparent !important; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:rgba(255,255,255,0.04) !important; }

.row-share { border-left:3px solid rgba(129,140,248,0.5); }
.row-save  { border-left:3px solid rgba(45,212,191,0.5); }
.row-month { border-left:3px solid rgba(99,102,241,0.35); }

.td-n     { color:#334155; font-size:12px; text-align:center; }
.td-month { color:#e2e8f0; font-weight:500; }
.td-pct   { text-align:right; color:#475569; font-size:12px; }

.dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:8px; vertical-align:middle; flex-shrink:0; }
.dot--indigo { background:#818cf8; }
.dot--teal   { background:#2dd4bf; }
.dot--green  { background:#34d399; }
.dot--red    { background:#f87171; }

.td-amt { text-align:right; font-weight:800; font-size:13.5px; }
.amt-g  { color:#34d399; }
.amt-r  { color:#f87171; }

.foot-row td   { border-top:1px solid rgba(255,255,255,0.08) !important; background:rgba(255,255,255,0.04) !important; }
.foot-pos td   { border-top-color:rgba(52,211,153,0.2) !important; background:rgba(52,211,153,0.05) !important; }
.foot-neg td   { border-top-color:rgba(248,113,113,0.2) !important; background:rgba(248,113,113,0.05) !important; }
.foot-label { color:#64748b; font-size:12.5px; padding:12px 16px; font-weight:700; }
.foot-val   { font-weight:900; font-size:15px; padding:12px 16px; text-align:right; }
.foot-pct   { color:#475569; font-size:12px; padding:12px 16px; text-align:right; }

/* Bar */
.td-bar { display:flex; align-items:center; gap:8px; min-width:120px; }
.bar-track { flex:1; height:7px; background:rgba(255,255,255,0.06); border-radius:4px; overflow:hidden; }
.bar-fill  { height:100%; background:linear-gradient(90deg,#818cf8,#34d399); border-radius:4px; transition:width 0.6s ease; }
.bar-pct   { font-size:11px; color:#334155; width:34px; text-align:right; }

.empty-td { text-align:center; padding:48px 24px; color:#334155; }
.empty-td i { font-size:28px; display:block; margin-bottom:8px; }
.empty-td p { margin:0; font-size:13px; }

@media(max-width:900px){
  .two-col { grid-template-columns:1fr; }
}
@media(max-width:640px){
  .kpi-grid { grid-template-columns:1fr 1fr; }
  .net-banner { flex-direction:column; align-items:flex-start; }
  .net-value  { font-size:22px; }
}
</style>
