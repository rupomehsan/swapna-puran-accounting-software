<template>
  <div class="page-root">
    <Head title="Transaction Log" />

    <!-- Hero -->
    <section class="page-hero">
      <div class="orb orb--1"></div>
      <div class="orb orb--2"></div>
      <div class="hero-body">
        <Link href="/" class="back-link"><i class="fas fa-chevron-left"></i> Back to Home</Link>
        <div class="hero-icon"><i class="fas fa-history"></i></div>
        <h1 class="hero-title">Transaction Log</h1>
        <p class="hero-sub">Complete record of all deposits &amp; withdrawals</p>

        <div class="stat-chips" v-if="!loading">
          <div class="chip chip--green">
            <div class="chip-icon"><i class="fas fa-arrow-circle-down"></i></div>
            <div class="chip-body"><span>Total Deposits</span><strong>৳{{ fmt(summary.total_deposits) }}</strong></div>
          </div>
          <div class="chip chip--red">
            <div class="chip-icon"><i class="fas fa-arrow-circle-up"></i></div>
            <div class="chip-body"><span>Total Withdrawals</span><strong>৳{{ fmt(summary.total_withdrawals) }}</strong></div>
          </div>
          <div class="chip chip--indigo">
            <div class="chip-icon"><i class="fas fa-list-ol"></i></div>
            <div class="chip-body"><span>Total Records</span><strong>{{ summary.total_count }}</strong></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Page navigation tabs -->
    <div class="page-nav">
      <div class="page-nav__inner">
        <Link href="/transaction-log" class="pnav pnav--active">
          <i class="fas fa-history"></i><span>Transaction Log</span>
        </Link>
        <Link href="/income" class="pnav">
          <i class="fas fa-hand-holding-usd"></i><span>Income</span>
        </Link>
        <Link href="/expense" class="pnav">
          <i class="fas fa-receipt"></i><span>Expense</span>
        </Link>
        <Link href="/balance-sheet" class="pnav">
          <i class="fas fa-file-invoice-dollar"></i><span>Balance Sheet</span>
        </Link>
      </div>
    </div>

    <!-- Content -->
    <div class="content-wrap">
      <div class="controls-bar">
        <div class="filter-group">
          <button :class="['ftab', filter==='all'&&'ftab--on']"        @click="filter='all'">All</button>
          <button :class="['ftab ftab--g',filter==='deposit'&&'ftab--on']"    @click="filter='deposit'">
            <i class="fas fa-arrow-down"></i> Deposits
          </button>
          <button :class="['ftab ftab--r',filter==='withdrawal'&&'ftab--on']" @click="filter='withdrawal'">
            <i class="fas fa-arrow-up"></i> Withdrawals
          </button>
        </div>
        <div class="search-field">
          <i class="fas fa-search"></i>
          <input v-model="search" placeholder="Search member or voucher…" />
          <span v-if="search" class="clear-btn" @click="search=''"><i class="fas fa-times"></i></span>
        </div>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="skel-wrap">
        <div class="skel-head"></div>
        <div class="skel-row" v-for="n in 7" :key="n" :style="{opacity: 1 - n*0.1}"></div>
      </div>

      <div v-else class="tbl-card">
        <div v-if="apiError" class="demo-banner">
          <i class="fas fa-flask"></i>
          Sample data shown — live data will appear once the API has records.
        </div>

        <div class="tbl-top">
          <span class="tbl-count">
            <strong>{{ filtered.length }}</strong> {{ filter==='all'?'records':filter+'s' }}
          </span>
          <div class="tbl-totals">
            <span class="tot-g">+৳{{ fmt(filtered.filter(t=>t.type==='deposit').reduce((s,t)=>s+parseFloat(t.amount||0),0)) }}</span>
            <span class="tot-r">−৳{{ fmt(filtered.filter(t=>t.type==='withdrawal').reduce((s,t)=>s+parseFloat(t.amount||0),0)) }}</span>
          </div>
        </div>

        <div class="tbl-scroll">
          <table>
            <thead>
              <tr>
                <th class="th-n">#</th>
                <th>Date</th>
                <th>Voucher</th>
                <th>Member</th>
                <th>Type</th>
                <th>Method</th>
                <th class="th-r">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filtered.length===0">
                <td colspan="7" class="empty-td">
                  <i class="fas fa-inbox"></i><p>No transactions found</p>
                </td>
              </tr>
              <tr v-for="(tx,i) in filtered" :key="i"
                  :class="tx.type==='deposit'?'row-dep':'row-wit'">
                <td class="td-n">{{ i+1 }}</td>
                <td class="td-date">{{ fDate(tx.date) }}</td>
                <td class="td-mono">{{ tx.voucher_no||'—' }}</td>
                <td class="td-name">{{ tx.member_name||'—' }}</td>
                <td>
                  <span :class="['badge',tx.type==='deposit'?'badge-g':'badge-r']">
                    <i :class="tx.type==='deposit'?'fas fa-arrow-down':'fas fa-arrow-up'"></i>
                    {{ tx.type==='deposit'?dLabel(tx.deposit_type):'Withdrawal' }}
                  </span>
                </td>
                <td class="td-method">{{ tx.payment_method||'—' }}</td>
                <td :class="['td-amt',tx.type==='deposit'?'amt-g':'amt-r']">
                  <span class="amt-sign">{{ tx.type==='deposit'?'+':'−' }}</span>৳{{ fmt(tx.amount) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
const DUMMY = [
  { voucher_no:'DEP-0001', member_name:'রহিম উদ্দিন',       type:'deposit',    deposit_type:'share_deposit', amount:5000,  date:'2025-05-10', payment_method:'Cash'   },
  { voucher_no:'DEP-0002', member_name:'করিম মিয়া',         type:'deposit',    deposit_type:'extra_savings', amount:3000,  date:'2025-05-08', payment_method:'bKash'  },
  { voucher_no:'WIT-0001', member_name:'সালমা বেগম',        type:'withdrawal', deposit_type:null,             amount:2500,  date:'2025-05-06', payment_method:'Cash'   },
  { voucher_no:'DEP-0003', member_name:'নাসির হোসেন',       type:'deposit',    deposit_type:'share_deposit', amount:5000,  date:'2025-05-04', payment_method:'Nagad'  },
  { voucher_no:'DEP-0004', member_name:'জান্নাতুল ফেরদৌস',  type:'deposit',    deposit_type:'extra_savings', amount:8000,  date:'2025-05-02', payment_method:'Cash'   },
  { voucher_no:'WIT-0002', member_name:'আবদুল হাকিম',       type:'withdrawal', deposit_type:null,             amount:4000,  date:'2025-04-28', payment_method:'bKash'  },
  { voucher_no:'DEP-0005', member_name:'ফাতেমা খানম',       type:'deposit',    deposit_type:'share_deposit', amount:5000,  date:'2025-04-25', payment_method:'Cash'   },
  { voucher_no:'DEP-0006', member_name:'মোহাম্মদ আলী',     type:'deposit',    deposit_type:'share_deposit', amount:5000,  date:'2025-04-20', payment_method:'Rocket' },
];
export default {
  name: 'TransactionLogPage',
  data(){ return { loading:true, apiError:false, transactions:[], summary:{total_deposits:0,total_withdrawals:0,total_count:0}, filter:'all', search:'' }; },
  computed:{
    filtered(){
      let l=this.transactions;
      if(this.filter!=='all') l=l.filter(t=>t.type===this.filter);
      const q=this.search.toLowerCase().trim();
      if(q) l=l.filter(t=>(t.member_name||'').toLowerCase().includes(q)||(t.voucher_no||'').toLowerCase().includes(q));
      return l;
    },
  },
  methods:{
    fmt(n){ return Number(n||0).toLocaleString('en-BD',{minimumFractionDigits:2}); },
    fDate(d){ if(!d)return'—'; return new Date(d).toLocaleDateString('en-BD',{year:'numeric',month:'short',day:'2-digit'}); },
    dLabel(t){ return t==='share_deposit'?'Share Deposit':t==='extra_savings'?'Extra Savings':'Deposit'; },
    applyDummy(){
      this.transactions=DUMMY;
      const deps=DUMMY.filter(t=>t.type==='deposit').reduce((s,t)=>s+t.amount,0);
      const wits=DUMMY.filter(t=>t.type==='withdrawal').reduce((s,t)=>s+t.amount,0);
      this.summary={total_deposits:deps,total_withdrawals:wits,total_count:DUMMY.length};
      this.apiError=true;
    },
    async fetchData(){
      try{
        const res=await axios.get('/api/public/transaction-log');
        const d=res.data?.data;
        if(d&&d.transactions&&d.transactions.length>0){this.transactions=d.transactions;this.summary=d.summary;}
        else this.applyDummy();
      }catch{this.applyDummy();}
      finally{this.loading=false;}
    },
  },
  mounted(){ this.fetchData(); },
};
</script>

<style scoped>
/* ── Root ─────────────────────────────────────────────── */
.page-root { background:#080c18; min-height:100vh; color:#94a3b8; }

/* ── Hero ─────────────────────────────────────────────── */
.page-hero {
  position:relative; overflow:hidden;
  padding:56px 24px 40px; text-align:center;
}
.orb {
  position:absolute; border-radius:50%;
  filter:blur(90px); pointer-events:none;
}
.orb--1 { width:460px;height:460px; background:rgba(99,102,241,0.14); top:-120px; left:50%; transform:translateX(-50%); }
.orb--2 { width:320px;height:320px; background:rgba(52,211,153,0.07); bottom:-60px; right:8%; }
.hero-body { position:relative; max-width:860px; margin:0 auto; }

.back-link {
  display:inline-flex; align-items:center; gap:6px;
  color:#475569; font-size:12.5px; text-decoration:none; margin-bottom:20px;
  border:1px solid rgba(255,255,255,0.07); border-radius:20px; padding:5px 14px;
  background:rgba(255,255,255,0.03); transition:all 0.2s;
}
.back-link:hover { color:#a5b4fc; border-color:rgba(99,102,241,0.3); }

.hero-icon {
  width:56px; height:56px; border-radius:16px; margin:0 auto 16px;
  background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.3);
  display:flex; align-items:center; justify-content:center;
  font-size:22px; color:#818cf8;
}
.hero-title {
  font-size:clamp(24px,4vw,42px); font-weight:800; margin:0 0 10px;
  background:linear-gradient(135deg,#a5b4fc,#34d399);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.hero-sub { color:#475569; font-size:14.5px; margin:0 0 28px; }

/* Stat chips */
.stat-chips { display:flex; flex-wrap:wrap; justify-content:center; gap:10px; }
.chip {
  display:flex; align-items:center; gap:10px;
  border:1px solid; border-radius:12px; padding:10px 18px;
}
.chip--green  { background:rgba(52,211,153,0.07);  border-color:rgba(52,211,153,0.2); }
.chip--red    { background:rgba(248,113,113,0.07); border-color:rgba(248,113,113,0.2); }
.chip--indigo { background:rgba(99,102,241,0.07);  border-color:rgba(99,102,241,0.2);  }
.chip-icon { font-size:18px; }
.chip--green  .chip-icon { color:#34d399; }
.chip--red    .chip-icon { color:#f87171; }
.chip--indigo .chip-icon { color:#818cf8; }
.chip-body { display:flex; flex-direction:column; gap:2px; text-align:left; }
.chip-body span  { font-size:11px; color:#475569; text-transform:uppercase; letter-spacing:0.5px; }
.chip-body strong{ font-size:16px; font-weight:800; color:#e2e8f0; }

/* ── Page navigation tabs ─────────────────────────────── */
.page-nav {
  background:rgba(10,14,28,0.9);
  border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
  position:sticky; top:64px; z-index:100;
  backdrop-filter:blur(16px);
}
.page-nav__inner {
  max-width:1200px; margin:0 auto;
  display:flex; overflow-x:auto; padding:0 20px;
  scrollbar-width:none;
}
.page-nav__inner::-webkit-scrollbar { display:none; }
.pnav {
  display:flex; align-items:center; gap:7px; flex-shrink:0;
  padding:0 20px; height:46px;
  font-size:13px; font-weight:600; color:#475569;
  text-decoration:none; white-space:nowrap;
  border-bottom:2px solid transparent;
  transition:all 0.2s;
}
.pnav i { font-size:12px; }
.pnav:hover { color:#94a3b8; border-bottom-color:rgba(255,255,255,0.15); }
.pnav--active {
  color:#a5b4fc; border-bottom-color:#6366f1;
  background:rgba(99,102,241,0.06);
}

/* ── Content ──────────────────────────────────────────── */
.content-wrap { max-width:1200px; margin:0 auto; padding:28px 24px 80px; }

/* Controls */
.controls-bar {
  display:flex; flex-wrap:wrap; align-items:center;
  justify-content:space-between; gap:12px; margin-bottom:16px;
}
.filter-group { display:flex; gap:6px; }
.ftab {
  display:flex; align-items:center; gap:6px;
  background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);
  border-radius:8px; padding:7px 14px; color:#475569; font-size:12.5px;
  cursor:pointer; transition:all 0.2s; font-weight:600;
}
.ftab:hover { background:rgba(255,255,255,0.07); color:#94a3b8; }
.ftab--on        { background:rgba(99,102,241,0.15); border-color:rgba(99,102,241,0.35); color:#a5b4fc; }
.ftab.ftab--g.ftab--on { background:rgba(52,211,153,0.12); border-color:rgba(52,211,153,0.35); color:#34d399; }
.ftab.ftab--r.ftab--on { background:rgba(248,113,113,0.12);border-color:rgba(248,113,113,0.35);color:#f87171; }

.search-field {
  display:flex; align-items:center; gap:8px;
  background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);
  border-radius:8px; padding:7px 14px; min-width:260px;
}
.search-field i { color:#334155; font-size:12px; }
.search-field input { background:none; border:none; outline:none; color:#e2e8f0; font-size:13px; width:100%; }
.search-field input::placeholder { color:#334155; }
.clear-btn { color:#475569; cursor:pointer; font-size:11px; padding:2px 4px; }
.clear-btn:hover { color:#94a3b8; }

/* Skeleton */
.skel-wrap { border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); }
.skel-head { height:44px; background:rgba(255,255,255,0.04); }
.skel-row  {
  height:52px; margin:0;
  background:linear-gradient(90deg,rgba(255,255,255,0.02) 25%,rgba(255,255,255,0.05) 50%,rgba(255,255,255,0.02) 75%);
  background-size:200% 100%;
  animation:shimmer 1.4s infinite;
  border-bottom:1px solid rgba(255,255,255,0.04);
}
@keyframes shimmer{ to{ background-position:-200% 0; } }

/* Demo banner */
.demo-banner {
  display:flex; align-items:center; gap:8px;
  background:rgba(251,191,36,0.07); border-bottom:1px solid rgba(251,191,36,0.15);
  color:#fbbf24; font-size:12px; padding:9px 18px;
}

/* Table card */
.tbl-card {
  background:#0d1224;
  border:1px solid rgba(255,255,255,0.08);
  border-radius:14px; overflow:hidden;
}
table { background:#0d1224 !important; }
.tbl-top {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
  padding:12px 18px; border-bottom:1px solid rgba(255,255,255,0.05);
  background:rgba(255,255,255,0.02);
}
.tbl-count { font-size:12.5px; color:#475569; }
.tbl-count strong { color:#94a3b8; }
.tbl-totals { display:flex; gap:14px; font-size:12.5px; font-weight:700; }
.tot-g { color:#34d399; }
.tot-r { color:#f87171; }

.tbl-scroll { overflow-x:auto; }
table { width:100%; border-collapse:collapse; }

thead tr {
  background:rgba(255,255,255,0.04) !important;
  border-bottom:1px solid rgba(255,255,255,0.08);
}
th {
  padding:13px 16px; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.8px; color:#64748b;
  text-align:left; white-space:nowrap;
  background:transparent !important;
}
.th-n { width:40px; text-align:center; }
.th-r  { text-align:right; }

td {
  padding:13px 16px; font-size:13px;
  border-bottom:1px solid rgba(255,255,255,0.05);
  background:transparent !important; color:#94a3b8;
}
tbody tr:last-child td { border-bottom:none; }
tbody tr { transition:background 0.15s; background:transparent !important; }
tbody tr:hover td { background:rgba(255,255,255,0.04) !important; }

.row-dep { border-left:3px solid rgba(52,211,153,0.5) !important; }
.row-wit { border-left:3px solid rgba(248,113,113,0.5) !important; }
tfoot tr td { background:rgba(255,255,255,0.03) !important; }

.td-n     { color:#334155; font-size:12px; text-align:center; }
.td-date  { color:#475569; white-space:nowrap; }
.td-mono  { font-family:monospace; font-size:12px; color:#64748b; }
.td-name  { color:#e2e8f0; font-weight:500; }
.td-method{ color:#475569; font-size:12px; }

.badge {
  display:inline-flex; align-items:center; gap:5px;
  font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:20px;
  white-space:nowrap;
}
.badge-g { background:rgba(52,211,153,0.12); color:#34d399; border:1px solid rgba(52,211,153,0.2); }
.badge-r { background:rgba(248,113,113,0.12);color:#f87171; border:1px solid rgba(248,113,113,0.2); }

.td-amt   { text-align:right; font-weight:800; font-size:14px; white-space:nowrap; }
.amt-g    { color:#34d399; }
.amt-r    { color:#f87171; }
.amt-sign { font-size:12px; opacity:0.7; margin-right:1px; }

.empty-td { text-align:center; padding:60px 24px; color:#334155; }
.empty-td i { font-size:32px; display:block; margin-bottom:10px; }
.empty-td p { margin:0; font-size:14px; }

@media(max-width:768px){
  .controls-bar { flex-direction:column; align-items:stretch; }
  .search-field { min-width:unset; }
  th:nth-child(4),td:nth-child(4),
  th:nth-child(6),td:nth-child(6) { display:none; }
}
</style>
