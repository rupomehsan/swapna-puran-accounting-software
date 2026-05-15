<template>
  <div class="page-root">
    <Head title="Expense" />

    <section class="page-hero">
      <div class="orb orb--1"></div>
      <div class="orb orb--2"></div>
      <div class="hero-body">
        <Link href="/" class="back-link"><i class="fas fa-chevron-left"></i> Back to Home</Link>
        <div class="hero-icon"><i class="fas fa-receipt"></i></div>
        <h1 class="hero-title">Expense</h1>
        <p class="hero-sub">All recorded expenditures and expense entries</p>

        <div class="stat-chips" v-if="!loading">
          <div class="chip chip--red">
            <div class="chip-icon"><i class="fas fa-wallet"></i></div>
            <div class="chip-body"><span>Total Expense</span><strong>৳{{ fmt(total) }}</strong></div>
          </div>
          <div class="chip chip--orange">
            <div class="chip-icon"><i class="fas fa-list-ol"></i></div>
            <div class="chip-body"><span>Entries</span><strong>{{ entries.length }}</strong></div>
          </div>
        </div>
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
        <Link href="/expense" class="pnav pnav--active">
          <i class="fas fa-receipt"></i><span>Expense</span>
        </Link>
        <Link href="/balance-sheet" class="pnav">
          <i class="fas fa-file-invoice-dollar"></i><span>Balance Sheet</span>
        </Link>
      </div>
    </div>

    <div class="content-wrap">
      <div class="controls-bar">
        <div class="search-field">
          <i class="fas fa-search"></i>
          <input v-model="search" placeholder="Search category or voucher…" />
          <span v-if="search" class="clear-btn" @click="search=''"><i class="fas fa-times"></i></span>
        </div>
        <div class="tbl-totals-bar">
          <span>Filtered:</span>
          <strong class="tot-r">৳{{ fmt(filteredTotal) }}</strong>
        </div>
      </div>

      <div v-if="loading" class="skel-wrap">
        <div class="skel-head"></div>
        <div class="skel-row" v-for="n in 6" :key="n" :style="{opacity:1-n*0.1}"></div>
      </div>

      <div v-else class="tbl-card">
        <div v-if="apiError" class="demo-banner">
          <i class="fas fa-flask"></i> Sample data shown — live data will appear once the API has records.
        </div>
        <div class="tbl-top">
          <span class="tbl-count"><strong>{{ filtered.length }}</strong> entries</span>
          <span class="tbl-total">Total: <strong class="tot-r">৳{{ fmt(filteredTotal) }}</strong></span>
        </div>
        <div class="tbl-scroll">
          <table>
            <thead>
              <tr>
                <th class="th-n">#</th>
                <th>Date</th>
                <th>Voucher</th>
                <th>Category</th>
                <th>Description</th>
                <th class="th-r">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filtered.length===0">
                <td colspan="6" class="empty-td">
                  <i class="fas fa-inbox"></i><p>No expense entries found</p>
                </td>
              </tr>
              <tr v-for="(e,i) in filtered" :key="i" class="row-exp">
                <td class="td-n">{{ i+1 }}</td>
                <td class="td-date">{{ fDate(e.entry_date) }}</td>
                <td class="td-mono">{{ e.voucher_no||'—' }}</td>
                <td class="td-cat">
                  <span class="cat-dot"></span>{{ e.expense_category||'—' }}
                </td>
                <td class="td-desc">{{ stripHtml(e.description)||'—' }}</td>
                <td class="td-amt amt-r">−৳{{ fmt(e.amount) }}</td>
              </tr>
            </tbody>
            <tfoot v-if="filtered.length>0">
              <tr class="foot-row">
                <td colspan="5" class="foot-label">Total</td>
                <td class="foot-val">৳{{ fmt(filteredTotal) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
const DUMMY = [
  { id:1, voucher_no:'EXP-0001', expense_category:'অফিস ভাড়া',    amount:8000,  entry_date:'2025-05-05', description:'মে মাসের অফিস ভাড়া'       },
  { id:2, voucher_no:'EXP-0002', expense_category:'বিদ্যুৎ বিল',   amount:1200,  entry_date:'2025-05-03', description:'এপ্রিল মাসের বিদ্যুৎ বিল' },
  { id:3, voucher_no:'EXP-0003', expense_category:'কর্মচারী বেতন', amount:15000, entry_date:'2025-05-01', description:'মে মাসের কর্মচারী বেতন'   },
  { id:4, voucher_no:'EXP-0004', expense_category:'অফিস সরবরাহ',   amount:3500,  entry_date:'2025-04-22', description:'স্টেশনারি ও অফিস সামগ্রী'  },
  { id:5, voucher_no:'EXP-0005', expense_category:'ব্যাংক চার্জ',  amount:450,   entry_date:'2025-04-15', description:'ব্যাংক সার্ভিস চার্জ'       },
  { id:6, voucher_no:'EXP-0006', expense_category:'অফিস ভাড়া',    amount:8000,  entry_date:'2025-04-05', description:'এপ্রিল মাসের অফিস ভাড়া'   },
  { id:7, voucher_no:'EXP-0007', expense_category:'যাতায়াত খরচ',  amount:900,   entry_date:'2025-03-28', description:'ফিল্ড ভিজিট পরিবহন'         },
];
export default {
  name:'ExpensePage',
  data(){ return { loading:true, apiError:false, entries:[], total:0, search:'' }; },
  computed:{
    filtered(){
      const q=this.search.toLowerCase().trim();
      if(!q) return this.entries;
      return this.entries.filter(e=>(e.expense_category||'').toLowerCase().includes(q)||(e.voucher_no||'').toLowerCase().includes(q));
    },
    filteredTotal(){ return this.filtered.reduce((s,e)=>s+parseFloat(e.amount||0),0); },
  },
  methods:{
    fmt(n){ return Number(n||0).toLocaleString('en-BD',{minimumFractionDigits:2}); },
    fDate(d){ if(!d)return'—'; return new Date(d).toLocaleDateString('en-BD',{year:'numeric',month:'short',day:'2-digit'}); },
    stripHtml(h){ if(!h)return''; return h.replace(/<[^>]*>/g,'').replace(/&nbsp;/g,' ').trim(); },
    applyDummy(){ this.entries=DUMMY; this.total=DUMMY.reduce((s,e)=>s+e.amount,0); this.apiError=true; },
    async fetchData(){
      try{
        const res=await axios.get(`${location.origin}/api/public/expense`);
        const d=res.data?.data;
        if(d&&d.entries&&d.entries.length>0){this.entries=d.entries;this.total=d.total;}
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
.orb--1 { width:440px;height:440px; background:rgba(248,113,113,0.1); top:-100px; left:50%; transform:translateX(-50%); }
.orb--2 { width:300px;height:300px; background:rgba(251,146,60,0.07); bottom:-40px; left:10%; }
.hero-body { position:relative; max-width:860px; margin:0 auto; }

.back-link {
  display:inline-flex; align-items:center; gap:6px; color:#475569; font-size:12.5px;
  text-decoration:none; margin-bottom:20px; border:1px solid rgba(255,255,255,0.07);
  border-radius:20px; padding:5px 14px; background:rgba(255,255,255,0.03); transition:all 0.2s;
}
.back-link:hover { color:#f87171; border-color:rgba(248,113,113,0.3); }

.hero-icon {
  width:56px; height:56px; border-radius:16px; margin:0 auto 16px;
  background:rgba(248,113,113,0.1); border:1px solid rgba(248,113,113,0.3);
  display:flex; align-items:center; justify-content:center; font-size:22px; color:#f87171;
}
.hero-title {
  font-size:clamp(24px,4vw,42px); font-weight:800; margin:0 0 10px;
  background:linear-gradient(135deg,#f87171,#fb923c);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.hero-sub { color:#475569; font-size:14.5px; margin:0 0 28px; }

.stat-chips { display:flex; flex-wrap:wrap; justify-content:center; gap:10px; }
.chip { display:flex; align-items:center; gap:10px; border:1px solid; border-radius:12px; padding:10px 18px; }
.chip--red    { background:rgba(248,113,113,0.07); border-color:rgba(248,113,113,0.2); }
.chip--orange { background:rgba(251,146,60,0.07);  border-color:rgba(251,146,60,0.2);  }
.chip-icon { font-size:18px; }
.chip--red    .chip-icon { color:#f87171; }
.chip--orange .chip-icon { color:#fb923c; }
.chip-body { display:flex; flex-direction:column; gap:2px; text-align:left; }
.chip-body span   { font-size:11px; color:#475569; text-transform:uppercase; letter-spacing:0.5px; }
.chip-body strong { font-size:16px; font-weight:800; color:#e2e8f0; }

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
.pnav--active { color:#f87171; border-bottom-color:#ef4444; background:rgba(248,113,113,0.06); }

.content-wrap { max-width:1200px; margin:0 auto; padding:28px 24px 80px; }

.controls-bar {
  display:flex; flex-wrap:wrap; align-items:center;
  justify-content:space-between; gap:12px; margin-bottom:16px;
}
.search-field {
  display:flex; align-items:center; gap:8px;
  background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);
  border-radius:8px; padding:7px 14px; min-width:280px;
}
.search-field i { color:#334155; font-size:12px; }
.search-field input { background:none; border:none; outline:none; color:#e2e8f0; font-size:13px; width:100%; }
.search-field input::placeholder { color:#334155; }
.clear-btn { color:#475569; cursor:pointer; font-size:11px; }
.clear-btn:hover { color:#94a3b8; }
.tbl-totals-bar { display:flex; align-items:center; gap:8px; font-size:13px; color:#475569; }
.tot-r { color:#f87171; font-size:15px; }

.skel-wrap { border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); }
.skel-head { height:44px; background:rgba(255,255,255,0.04); }
.skel-row  {
  height:52px; background:linear-gradient(90deg,rgba(255,255,255,0.02) 25%,rgba(255,255,255,0.05) 50%,rgba(255,255,255,0.02) 75%);
  background-size:200% 100%; animation:shimmer 1.4s infinite; border-bottom:1px solid rgba(255,255,255,0.04);
}
@keyframes shimmer{ to{ background-position:-200% 0; } }

.demo-banner {
  display:flex; align-items:center; gap:8px;
  background:rgba(251,191,36,0.07); border-bottom:1px solid rgba(251,191,36,0.15);
  color:#fbbf24; font-size:12px; padding:9px 18px;
}
.tbl-card {
  background:#0d1224; border:1px solid rgba(255,255,255,0.08);
  border-radius:14px; overflow:hidden;
}
.tbl-top {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
  padding:12px 18px; border-bottom:1px solid rgba(255,255,255,0.05);
  background:rgba(255,255,255,0.02);
}
.tbl-count { font-size:12.5px; color:#475569; }
.tbl-count strong { color:#94a3b8; }
.tbl-total { font-size:12.5px; color:#475569; }

.tbl-scroll { overflow-x:auto; }
table { width:100%; border-collapse:collapse; background:#0d1224 !important; }
thead tr { background:rgba(255,255,255,0.04) !important; border-bottom:1px solid rgba(255,255,255,0.08); }
th {
  padding:13px 16px; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.8px; color:#64748b;
  text-align:left; white-space:nowrap; background:transparent !important;
}
.th-n { width:40px; text-align:center; }
.th-r { text-align:right; }
td {
  padding:13px 16px; font-size:13px;
  border-bottom:1px solid rgba(255,255,255,0.05);
  background:transparent !important; color:#94a3b8;
}
tbody tr { background:transparent !important; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:rgba(255,255,255,0.04) !important; }
.row-exp { border-left:3px solid rgba(248,113,113,0.45); }

.td-n    { color:#334155; font-size:12px; text-align:center; }
.td-date { color:#475569; white-space:nowrap; }
.td-mono { font-family:monospace; font-size:12px; color:#64748b; }
.td-cat  { color:#e2e8f0; font-weight:500; display:flex; align-items:center; gap:8px; }
.cat-dot { width:7px; height:7px; border-radius:50%; background:#f87171; flex-shrink:0; }
.td-desc { color:#475569; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.td-amt  { text-align:right; font-weight:800; font-size:14px; }
.amt-r   { color:#f87171; }

.foot-row td { background:rgba(248,113,113,0.06) !important; border-top:1px solid rgba(248,113,113,0.2); }
.foot-label { color:#64748b; font-size:12.5px; padding:13px 16px; text-align:right; font-weight:600; }
.foot-val   { color:#f87171; font-weight:800; font-size:15px; padding:13px 16px; text-align:right; }

.empty-td { text-align:center; padding:60px 24px; color:#334155; }
.empty-td i { font-size:32px; display:block; margin-bottom:10px; }
.empty-td p { margin:0; font-size:14px; }

@media(max-width:768px){
  .controls-bar { flex-direction:column; align-items:stretch; }
  .search-field { min-width:unset; }
  th:nth-child(5),td:nth-child(5) { display:none; }
}
</style>
