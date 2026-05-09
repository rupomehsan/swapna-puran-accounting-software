<template>
 
</template>

<script>
import { mapActions, mapState } from 'pinia';
import { auth_store }          from '../GlobalStore/auth_store';
import { site_settings_store } from '../GlobalStore/site_settings_store';

export default {
  name: 'SiteHeader',
  data: () => ({ mobileOpen: false }),
  computed: {
    ...mapState(auth_store, ['is_auth', 'auth_info']),
    siteName() { return site_settings_store().get_setting_value('site_name') || 'শপনোপুরণ'; },
    siteLogo() { return site_settings_store().get_setting_value('header_logo') || null; },
  },
  methods: {
    ...mapActions(auth_store, ['check_is_auth', 'log_out']),
  },
  async mounted() {
    site_settings_store().get_all_website_settings();
    if (localStorage.getItem('auth_token')) {
      await this.check_is_auth();
    }
    document.addEventListener('click', this._closeOnOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this._closeOnOutside);
  },
  methods: {
    ...mapActions(auth_store, ['check_is_auth', 'log_out']),
    _closeOnOutside(e) {
      if (!this.$el.contains(e.target)) this.mobileOpen = false;
    },
  },
};
</script>

<style scoped>
/* ── Header shell ──────────────────────────────────────────── */
.site-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(8, 12, 24, 0.92);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,0.06);
}

.site-header__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 24px;
  height: 64px;
  display: flex;
  align-items: center;
  gap: 32px;
}

/* Brand */
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
}
.site-header__logo-img  { width: 100%; height: 100%; object-fit: cover; }
.site-header__logo-text { font-size: 14px; font-weight: 800; color: #a5b4fc; }
.site-header__name {
  font-size: 16px; font-weight: 700;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  white-space: nowrap;
}

/* Nav */
.site-header__nav {
  display: flex;
  align-items: center;
  gap: 4px;
  flex: 1;
}
.nav-link {
  padding: 6px 14px;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 8px;
  transition: color 0.2s, background 0.2s;
}
.nav-link:hover {
  color: #e2e8f0;
  background: rgba(255,255,255,0.06);
}

/* Actions */
.site-header__actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.site-header__user {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 500;
}
.site-header__avatar {
  width: 30px; height: 30px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(99,102,241,0.4);
}
.site-header__avatar--text {
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg,#6366f1,#4f46e5);
  color: #fff; font-size: 12px; font-weight: 700;
}
.site-header__username {
  max-width: 120px;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

/* Buttons */
.site-header__btn {
  padding: 7px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  white-space: nowrap;
}
.site-header__btn--primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
}
.site-header__btn--primary:hover { opacity: 0.88; transform: translateY(-1px); }
.site-header__btn--ghost {
  background: rgba(255,255,255,0.06);
  color: #94a3b8;
  border: 1px solid rgba(255,255,255,0.1);
}
.site-header__btn--ghost:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; }
.site-header__btn--outline {
  background: transparent;
  color: #f87171;
  border: 1px solid rgba(239,68,68,0.35);
}
.site-header__btn--outline:hover { background: rgba(239,68,68,0.1); }

/* Hamburger */
.site-header__hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  padding: 6px;
  background: transparent;
  border: none;
  cursor: pointer;
  margin-left: auto;
}
.site-header__hamburger span {
  display: block;
  width: 22px; height: 2px;
  background: #94a3b8;
  border-radius: 2px;
  transition: background 0.2s;
}
.site-header__hamburger:hover span { background: #e2e8f0; }

/* Mobile menu */
.site-header__mobile {
  display: none;
  flex-direction: column;
  gap: 4px;
  padding: 12px 24px 16px;
  border-top: 1px solid rgba(255,255,255,0.06);
  background: rgba(8,12,24,0.98);
}
.site-header__mobile--open { display: flex; }
.mobile-link {
  padding: 10px 14px;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 8px;
  transition: color 0.2s, background 0.2s;
  border: none;
  background: transparent;
  text-align: left;
  cursor: pointer;
}
.mobile-link:hover { color: #e2e8f0; background: rgba(255,255,255,0.06); }
.mobile-link--primary {
  background: rgba(99,102,241,0.15);
  color: #a5b4fc;
  border: 1px solid rgba(99,102,241,0.3);
}
.mobile-link--btn { width: 100%; color: #f87171; }

/* Responsive */
@media (max-width: 640px) {
  .site-header__nav,
  .site-header__actions { display: none; }
  .site-header__hamburger { display: flex; }
}
</style>
