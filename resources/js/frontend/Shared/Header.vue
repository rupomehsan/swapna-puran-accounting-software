<template>
  <header class="site-header" :class="{ 'site-header--scrolled': scrolled }">

    <!-- Animated rainbow top line -->
    <div class="site-header__rainbow"></div>

    <!-- Scroll-progress bar at bottom of header -->
    <div class="site-header__progress" :style="{ width: scrollProgress + '%' }"></div>

    <div class="site-header__inner">

      <!-- ── Brand (left) ── -->
      <div class="site-header__left">
        <a href="/" class="site-header__brand">
          <div class="site-header__logo-ring">
            <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="site-header__logo-img" />
            <span v-else class="site-header__logo-text">শপ</span>
          </div>
        </a>
      </div>

      <!-- ── Nav links (center) ── -->
      <nav class="site-header__nav">
        <a href="/" class="hdr-nav-link" :class="{ 'hdr-nav-link--active': activeSection === 'top' }" @click.prevent="scrollTo('top')">
          <i class="fas fa-house"></i><span>Home</span>
        </a>
        <a href="#home" class="hdr-nav-link" :class="{ 'hdr-nav-link--active': activeSection === 'home' }" @click.prevent="scrollTo('home')">
          <i class="fas fa-users"></i><span>Members</span>
        </a>
        <a href="#about" class="hdr-nav-link" :class="{ 'hdr-nav-link--active': activeSection === 'about' }" @click.prevent="scrollTo('about')">
          <i class="fas fa-circle-info"></i><span>About</span>
        </a>
        <a href="#objectives" class="hdr-nav-link" :class="{ 'hdr-nav-link--active': activeSection === 'objectives' }" @click.prevent="scrollTo('objectives')">
          <i class="fas fa-bullseye"></i><span>Objectives</span>
        </a>
        <a href="#terms" class="hdr-nav-link" :class="{ 'hdr-nav-link--active': activeSection === 'terms' }" @click.prevent="scrollTo('terms')">
          <i class="fas fa-file-contract"></i><span>Terms &amp; Condition</span>
        </a>
        <a href="#contact" class="hdr-nav-link" :class="{ 'hdr-nav-link--active': activeSection === 'contact' }" @click.prevent="scrollTo('contact')">
          <i class="fas fa-envelope"></i><span>Contact</span>
        </a>
      </nav>

      <!-- ── Actions (right) ── -->
      <div class="site-header__right">
      <div class="site-header__actions">

        <!-- Authenticated: user dropdown -->
        <template v-if="is_auth">
          <div class="site-header__user-wrap" ref="userWrap">
            <button class="site-header__user" @click="dropdownOpen = !dropdownOpen">
              <div class="site-header__avatar site-header__avatar--text">
                {{ initials(auth_info && auth_info.name) }}
              </div>
              <span class="site-header__username">{{ auth_info && auth_info.name }}</span>
              <i class="fas fa-chevron-down site-header__chevron"
                 :class="{ 'site-header__chevron--open': dropdownOpen }"></i>
            </button>

            <transition name="dropdown-pop">
              <div v-if="dropdownOpen" class="site-header__dropdown">
                <div class="dropdown__arrow"></div>
                <div class="dropdown__user-info">
                  <div class="dropdown__avatar">{{ initials(auth_info && auth_info.name) }}</div>
                  <div>
                    <div class="dropdown__user-name">{{ auth_info && auth_info.name }}</div>
                    <div class="dropdown__user-role">Member</div>
                  </div>
                </div>
                <div class="dropdown__divider"></div>
                <a href="/admin#/dashboard" class="dropdown-item">
                  <span class="dropdown-item__icon"><i class="fas fa-gauge-high"></i></span>
                  <span>Dashboard</span>
                </a>
                <div class="dropdown__divider"></div>
                <button class="dropdown-item dropdown-item--danger" @click="log_out">
                  <span class="dropdown-item__icon"><i class="fas fa-right-from-bracket"></i></span>
                  <span>Logout</span>
                </button>
              </div>
            </transition>
          </div>
        </template>

        <!-- Guest: login button -->
        <template v-else>
          <a href="/login" class="site-header__btn site-header__btn--primary">
            <i class="fas fa-right-to-bracket"></i>
            <span>Login</span>
          </a>
        </template>
      </div>

      </div><!-- end .site-header__right -->

      <!-- ── Hamburger ── -->
      <button class="site-header__hamburger"
              :class="{ 'is-open': mobileOpen }"
              @click="mobileOpen = !mobileOpen"
              aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <!-- ── Mobile menu ── -->
    <transition name="mobile-slide">
      <div v-if="mobileOpen" class="site-header__mobile">
        <a href="/"            class="mobile-link" @click.prevent="scrollTo('top')"><i class="fas fa-house"></i><span>Home</span></a>
        <a href="#home"        class="mobile-link" @click.prevent="scrollTo('home')"><i class="fas fa-users"></i><span>Members</span></a>
        <a href="#about"       class="mobile-link" @click="scrollTo('about');mobileOpen=false"><i class="fas fa-circle-info"></i><span>About</span></a>
        <a href="#objectives"  class="mobile-link" @click="scrollTo('objectives');mobileOpen=false"><i class="fas fa-bullseye"></i><span>Objectives</span></a>
        <a href="#terms"       class="mobile-link" @click="scrollTo('terms');mobileOpen=false"><i class="fas fa-file-contract"></i><span>Terms &amp; Condition</span></a>
        <a href="#contact"     class="mobile-link" @click="scrollTo('contact');mobileOpen=false"><i class="fas fa-envelope"></i><span>Contact</span></a>
        <div class="mobile__divider"></div>
        <template v-if="is_auth">
          <a href="/admin#/dashboard" class="mobile-link mobile-link--primary"><i class="fas fa-gauge-high"></i><span>Dashboard</span></a>
          <button class="mobile-link mobile-link--danger" @click="log_out"><i class="fas fa-right-from-bracket"></i><span>Logout</span></button>
        </template>
        <template v-else>
          <a href="/login" class="mobile-link mobile-link--primary"><i class="fas fa-right-to-bracket"></i><span>Login</span></a>
        </template>
      </div>
    </transition>

  </header>
</template>

<script>
import { mapActions, mapState } from 'pinia';
import { router }              from '@inertiajs/vue3';
import { auth_store }          from '../GlobalStore/auth_store';
import { site_settings_store } from '../GlobalStore/site_settings_store';

export default {
  name: 'SiteHeader',
  data: () => ({
    mobileOpen:     false,
    dropdownOpen:   false,
    scrolled:       false,
    scrollProgress: 0,
    activeSection:  'top',
  }),
  computed: {
    ...mapState(auth_store, ['is_auth', 'auth_info']),
    siteName() { return site_settings_store().get_setting_value('site_name') || 'শপনোপুরণ'; },
    siteLogo() {
      const v = site_settings_store().get_setting_value('image') || site_settings_store().get_setting_value('header_logo');
      if (!v) return null;
      return v.startsWith('http') || v.startsWith('/') ? v : '/' + v;
    },
  },
  async mounted() {
    site_settings_store().get_all_website_settings();
    if (localStorage.getItem('auth_token')) {
      await this.check_is_auth();
    }

    const onHome = window.location.pathname === '/';

    // On non-home pages don't highlight any nav item by default
    if (!onHome) this.activeSection = '';

    window.addEventListener('scroll', this._onScroll, { passive: true });
    document.addEventListener('click', this._closeOnOutside);

    // Section observer — only on home page
    if (onHome) {
      this._secObserver = new IntersectionObserver(
        (entries) => entries.forEach(e => { if (e.isIntersecting) this.activeSection = e.target.id; }),
        { threshold: 0.25 }
      );
      ['home', 'about', 'objectives', 'terms'].forEach(id => {
        const el = document.getElementById(id);
        if (el) this._secObserver.observe(el);
      });
    }

    // Contact/footer observer — works on ALL pages
    this._contactObserver = new IntersectionObserver(
      (entries) => entries.forEach(e => {
        if (e.isIntersecting) {
          this.activeSection = 'contact';
        } else if (this.activeSection === 'contact') {
          // Leaving the footer — reset to top or blank
          this.activeSection = (onHome && window.scrollY < 80) ? 'top' : '';
        }
      }),
      { threshold: 0.15 }
    );
    const contactEl = document.getElementById('contact');
    if (contactEl) this._contactObserver.observe(contactEl);
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this._onScroll);
    document.removeEventListener('click', this._closeOnOutside);
    if (this._secObserver)     this._secObserver.disconnect();
    if (this._contactObserver) this._contactObserver.disconnect();
  },
  methods: {
    ...mapActions(auth_store, ['check_is_auth', 'log_out']),
    _onScroll() {
      this.scrolled = window.scrollY > 30;
      const doc   = document.documentElement;
      const total = doc.scrollHeight - doc.clientHeight;
      this.scrollProgress = total > 0 ? (window.scrollY / total) * 100 : 0;
      // Only reset to 'top' when actually on the home page
      if (window.location.pathname === '/' && window.scrollY < 80) {
        this.activeSection = 'top';
      }
    },
    _closeOnOutside(e) {
      if (this.$refs.userWrap && !this.$refs.userWrap.contains(e.target)) {
        this.dropdownOpen = false;
      }
      if (this.$el && !this.$el.contains(e.target)) {
        this.mobileOpen = false;
      }
    },
    initials(name) {
      return (name || '?').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    },
    scrollTo(id) {
      this.mobileOpen = false;
      const onHome = window.location.pathname === '/';

      if (!onHome) {
        // Store scroll target, then SPA-navigate to home (no reload)
        if (id !== 'top') sessionStorage.setItem('scrollTarget', id);
        router.visit('/');
        return;
      }

      // Already on home page — smooth scroll
      if (id === 'top') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.activeSection = 'top';
      } else if (id === 'contact') {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        this.activeSection = 'contact';
      } else {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        this.activeSection = id;
      }
    },
  },
};
</script>

<style scoped>
/* ── Header shell ─────────────────────────────────────────────── */
.site-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  z-index: 300;
  background: rgba(6, 9, 22, 0.88);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255,255,255,0.06);
  transition: background 0.3s, box-shadow 0.3s;
}
.site-header--scrolled {
  background: rgba(4, 6, 18, 0.97);
  box-shadow: 0 4px 40px rgba(0,0,0,0.45);
  border-bottom-color: rgba(99,102,241,0.15);
}

/* Animated rainbow top strip */
.site-header__rainbow {
  height: 2px;
  background: linear-gradient(90deg, #6366f1, #8b5cf6, #14b8a6, #10b981, #f59e0b, #6366f1);
  background-size: 300% 100%;
  animation: rainbowSlide 4s linear infinite;
}
@keyframes rainbowSlide {
  0%   { background-position: 0% 50%; }
  100% { background-position: 300% 50%; }
}

/* Scroll-progress bar */
.site-header__progress {
  position: absolute;
  bottom: 0; left: 0;
  height: 2px;
  background: linear-gradient(90deg, #6366f1, #14b8a6);
  transition: width 0.08s linear;
  pointer-events: none;
  z-index: 1;
}

/* ── Inner layout ─────────────────────────────────────────────── */
.site-header__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 24px;
  height: 62px;
  display: flex;
  align-items: center;
  gap: 0;
}
.site-header__left {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}
.site-header__right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
}

/* ── Brand ────────────────────────────────────────────────────── */
.site-header__brand {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  flex-shrink: 0;
}
.site-header__logo-ring {
  width: 38px; height: 38px;
  border-radius: 50%;
  border: 2px solid rgba(99,102,241,0.5);
  overflow: hidden;
  background: rgba(255,255,255,0.06);
  display: flex; align-items: center; justify-content: center;
  transition: border-color 0.3s, box-shadow 0.3s;
}
.site-header__brand:hover .site-header__logo-ring {
  border-color: rgba(99,102,241,0.9);
  box-shadow: 0 0 16px rgba(99,102,241,0.4);
}
.site-header__logo-img  { width: 100%; height: 100%; object-fit: cover; }
.site-header__logo-text { font-size: 14px; font-weight: 800; color: #a5b4fc; }


/* ── Nav links (sec-nav style) ───────────────────────────────── */
.site-header__nav {
  display: flex;
  align-items: stretch;
  gap: 0;
}
.hdr-nav-link {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 0 18px;
  height: 62px;
  font-size: 13.5px;
  font-weight: 600;
  color: #4b5563;
  text-decoration: none;
  white-space: nowrap;
  position: relative;
  transition: color 0.25s, background 0.25s;
  letter-spacing: 0.3px;
}
.hdr-nav-link i {
  font-size: 12px;
  transition: color 0.25s, transform 0.25s;
}
.hdr-nav-link:hover {
  color: #cbd5e1;
  background: rgba(255,255,255,0.04);
}
.hdr-nav-link:hover i { transform: scale(1.15); }

.hdr-nav-link--active {
  color: #e2e8f0;
  background: rgba(99,102,241,0.10);
}
.hdr-nav-link--active i { color: #a5b4fc; }
.hdr-nav-link--active::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, #6366f1, #a5b4fc);
  box-shadow: 0 0 8px rgba(99,102,241,0.7);
}

/* ── Actions ──────────────────────────────────────────────────── */
.site-header__actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* User button */
.site-header__user-wrap {
  position: relative;
}
.site-header__user {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 12px 5px 5px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 40px;
  cursor: pointer;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 500;
  transition: background 0.2s, border-color 0.2s;
}
.site-header__user:hover {
  background: rgba(99,102,241,0.1);
  border-color: rgba(99,102,241,0.35);
}

.site-header__avatar {
  width: 28px; height: 28px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid rgba(99,102,241,0.5);
  object-fit: cover;
  flex-shrink: 0;
}
.site-header__avatar--text {
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg,#6366f1,#4f46e5);
  color: #fff; font-size: 11px; font-weight: 700;
}
.site-header__username {
  max-width: 110px;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.site-header__chevron {
  font-size: 10px;
  color: #64748b;
  transition: transform 0.25s;
}
.site-header__chevron--open { transform: rotate(180deg); }

/* Dropdown */
.site-header__dropdown {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  background: rgba(6, 9, 24, 0.98);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px;
  padding: 8px;
  min-width: 190px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.55), 0 0 0 1px rgba(99,102,241,0.1);
  z-index: 99;
}
.dropdown__arrow {
  position: absolute;
  top: -5px; right: 20px;
  width: 10px; height: 10px;
  background: rgba(6,9,24,0.98);
  border-left: 1px solid rgba(255,255,255,0.08);
  border-top:  1px solid rgba(255,255,255,0.08);
  transform: rotate(45deg);
}
.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 10px;
  font-size: 13.5px;
  font-weight: 500;
  color: #94a3b8;
  text-decoration: none;
  background: transparent;
  border: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
  transition: background 0.2s, color 0.2s;
}
.dropdown-item:hover {
  background: rgba(255,255,255,0.06);
  color: #e2e8f0;
}
.dropdown-item__icon {
  width: 28px; height: 28px;
  border-radius: 8px;
  background: rgba(255,255,255,0.06);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
  transition: background 0.2s;
}
.dropdown-item:hover .dropdown-item__icon {
  background: rgba(99,102,241,0.15);
  color: #a5b4fc;
}
.dropdown-item--danger       { color: #f87171; }
.dropdown-item--danger:hover { background: rgba(239,68,68,0.08); color: #fca5a5; }
.dropdown-item--danger:hover .dropdown-item__icon { background: rgba(239,68,68,0.12); color: #f87171; }
.dropdown__divider {
  height: 1px;
  background: rgba(255,255,255,0.06);
  margin: 6px 4px;
}
.dropdown__user-info {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px 8px;
}
.dropdown__avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  font-size: 13px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  border: 2px solid rgba(99,102,241,0.4);
}
.dropdown__user-name {
  font-size: 13.5px; font-weight: 700; color: #e2e8f0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;
}
.dropdown__user-role {
  font-size: 11px; color: #34d399; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.6px; margin-top: 1px;
}

/* Dropdown transition */
.dropdown-pop-enter-active { animation: dropPopIn  0.2s cubic-bezier(0.22,1,0.36,1); }
.dropdown-pop-leave-active { animation: dropPopOut 0.15s ease-in; }
@keyframes dropPopIn  {
  from { opacity: 0; transform: translateY(-6px) scale(0.96); }
  to   { opacity: 1; transform: translateY(0)    scale(1); }
}
@keyframes dropPopOut {
  from { opacity: 1; transform: translateY(0)    scale(1); }
  to   { opacity: 0; transform: translateY(-4px) scale(0.97); }
}

/* Primary login button */
.site-header__btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  border: none;
  transition: all 0.25s;
  white-space: nowrap;
}
.site-header__btn--primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  box-shadow: 0 4px 18px rgba(99,102,241,0.35);
}
.site-header__btn--primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(99,102,241,0.5);
}

/* ── Hamburger ───────────────────────────────────────────────── */
.site-header__hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  padding: 6px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
  cursor: pointer;
  margin-left: auto;
}
.site-header__hamburger span {
  display: block;
  width: 20px; height: 2px;
  background: #94a3b8;
  border-radius: 2px;
  transform-origin: center;
  transition: transform 0.3s cubic-bezier(0.22,1,0.36,1), opacity 0.3s, background 0.2s;
}
.site-header__hamburger:hover span { background: #e2e8f0; }

/* Morph to × */
.site-header__hamburger.is-open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.site-header__hamburger.is-open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.site-header__hamburger.is-open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ── Mobile menu ─────────────────────────────────────────────── */
.site-header__mobile {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 12px 16px 16px;
  border-top: 1px solid rgba(255,255,255,0.06);
  background: rgba(4,6,18,0.98);
}
.mobile-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  color: #64748b;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  border-radius: 10px;
  cursor: pointer;
  border: none;
  background: transparent;
  text-align: left;
  width: 100%;
  transition: color 0.2s, background 0.2s;
}
.mobile-link i { font-size: 14px; width: 16px; text-align: center; }
.mobile-link:hover { color: #e2e8f0; background: rgba(255,255,255,0.05); }
.mobile-link--primary {
  color: #a5b4fc;
  background: rgba(99,102,241,0.10);
  border: 1px solid rgba(99,102,241,0.22);
}
.mobile-link--primary:hover { background: rgba(99,102,241,0.18); color: #c7d2fe; }
.mobile-link--danger  { color: #f87171; }
.mobile-link--danger:hover  { background: rgba(239,68,68,0.08); color: #fca5a5; }
.mobile__divider {
  height: 1px;
  background: rgba(255,255,255,0.05);
  margin: 4px 0;
}

/* Mobile menu slide transition */
.mobile-slide-enter-active { animation: mobileIn  0.28s cubic-bezier(0.22,1,0.36,1); }
.mobile-slide-leave-active { animation: mobileOut 0.18s ease-in; }
@keyframes mobileIn  {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes mobileOut {
  from { opacity: 1; transform: translateY(0); }
  to   { opacity: 0; transform: translateY(-6px); }
}

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 768px) {
  .site-header__nav { display: none; }
  .site-header__actions .site-header__btn { display: none; }
  .site-header__actions .site-header__user-wrap { display: none; }
  .site-header__hamburger { display: flex; }
}
@media (max-width: 480px) {
  .site-header__brand-text { display: none; }
}
</style>
