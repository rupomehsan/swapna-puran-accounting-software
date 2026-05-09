<template>
  <div>
    <Head :title="siteName + ' — Member Portal'" />

    <!-- ═══════════════════════════════════════════
         HERO BANNER
    ════════════════════════════════════════════════ -->
    <section class="hero">
      <div class="hero__bg"></div>
      <div class="hero__overlay"></div>

      <div class="hero__content">
        <!-- Logo + identity -->
        <div class="hero__identity">
          <div class="hero__logo-ring">
            <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="hero__logo-img" />
            <span v-else class="hero__logo-text">শপ</span>
          </div>
          <h1 class="hero__name">{{ siteName }}</h1>
          <p class="hero__slogan">সঞ্চয় · সমৃদ্ধি · সহযোগিতা</p>
        </div>

        <!-- Stats grid -->
        <div class="hero__stats">
          <div class="stat-card stat-card--green">
            <i class="stat-card__icon">💰</i>
            <span class="stat-card__label">Net Fund</span>
            <span class="stat-card__value">৳ {{ fmt(org.net_fund) }}</span>
          </div>
          <div class="stat-card stat-card--blue">
            <i class="stat-card__icon">📥</i>
            <span class="stat-card__label">Total Deposits</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_deposits) }}</span>
          </div>
          <div class="stat-card stat-card--purple">
            <i class="stat-card__icon">📈</i>
            <span class="stat-card__label">Total Income</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_income) }}</span>
          </div>
          <div class="stat-card stat-card--orange">
            <i class="stat-card__icon">📉</i>
            <span class="stat-card__label">Total Expense</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_expense) }}</span>
          </div>
          <div class="stat-card stat-card--red">
            <i class="stat-card__icon">📤</i>
            <span class="stat-card__label">Withdrawals</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_withdrawals) }}</span>
          </div>
          <div class="stat-card stat-card--teal">
            <i class="stat-card__icon">👥</i>
            <span class="stat-card__label">Members</span>
            <span class="stat-card__value">{{ org.total_members }}</span>
          </div>
        </div>
      </div>

      <!-- Wave bottom edge -->
      <div class="hero__wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="rgba(16,185,129,0.25)" />
          <path d="M0,50 C480,10 960,70 1440,30 L1440,80 L0,80 Z" fill="rgba(99,102,241,0.30)" />
          <path d="M0,60 C300,20 900,80 1440,45 L1440,80 L0,80 Z" fill="#0d1320" />
        </svg>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         MEMBER LIST
    ════════════════════════════════════════════════ -->
    <section class="members">
      <div class="members__wrap">
        <!-- Section heading -->
        <div class="members__heading">
          <div class="members__heading-left">
            <h2 class="members__title">Member Overview</h2>
            <span class="members__count">{{ members.length }} active members</span>
          </div>
          <div class="members__menu">
            <a href="/admin/transaction-log" class="members__menu-link"><i class="fas fa-history"></i> Transaction Log</a>
            <a href="/admin/income" class="members__menu-link"><i class="fas fa-arrow-down"></i> Income</a>
            <a href="/admin/expense" class="members__menu-link"><i class="fas fa-arrow-up"></i> Expense</a>
            <a href="/admin/balance-sheet" class="members__menu-link"><i class="fas fa-file-invoice-dollar"></i> Balance Sheet</a>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="members__loading">
          <div class="sp-spinner"></div>
          <span>Loading…</span>
        </div>

        <!-- Member cards -->
        <div v-else>
          <div v-for="(m, idx) in members" :key="m.id">
            <!-- Row -->
            <div class="mrow" :class="{ 'mrow--open': expandedId === m.id }">
              <div class="mrow__avatar" :style="{ background: avatarColor(idx) }">
                <img v-if="m.image" :src="m.image" :alt="m.name" @error="(e) => (e.target.style.display = 'none')" />
                <span v-else>{{ initials(m.name) }}</span>
              </div>

              <div class="mrow__info">
                <div class="mrow__name">{{ m.name }}</div>
                <div class="mrow__txn">{{ m.deposit_count }} deposit{{ m.deposit_count !== 1 ? "s" : "" }}</div>
              </div>

              <div class="mrow__pills">
                <span class="pill pill--blue">Total ৳{{ fmt(m.total_deposit) }}</span>
                <span class="pill pill--purple">Share ৳{{ fmt(m.total_share) }}</span>
                <span class="pill pill--green">Extra ৳{{ fmt(m.total_savings) }}</span>
                <span class="pill" :class="m.total_due > 0 ? 'pill--red' : 'pill--clear'">
                  {{ m.total_due > 0 ? "Due ৳" + fmt(m.total_due) : "✓ Clear" }}
                </span>
              </div>

              <div class="mrow__right">
                <span class="mrow__rank" :class="'rank-' + Math.min(idx + 1, 4)">#{{ idx + 1 }}</span>
                <button class="mrow__btn" :class="{ 'mrow__btn--open': expandedId === m.id }" @click="toggleDetails(m)">
                  <span v-if="loadingMemberId === m.id" class="sp-spinner sp-spinner--sm"></span>
                  <span v-else>{{ expandedId === m.id ? "▲ Close" : "▼ Details" }}</span>
                </button>
              </div>
            </div>

            <!-- Deposit history panel -->
            <div v-if="expandedId === m.id" class="history">
              <div class="history__title">📋 Deposit History — {{ m.name }}</div>
              <div v-if="!depositHistory.length" class="history__empty">No deposits found.</div>
              <div v-else class="history__table-wrap">
                <table class="history__table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Voucher</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>For Month</th>
                      <th>Payment Date</th>
                      <th>Method</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(d, di) in depositHistory" :key="di">
                      <td>{{ di + 1 }}</td>
                      <td class="mono">{{ d.voucher_no || "—" }}</td>
                      <td>
                        <span class="pill pill--sm" :class="d.deposit_type === 'share_deposit' ? 'pill--purple' : 'pill--green'">
                          {{ d.deposit_type === "share_deposit" ? "Share" : "Extra" }}
                        </span>
                      </td>
                      <td class="amount">৳ {{ fmt(d.amount) }}</td>
                      <td>{{ fmtMonth(d.for_month) }}</td>
                      <td>{{ fmtDate(d.payment_date) }}</td>
                      <td>{{ d.payment_method || "—" }}</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr class="history__total">
                      <td colspan="3"><strong>Total</strong></td>
                      <td class="amount">
                        <strong>৳ {{ fmt(depositHistory.reduce((s, d) => s + parseFloat(d.amount || 0), 0)) }}</strong>
                      </td>
                      <td colspan="3"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Grand total footer row -->
          <div v-if="members.length" class="members__total">
            <span class="members__total-label">Grand Totals:</span>
            <span class="pill pill--blue">Deposits ৳{{ fmt(org.total_deposits) }}</span>
            <span class="pill pill--purple">Share ৳{{ fmt(totalShare) }}</span>
            <span class="pill pill--green">Extra ৳{{ fmt(totalSavings) }}</span>
            <span class="pill pill--red">Due ৳{{ fmt(totalDue) }}</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import { site_settings_store } from "../../GlobalStore/site_settings_store";

const COLORS = [
  "linear-gradient(135deg,#6366f1,#4f46e5)",
  "linear-gradient(135deg,#10b981,#059669)",
  "linear-gradient(135deg,#f59e0b,#d97706)",
  "linear-gradient(135deg,#ef4444,#dc2626)",
  "linear-gradient(135deg,#8b5cf6,#7c3aed)",
  "linear-gradient(135deg,#14b8a6,#0d9488)",
  "linear-gradient(135deg,#ec4899,#db2777)",
  "linear-gradient(135deg,#3b82f6,#2563eb)",
];

export default {
  name: "HomePage",
  components: { Head },
  data() {
    return {
      loading: true,
      org: { net_fund: 0, total_deposits: 0, total_withdrawals: 0, total_income: 0, total_expense: 0, total_members: 0 },
      members: [],
      expandedId: null,
      depositHistory: [],
      loadingMemberId: null,
    };
  },
  computed: {
    siteName() {
      return site_settings_store().get_setting_value("site_name") || "স্বপ্নপূরণ";
    },
    siteLogo() {
      return site_settings_store().get_setting_value("image") || null;
    },
    totalShare() {
      return this.members.reduce((s, m) => s + parseFloat(m.total_share || 0), 0);
    },
    totalSavings() {
      return this.members.reduce((s, m) => s + parseFloat(m.total_savings || 0), 0);
    },
    totalDue() {
      return this.members.reduce((s, m) => s + parseFloat(m.total_due || 0), 0);
    },
  },
  async mounted() {
    // Add scroll animation observer
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
          }
        });
      },
      { threshold: 0.1, rootMargin: "0px 0px -50px 0px" },
    );

    setTimeout(() => {
      document.querySelectorAll(".animate-on-scroll, .section-title, .section-sub, .service-card, .about-stat, .tcard").forEach((el) => {
        el.classList.add("animate-on-scroll");
        observer.observe(el);
      });
    }, 100);
  },
  async created() {
    site_settings_store().get_all_website_settings();
    try {
      const res = await axios.get(`${location.origin}/api/public/summary`);
      this.org = res.data.data.org;
      this.members = res.data.data.members;
    } catch (e) {
      console.error(e);
    } finally {
      this.loading = false;
    }
  },
  methods: {
    async toggleDetails(m) {
      if (this.expandedId === m.id) {
        this.expandedId = null;
        this.depositHistory = [];
        return;
      }
      this.loadingMemberId = m.id;
      this.expandedId = null;
      this.depositHistory = [];
      try {
        const res = await axios.get(`${location.origin}/api/public/member/${m.id}/deposits`);
        this.depositHistory = res.data.data.deposits;
        this.expandedId = m.id;
      } catch (e) {
        console.error(e);
      } finally {
        this.loadingMemberId = null;
      }
    },
    fmt(v) {
      return Number(v || 0).toLocaleString("en-BD", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    fmtDate(d) {
      if (!d) return "—";
      return new Date(d).toLocaleDateString("en-BD", { year: "numeric", month: "short", day: "numeric" });
    },
    fmtMonth(d) {
      if (!d) return "—";
      return new Date(d).toLocaleDateString("en-BD", { year: "numeric", month: "long" });
    },
    initials(n) {
      return (n || "?")
        .split(" ")
        .map((w) => w[0])
        .slice(0, 2)
        .join("")
        .toUpperCase();
    },
    avatarColor(i) {
      return COLORS[i % COLORS.length];
    },
  },
};
</script>

<style scoped>
/* ─── Reset ─────────────────────────────────────────────────── */
*,
*::before,
*::after {
  box-sizing: border-box;
}

/* ─── HERO ───────────────────────────────────────────────────── */
.hero {
  position: relative;
  width: 100%;
  padding: 60px 24px 0;
  background:
    linear-gradient(160deg, rgba(5, 8, 20, 0.82) 0%, rgba(10, 18, 45, 0.76) 100%),
    url("/frontend/asset/img/home_page/banner.png") center/cover no-repeat;
  color: #e2e8f0;
}

.hero__bg,
.hero__overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.hero__overlay {
  background:
    radial-gradient(ellipse at 10% 50%, rgba(99, 102, 241, 0.18) 0%, transparent 55%),
    radial-gradient(ellipse at 90% 50%, rgba(16, 185, 129, 0.13) 0%, transparent 55%);
}

.hero__content {
  position: relative;
  z-index: 1;
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 48px;
  padding-bottom: 80px;
}

/* Identity */
.hero__identity {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  min-width: 170px;
}
.hero__logo-ring {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  border: 3px solid rgba(99, 102, 241, 0.55);
  box-shadow: 0 0 40px rgba(99, 102, 241, 0.28);
  overflow: hidden;
  background: rgba(255, 255, 255, 0.07);
  display: flex;
  align-items: center;
  justify-content: center;
}
.hero__logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.hero__logo-text {
  font-size: 36px;
  font-weight: 800;
  color: #a5b4fc;
}
.hero__name {
  font-size: 24px;
  font-weight: 800;
  text-align: center;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero__slogan {
  font-size: 16px;
  color: #eaeaea;
  letter-spacing: 1px;
  font-weight: bolder;
}

/* Stats grid */
.hero__stats {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 18px;
  padding: 26px 22px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  backdrop-filter: blur(14px);
  transition: transform 0.2s;
}
.stat-card:hover {
  transform: translateY(-3px);
}
.stat-card__icon {
  font-size: 26px;
  font-style: normal;
}
.stat-card__label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #64748b;
}
.stat-card__value {
  font-size: 22px;
  font-weight: 800;
}

.stat-card--green {
  border-color: rgba(16, 185, 129, 0.4);
}
.stat-card--green .stat-card__value {
  color: #34d399;
}
.stat-card--blue {
  border-color: rgba(59, 130, 246, 0.4);
}
.stat-card--blue .stat-card__value {
  color: #60a5fa;
}
.stat-card--purple {
  border-color: rgba(139, 92, 246, 0.4);
}
.stat-card--purple .stat-card__value {
  color: #a78bfa;
}
.stat-card--orange {
  border-color: rgba(245, 158, 11, 0.4);
}
.stat-card--orange .stat-card__value {
  color: #fbbf24;
}
.stat-card--red {
  border-color: rgba(239, 68, 68, 0.4);
}
.stat-card--red .stat-card__value {
  color: #f87171;
}
.stat-card--teal {
  border-color: rgba(20, 184, 166, 0.4);
}
.stat-card--teal .stat-card__value {
  color: #2dd4bf;
}

/* Wave */
.hero__wave {
  position: relative;
  width: 100%;
  line-height: 0;
}
.hero__wave svg {
  display: block;
  width: 100%;
  height: 80px;
}

/* ─── MEMBERS ────────────────────────────────────────────────── */
.members {
  background: #0d1320;
  padding: 48px 24px 64px;
}
.members__wrap {
  max-width: 1280px;
  margin: 0 auto;
}
.members__heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  background: rgba(255, 255, 255, 0.02);
  padding: 20px 24px;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  flex-wrap: wrap;
  gap: 20px;
}
.members__heading-left {
  display: flex;
  align-items: center;
  gap: 14px;
}
.members__title {
  font-size: 24px;
  font-weight: 800;
  color: #f1f5f9;
  margin: 0;
}
.members__count {
  font-size: 13px;
  color: #34d399;
  background: rgba(52, 211, 153, 0.15);
  padding: 5px 12px;
  border-radius: 20px;
  font-weight: 700;
}
.members__menu {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.members__menu-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 16px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.members__menu-link i {
  color: #a5b4fc;
}
.members__menu-link:hover {
  background: rgba(99, 102, 241, 0.15);
  border-color: rgba(99, 102, 241, 0.4);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}
.members__loading {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #64748b;
  padding: 40px 0;
}

/* Member row */
.mrow {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 20px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  margin-bottom: 10px;
  transition:
    border-color 0.2s,
    background 0.2s;
}
.mrow:hover,
.mrow--open {
  border-color: rgba(99, 102, 241, 0.35);
  background: rgba(99, 102, 241, 0.05);
}

.mrow__avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  font-weight: 700;
  color: #fff;
  overflow: hidden;
}
.mrow__avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.mrow__info {
  min-width: 150px;
  flex-shrink: 0;
}
.mrow__name {
  font-size: 15px;
  font-weight: 600;
  color: #e2e8f0;
}
.mrow__txn {
  font-size: 12px;
  color: #475569;
  margin-top: 2px;
}

.mrow__pills {
  display: flex;
  flex-wrap: wrap;
  gap: 7px;
  flex: 1;
}

.mrow__right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.mrow__rank {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 30px;
}
.rank-1 {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #fff;
}
.rank-2 {
  background: linear-gradient(135deg, #94a3b8, #64748b);
  color: #fff;
}
.rank-3 {
  background: linear-gradient(135deg, #b45309, #92400e);
  color: #fff;
}
.rank-4 {
  background: rgba(99, 102, 241, 0.15);
  color: #a5b4fc;
  border: 1px solid rgba(99, 102, 241, 0.3);
}

.mrow__btn {
  background: rgba(99, 102, 241, 0.12);
  border: 1px solid rgba(99, 102, 241, 0.3);
  color: #a5b4fc;
  border-radius: 8px;
  padding: 7px 16px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: background 0.2s;
}
.mrow__btn:hover,
.mrow__btn--open {
  background: rgba(99, 102, 241, 0.28);
  border-color: rgba(99, 102, 241, 0.6);
}

/* Deposit history panel */
.history {
  background: rgba(16, 185, 129, 0.05);
  border: 1px solid rgba(16, 185, 129, 0.18);
  border-radius: 12px;
  padding: 20px 22px 24px;
  margin-bottom: 10px;
  margin-top: -4px;
}
.history__title {
  font-size: 14px;
  font-weight: 600;
  color: #34d399;
  margin-bottom: 16px;
}
.history__empty {
  color: #475569;
  font-size: 13px;
  padding: 12px 0;
}
.history__table-wrap {
  overflow-x: auto;
}
.history__table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  color: #cbd5e1;
}
.history__table th {
  padding: 9px 13px;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(16, 185, 129, 0.08);
}
.history__table td {
  padding: 10px 13px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}
.history__table tbody tr:hover {
  background: rgba(255, 255, 255, 0.02);
}
.history__total td {
  background: rgba(255, 255, 255, 0.04);
  border-top: 2px solid rgba(16, 185, 129, 0.3);
}
.mono {
  font-family: monospace;
  font-size: 12px;
  color: #94a3b8;
}
.amount {
  color: #60a5fa;
  font-weight: 600;
}

/* Grand total bar */
.members__total {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  padding: 18px 20px;
  background: rgba(99, 102, 241, 0.06);
  border: 1px solid rgba(99, 102, 241, 0.18);
  border-radius: 12px;
  margin-top: 20px;
}
.members__total-label {
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
}

/* ─── Pills ──────────────────────────────────────────────────── */
.pill {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 30px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}
.pill--sm {
  padding: 2px 9px;
  font-size: 11px;
}
.pill--blue {
  background: rgba(59, 130, 246, 0.12);
  color: #60a5fa;
  border: 1px solid rgba(59, 130, 246, 0.25);
}
.pill--purple {
  background: rgba(139, 92, 246, 0.12);
  color: #a78bfa;
  border: 1px solid rgba(139, 92, 246, 0.25);
}
.pill--green {
  background: rgba(16, 185, 129, 0.12);
  color: #34d399;
  border: 1px solid rgba(16, 185, 129, 0.25);
}
.pill--red {
  background: rgba(239, 68, 68, 0.1);
  color: #f87171;
  border: 1px solid rgba(239, 68, 68, 0.22);
}
.pill--clear {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.22);
}

/* ─── Spinner ────────────────────────────────────────────────── */
.sp-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(99, 102, 241, 0.25);
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
.sp-spinner--sm {
  width: 12px;
  height: 12px;
  border-width: 2px;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ─── Footer ─────────────────────────────────────────────────── */
.site-footer {
  background: #080c18;
  border-top: 1px solid rgba(255, 255, 255, 0.07);
  color: #94a3b8;
  font-size: 13px;
  flex-shrink: 0;
}

.footer__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 56px 24px 40px;
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 60px;
}

/* About column */
.footer__logo-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
}
.footer__logo-ring {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 2px solid rgba(99, 102, 241, 0.45);
  overflow: hidden;
  background: rgba(255, 255, 255, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.footer__logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.footer__logo-text {
  font-size: 18px;
  font-weight: 800;
  color: #a5b4fc;
}
.footer__brand {
  font-size: 18px;
  font-weight: 700;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.footer__desc {
  line-height: 1.75;
  color: #64748b;
  font-size: 13.5px;
  max-width: 480px;
  margin-bottom: 14px;
}
.footer__tagline {
  font-size: 11px;
  letter-spacing: 1.5px;
  color: rgba(99, 102, 241, 0.6);
  text-transform: uppercase;
}

/* Stats column */
.footer__section-title {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #475569;
  margin-bottom: 18px;
}
.footer__stat-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.footer__stat-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}
.footer__stat-label {
  color: #64748b;
  font-size: 13px;
}
.footer__stat-value {
  font-size: 13px;
  font-weight: 700;
}
.footer__stat-value.teal {
  color: #2dd4bf;
}
.footer__stat-value.blue {
  color: #60a5fa;
}
.footer__stat-value.green {
  color: #34d399;
}
.footer__stat-value.purple {
  color: #a78bfa;
}

/* Bottom bar */
.footer__bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  text-align: center;
  padding: 20px 24px;
  color: #334155;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.footer__bottom-sep {
  color: rgba(255, 255, 255, 0.12);
}

@media (max-width: 760px) {
  .footer__inner {
    grid-template-columns: 1fr;
    gap: 36px;
    padding: 40px 20px 28px;
  }
}

/* ─── Responsive ─────────────────────────────────────────────── */
@media (max-width: 900px) {
  .hero__content {
    flex-direction: column;
    gap: 32px;
    padding-bottom: 60px;
  }
  .hero__stats {
    grid-template-columns: repeat(3, 1fr);
    width: 100%;
  }
  .mrow {
    flex-wrap: wrap;
  }
  .mrow__info {
    min-width: auto;
  }
}
@media (max-width: 560px) {
  .hero__stats {
    grid-template-columns: repeat(2, 1fr);
  }
  .mrow__pills {
    gap: 5px;
  }
  .hero {
    padding: 40px 16px 0;
  }
  .members {
    padding: 32px 16px 48px;
  }
}
</style>
