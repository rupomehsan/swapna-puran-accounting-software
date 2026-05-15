<template>
  <div class="md-page">
    <Head :title="member ? member.name + ' — Member Detail' : 'Member Detail'" />

    <!-- ── Page nav ── -->
    <div class="page-nav-bar">
      <div class="page-nav-bar__inner">
        <Link href="/" class="back-btn">
          <i class="fas fa-arrow-left"></i> Back to Home
        </Link>
        <div class="page-tabs">
          <Link href="/transaction-log" class="ptab"><i class="fas fa-history"></i><span>Transaction Log</span></Link>
          <Link href="/income"          class="ptab"><i class="fas fa-hand-holding-usd"></i><span>Income</span></Link>
          <Link href="/expense"         class="ptab"><i class="fas fa-receipt"></i><span>Expense</span></Link>
          <Link href="/balance-sheet"   class="ptab"><i class="fas fa-file-invoice-dollar"></i><span>Balance Sheet</span></Link>
        </div>
      </div>
    </div>

    <div class="md-wrap">

      <!-- ── Skeleton ── -->
      <template v-if="loading">
        <div class="skel-profile">
          <div class="skel-avatar"></div>
          <div class="skel-lines">
            <div class="skel-line skel-line--lg"></div>
            <div class="skel-line skel-line--md"></div>
            <div class="skel-stats-row">
              <div class="skel-stat" v-for="n in 4" :key="n"></div>
            </div>
          </div>
        </div>
        <div class="skel-table">
          <div class="skel-th"></div>
          <div class="skel-row" v-for="n in 6" :key="n">
            <div class="skel-cell" v-for="c in 7" :key="c"></div>
          </div>
        </div>
      </template>

      <!-- ── 404 ── -->
      <div v-else-if="notFound" class="not-found">
        <i class="fas fa-user-slash"></i>
        <p>Member not found.</p>
        <Link href="/" class="back-btn">Go Home</Link>
      </div>

      <!-- ── Content ── -->
      <template v-else>

        <!-- Profile card -->
        <div class="profile-card">
          <div class="profile-card__left">
            <div class="profile-avatar" :style="{ background: avatarGradient }">
              <img v-if="member.image" :src="imgUrl(member.image)" :alt="member.name"
                   @error="e => e.target.style.display='none'" />
              <span v-else>{{ initials(member.name) }}</span>
            </div>
          </div>
          <div class="profile-card__info">
            <div class="profile-card__badge">Member</div>
            <h1 class="profile-card__name">{{ member.name }}</h1>
            <div class="profile-card__meta">
              <span v-if="member.phone"><i class="fas fa-phone-alt"></i> {{ member.phone }}</span>
              <span v-if="member.email"><i class="fas fa-envelope"></i> {{ member.email }}</span>
              <span><i class="fas fa-calendar-alt"></i> Joined {{ fmtDate(member.join_date || member.created_at) }}</span>
            </div>
            <div class="profile-card__stats">
              <div class="ps-item ps-item--indigo">
                <span class="ps-item__icon"><i class="fas fa-layer-group"></i></span>
                <span class="ps-item__label">No. of Shares</span>
                <span class="ps-item__val">
                  {{ member.number_of_share ?? 0 }}
                  <span v-if="stats.last_adjustment" class="share-trend-inline"
                        :class="stats.last_adjustment.adjustment_type === 'increase' ? 'share-trend-inline--up' : 'share-trend-inline--down'">
                    <i :class="stats.last_adjustment.adjustment_type === 'increase' ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                    {{ stats.last_adjustment.adjustment_type === 'increase' ? '+' : '−' }}{{ Math.abs(stats.last_adjustment.shares_delta || 0) }}
                  </span>
                </span>
              </div>
              <div class="ps-item ps-item--blue">
                <span class="ps-item__icon"><i class="fas fa-piggy-bank"></i></span>
                <span class="ps-item__label">Total Deposit</span>
                <span class="ps-item__val">৳ {{ fmt(stats.total_deposit) }}</span>
              </div>
              <div class="ps-item ps-item--purple">
                <span class="ps-item__icon"><i class="fas fa-coins"></i></span>
                <span class="ps-item__label">Share Deposit</span>
                <span class="ps-item__val">৳ {{ fmt(stats.total_share) }}</span>
              </div>
              <div class="ps-item ps-item--green">
                <span class="ps-item__icon"><i class="fas fa-sack-dollar"></i></span>
                <span class="ps-item__label">Extra Savings</span>
                <span class="ps-item__val">৳ {{ fmt(stats.total_savings) }}</span>
              </div>
              <div class="ps-item" :class="stats.total_due > 0 ? 'ps-item--red' : 'ps-item--teal'">
                <span class="ps-item__icon"><i :class="stats.total_due > 0 ? 'fas fa-triangle-exclamation' : 'fas fa-circle-check'"></i></span>
                <span class="ps-item__label">{{ stats.total_due > 0 ? 'Due Amount' : 'Due Status' }}</span>
                <span class="ps-item__val">{{ stats.total_due > 0 ? '৳ ' + fmt(stats.total_due) : '✓ Clear' }}</span>
              </div>
              <div v-if="isAdvancePaid(stats.paid_till)" class="ps-item ps-item--purple">
                <span class="ps-item__icon"><i class="fas fa-calendar-check"></i></span>
                <span class="ps-item__label">Paid Till (Advance)</span>
                <span class="ps-item__val">{{ fmtMonth(stats.paid_till) }}</span>
              </div>
            </div>
          </div>
          <div class="profile-card__badge-count">
            <span class="count-ring">{{ stats.deposit_count }}</span>
            <span class="count-label">Deposits</span>
          </div>
        </div>

        <!-- Deposit history -->
        <div class="deposits-section">
          <div class="deposits-section__header">
            <h2 class="deposits-section__title">
              <i class="fas fa-clock-rotate-left"></i> Deposit History
            </h2>
            <span class="deposits-section__count">{{ deposits.length }} records</span>
          </div>

          <div v-if="!deposits.length" class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No deposits found for this member.</p>
          </div>

          <div v-else class="table-wrap">
            <table class="dep-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Voucher No</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>For Month</th>
                  <th>Payment Date</th>
                  <th>Method</th>
                  <th>Note</th>
                  <th>Voucher</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(d, i) in deposits" :key="d.id">
                  <td class="td-num">{{ i + 1 }}</td>
                  <td class="td-mono">{{ d.voucher_no || '—' }}</td>
                  <td>
                    <span class="type-badge" :class="d.deposit_type === 'share_deposit' ? 'type-badge--purple' : 'type-badge--green'">
                      {{ d.deposit_type === 'share_deposit' ? 'Share' : 'Extra' }}
                    </span>
                  </td>
                  <td class="td-amount">৳ {{ fmt(d.amount) }}</td>
                  <td class="td-date">{{ fmtMonth(d.for_month) }}</td>
                  <td class="td-date">{{ fmtDate(d.payment_date) }}</td>
                  <td class="td-method">{{ d.payment_method || '—' }}</td>
                  <td class="td-note">{{ stripHtml(d.note) || '—' }}</td>
                  <td class="td-img">
                    <button v-if="d.image" class="voucher-thumb-btn" @click="openLightbox(imgUrl(d.image))" title="View voucher">
                      <img :src="imgUrl(d.image)" :alt="'Voucher ' + d.voucher_no" class="voucher-thumb" />
                      <span class="voucher-thumb-overlay"><i class="fas fa-magnifying-glass-plus"></i></span>
                    </button>
                    <span v-else class="no-img">—</span>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="dep-foot">
                  <td colspan="3"><strong>Total</strong></td>
                  <td class="td-amount"><strong>৳ {{ fmt(stats.total_deposit) }}</strong></td>
                  <td colspan="5"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </template>
    </div>

    <!-- ── Lightbox ── -->
    <transition name="lb-fade">
      <div v-if="lightboxSrc" class="lightbox" @click.self="closeLightbox">
        <div class="lightbox__box">
          <button class="lightbox__close" @click="closeLightbox">
            <i class="fas fa-xmark"></i>
          </button>
          <img :src="lightboxSrc" class="lightbox__img" alt="Voucher Image" />
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import { Head } from '@inertiajs/vue3';

const GRADIENTS = [
  'linear-gradient(135deg,#6366f1,#4f46e5)',
  'linear-gradient(135deg,#10b981,#059669)',
  'linear-gradient(135deg,#f59e0b,#d97706)',
  'linear-gradient(135deg,#ef4444,#dc2626)',
  'linear-gradient(135deg,#8b5cf6,#7c3aed)',
  'linear-gradient(135deg,#14b8a6,#0d9488)',
  'linear-gradient(135deg,#ec4899,#db2777)',
  'linear-gradient(135deg,#3b82f6,#2563eb)',
];

export default {
  name: 'MemberDetailPage',
  components: { Head },
  props: {
    memberId: { type: [String, Number], required: true },
  },
  data() {
    return {
      loading:     true,
      notFound:    false,
      member:      null,
      stats:       { total_deposit: 0, total_share: 0, total_savings: 0, total_due: 0, deposit_count: 0, paid_till: null, last_adjustment: null },
      deposits:    [],
      lightboxSrc: null,
    };
  },
  computed: {
    avatarGradient() {
      if (!this.member) return GRADIENTS[0];
      return GRADIENTS[(this.member.id || 0) % GRADIENTS.length];
    },
  },
  async created() {
    try {
      const res = await axios.get(`${location.origin}/api/public/member/${this.memberId}/detail`);
      const d   = res.data.data;
      this.member   = d.member;
      this.stats    = d.stats;
      this.deposits = d.deposits;
    } catch (e) {
      if (e.response?.status === 404) this.notFound = true;
    } finally {
      this.loading = false;
    }
  },
  mounted() {
    document.addEventListener('keydown', this._onKey);
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this._onKey);
  },
  methods: {
    openLightbox(src) { this.lightboxSrc = src; document.body.style.overflow = 'hidden'; },
    imgUrl(path) {
      if (!path) return null;
      if (/^https?:\/\//.test(path) || path.startsWith('/')) return path;
      return '/' + path;
    },
    closeLightbox()   { this.lightboxSrc = null; document.body.style.overflow = ''; },
    _onKey(e)         { if (e.key === 'Escape') this.closeLightbox(); },
    fmt(v) {
      return Number(v || 0).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    fmtDate(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('en-BD', { year: 'numeric', month: 'short', day: 'numeric' });
    },
    fmtMonth(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('en-BD', { year: 'numeric', month: 'long' });
    },
    isAdvancePaid(paidTill) {
      if (!paidTill) return false;
      const now = new Date();
      const currentYM = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
      const paidYM = String(paidTill).slice(0, 7);
      return paidYM > currentYM;
    },
    initials(n) {
      return (n || '?').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    },
    stripHtml(html) {
      if (!html) return '';
      const text = String(html).replace(/<[^>]*>/g, ' ').replace(/&nbsp;/gi, ' ').replace(/\s+/g, ' ').trim();
      return text;
    },
  },
};
</script>

<style scoped>
/* ── Base ── */
.md-page { min-height: 100vh; background: #080c18; color: #cbd5e1; font-size: 14px; }

/* ── Page nav bar ── */
.page-nav-bar {
  background: rgba(8,12,24,0.9);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,0.06);
  position: sticky; top: 62px; z-index: 100;
}
.page-nav-bar__inner {
  max-width: 1280px; margin: 0 auto;
  padding: 0 24px;
  display: flex; align-items: center; justify-content: space-between;
  height: 52px; gap: 16px;
}
.back-btn {
  display: inline-flex; align-items: center; gap: 7px;
  color: #64748b; font-size: 13px; font-weight: 600;
  text-decoration: none; padding: 6px 14px;
  border: 1px solid rgba(255,255,255,0.08); border-radius: 8px;
  background: rgba(255,255,255,0.04); transition: all 0.2s;
  white-space: nowrap;
}
.back-btn:hover { color: #a5b4fc; border-color: rgba(99,102,241,0.4); background: rgba(99,102,241,0.08); }
.page-tabs { display: flex; gap: 4px; }
.ptab {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 8px;
  font-size: 12.5px; font-weight: 600; color: #475569;
  text-decoration: none; transition: all 0.2s; white-space: nowrap;
}
.ptab:hover { color: #94a3b8; background: rgba(255,255,255,0.05); }
.ptab i { font-size: 11px; }
@media (max-width: 768px) { .ptab span { display: none; } .ptab { padding: 7px 10px; } }
@media (max-width: 480px) { .page-tabs { gap: 2px; } }

/* ── Wrap ── */
.md-wrap { max-width: 1280px; margin: 0 auto; padding: 32px 24px 64px; }

/* ── Skeletons ── */
@keyframes shimmer {
  0%   { background-position: -600px 0; }
  100% { background-position: 600px 0; }
}
.skel-profile {
  display: flex; align-items: flex-start; gap: 28px;
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
  border-radius: 20px; padding: 32px; margin-bottom: 28px;
}
.skel-avatar {
  width: 100px; height: 100px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(90deg,#1e2535 0%,#2a3350 50%,#1e2535 100%);
  background-size: 600px 100%; animation: shimmer 1.4s infinite;
}
.skel-lines { flex: 1; display: flex; flex-direction: column; gap: 12px; }
.skel-line {
  border-radius: 8px; height: 16px;
  background: linear-gradient(90deg,#1e2535 0%,#2a3350 50%,#1e2535 100%);
  background-size: 600px 100%; animation: shimmer 1.4s infinite;
}
.skel-line--lg { width: 220px; height: 24px; }
.skel-line--md { width: 150px; }
.skel-stats-row { display: flex; gap: 12px; margin-top: 8px; }
.skel-stat {
  flex: 1; height: 60px; border-radius: 12px;
  background: linear-gradient(90deg,#1e2535 0%,#2a3350 50%,#1e2535 100%);
  background-size: 600px 100%; animation: shimmer 1.4s infinite;
}
.skel-table { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 20px; }
.skel-th {
  height: 38px; border-radius: 8px; margin-bottom: 12px;
  background: linear-gradient(90deg,#1e2535 0%,#2a3350 50%,#1e2535 100%);
  background-size: 600px 100%; animation: shimmer 1.4s infinite;
}
.skel-row { display: flex; gap: 10px; margin-bottom: 10px; }
.skel-cell {
  flex: 1; height: 36px; border-radius: 6px;
  background: linear-gradient(90deg,#1e2535 0%,#2a3350 50%,#1e2535 100%);
  background-size: 600px 100%; animation: shimmer 1.4s infinite;
}

/* ── Not found ── */
.not-found {
  text-align: center; padding: 80px 20px; color: #475569;
}
.not-found i { font-size: 48px; margin-bottom: 16px; display: block; color: #334155; }
.not-found p { font-size: 16px; margin-bottom: 24px; }

/* ── Profile card ── */
.profile-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 20px; padding: 32px 36px;
  display: flex; align-items: flex-start; gap: 28px;
  margin-bottom: 28px; position: relative; overflow: hidden;
}
.profile-card::before {
  content: ''; position: absolute;
  top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, #6366f1, #8b5cf6, #34d399, #14b8a6);
}
.profile-avatar {
  width: 96px; height: 96px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; font-weight: 800; color: #fff;
  overflow: hidden; border: 3px solid rgba(99,102,241,0.4);
  box-shadow: 0 0 32px rgba(99,102,241,0.25);
}
.profile-avatar img { width: 100%; height: 100%; object-fit: cover; }

.profile-card__info { flex: 1; }
.profile-card__badge {
  display: inline-block; padding: 3px 12px;
  background: rgba(52,211,153,0.12); border: 1px solid rgba(52,211,153,0.3);
  border-radius: 20px; font-size: 11px; font-weight: 700;
  color: #34d399; letter-spacing: 1px; text-transform: uppercase;
  margin-bottom: 8px;
}
.profile-card__name {
  font-size: 26px; font-weight: 800; color: #f1f5f9; margin: 0 0 10px;
  background: linear-gradient(135deg, #e0e7ff, #a5b4fc);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.profile-card__meta {
  display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;
}
.profile-card__meta span {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12.5px; color: #475569;
}
.profile-card__meta i { font-size: 11px; color: #334155; }

.profile-card__stats { display: flex; flex-wrap: wrap; gap: 12px; }
.ps-item {
  flex: 1; min-width: 130px;
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px; padding: 12px 16px;
  display: flex; flex-direction: column; gap: 4px;
}
.ps-item__label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.7px; color: #334155; }
.ps-item__val   { font-size: 16px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; }
.share-trend-inline {
  display: inline-flex; align-items: center; gap: 3px;
  padding: 2px 8px; border-radius: 10px;
  font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
}
.share-trend-inline i { font-size: 9px; }
.share-trend-inline--up   { background: #10b981; color: #fff; }
.share-trend-inline--down { background: #f87171; color: #fff; }
.ps-item__icon { font-size: 13px; color: #334155; margin-bottom: 2px; }
.ps-item--indigo { border-color: rgba(99,102,241,0.25); background: rgba(99,102,241,0.06); }
.ps-item--indigo .ps-item__val  { color: #a5b4fc; }
.ps-item--indigo .ps-item__icon { color: #6366f1; }
.ps-item--blue   { border-color: rgba(59,130,246,0.2);  }
.ps-item--blue   .ps-item__val  { color: #60a5fa; }
.ps-item--blue   .ps-item__icon { color: #3b82f6; }
.ps-item--purple { border-color: rgba(139,92,246,0.2); }
.ps-item--purple .ps-item__val  { color: #a78bfa; }
.ps-item--purple .ps-item__icon { color: #8b5cf6; }
.ps-item--green  { border-color: rgba(16,185,129,0.2); }
.ps-item--green  .ps-item__val  { color: #34d399; }
.ps-item--green  .ps-item__icon { color: #10b981; }
.ps-item--red    { border-color: rgba(239,68,68,0.2);  }
.ps-item--red    .ps-item__val  { color: #f87171; }
.ps-item--red    .ps-item__icon { color: #ef4444; }
.ps-item--teal   { border-color: rgba(20,184,166,0.2); }
.ps-item--teal   .ps-item__val  { color: #2dd4bf; }
.ps-item--teal   .ps-item__icon { color: #14b8a6; }

.profile-card__badge-count {
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  flex-shrink: 0;
}
.count-ring {
  width: 64px; height: 64px; border-radius: 50%;
  background: rgba(99,102,241,0.12);
  border: 2px solid rgba(99,102,241,0.35);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; font-weight: 900; color: #a5b4fc;
}
.count-label { font-size: 11px; color: #334155; text-transform: uppercase; letter-spacing: 0.8px; }

/* ── Deposits section ── */
.deposits-section {
  background: rgba(255,255,255,0.02);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 20px; overflow: hidden;
}
.deposits-section__header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 22px 28px; border-bottom: 1px solid rgba(255,255,255,0.05);
}
.deposits-section__title {
  font-size: 16px; font-weight: 700; color: #e2e8f0; margin: 0;
  display: flex; align-items: center; gap: 10px;
}
.deposits-section__title i { color: #6366f1; font-size: 14px; }
.deposits-section__count {
  font-size: 12px; font-weight: 700; color: #34d399;
  background: rgba(52,211,153,0.12); padding: 4px 12px; border-radius: 20px;
}

.empty-state {
  text-align: center; padding: 60px 20px; color: #334155;
}
.empty-state i { font-size: 36px; display: block; margin-bottom: 12px; }

/* ── Table ── */
.table-wrap { overflow-x: auto; }
.dep-table {
  width: 100%; border-collapse: collapse;
  font-size: 13px; color: #94a3b8;
  background: transparent !important;
}
.dep-table th {
  padding: 11px 14px;
  font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px;
  color: #334155; font-weight: 700; text-align: left;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  background: rgba(255,255,255,0.03) !important;
  white-space: nowrap;
}
.dep-table td {
  padding: 12px 14px;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  background: transparent !important;
  vertical-align: middle;
  color: #cbd5e1 !important;
}
.dep-table tbody tr:hover td { background: rgba(99,102,241,0.04) !important; }
.dep-table tfoot .dep-foot td {
  background: rgba(99,102,241,0.06) !important;
  border-top: 1px solid rgba(99,102,241,0.2);
  color: #a5b4fc; font-size: 13px;
}

.dep-table .td-num    { color: #64748b !important; font-weight: 700; width: 40px; }
.dep-table .td-mono   { font-family: monospace; font-size: 12px; color: #94a3b8 !important; }
.dep-table .td-amount { color: #60a5fa !important; font-weight: 700; white-space: nowrap; }
.dep-table .td-date   { color: #cbd5e1 !important; white-space: nowrap; }
.dep-table .td-method { color: #94a3b8 !important; }
.dep-table .td-note   { color: #94a3b8 !important; font-size: 12px; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.type-badge {
  display: inline-block; padding: 3px 10px; border-radius: 20px;
  font-size: 11px; font-weight: 700; white-space: nowrap;
}
.type-badge--purple { background: rgba(139,92,246,0.12); color: #a78bfa; border: 1px solid rgba(139,92,246,0.25); }
.type-badge--green  { background: rgba(16,185,129,0.12);  color: #34d399; border: 1px solid rgba(16,185,129,0.25); }

/* ── Voucher thumbnail ── */
.td-img { width: 60px; }
.voucher-thumb-btn {
  position: relative; display: inline-block;
  width: 48px; height: 48px; border-radius: 8px;
  overflow: hidden; cursor: pointer;
  border: 1px solid rgba(99,102,241,0.3);
  background: rgba(255,255,255,0.05);
  padding: 0; transition: all 0.2s;
}
.voucher-thumb-btn:hover { border-color: rgba(99,102,241,0.7); transform: scale(1.08); }
.voucher-thumb {
  width: 100%; height: 100%; object-fit: cover; display: block;
}
.voucher-thumb-overlay {
  position: absolute; inset: 0;
  background: rgba(0,0,0,0.55);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity 0.2s; color: #fff; font-size: 14px;
}
.voucher-thumb-btn:hover .voucher-thumb-overlay { opacity: 1; }
.no-img { color: #334155; font-size: 12px; }

/* ── Lightbox ── */
.lightbox {
  position: fixed; inset: 0; z-index: 9000;
  background: rgba(0,0,0,0.88);
  backdrop-filter: blur(12px);
  display: flex; align-items: center; justify-content: center;
  padding: 24px;
}
.lightbox__box {
  position: relative; max-width: 90vw; max-height: 90vh;
  display: flex; align-items: center; justify-content: center;
}
.lightbox__img {
  max-width: 100%; max-height: 85vh;
  border-radius: 12px;
  box-shadow: 0 32px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.08);
  display: block;
}
.lightbox__close {
  position: absolute; top: -14px; right: -14px;
  width: 36px; height: 36px; border-radius: 50%;
  background: #0f172a; border: 1px solid rgba(255,255,255,0.12);
  color: #94a3b8; font-size: 16px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s; z-index: 1;
}
.lightbox__close:hover { background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.4); }

/* Lightbox transition */
.lb-fade-enter-active { animation: lbIn  0.2s ease; }
.lb-fade-leave-active { animation: lbOut 0.15s ease; }
@keyframes lbIn  { from { opacity: 0; } to { opacity: 1; } }
@keyframes lbOut { from { opacity: 1; } to { opacity: 0; } }

/* ── Responsive ── */
@media (max-width: 768px) {
  .profile-card { flex-direction: column; gap: 20px; padding: 24px 20px; }
  .profile-card__badge-count { flex-direction: row; }
  .md-wrap { padding: 20px 16px 48px; }
  .deposits-section__header { padding: 16px 20px; }
  .dep-table th, .dep-table td { padding: 9px 10px; }
  .page-nav-bar__inner { padding: 0 16px; }
}
@media (max-width: 540px) {
  .profile-card__name { font-size: 20px; }
  .ps-item { min-width: 100%; }
}
</style>
