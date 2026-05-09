<template>
 
</template>

<script>
import { site_settings_store } from '../GlobalStore/site_settings_store';

export default {
  name: 'SiteFooter',
  computed: {
    siteName() { return site_settings_store().get_setting_value('site_name') || 'শপনোপুরণ'; },
    siteLogo() { return site_settings_store().get_setting_value('header_logo') || null; },
    year()     { return new Date().getFullYear(); },
  },
};
</script>

<style scoped>
.site-footer {
  background: #080c18;
  border-top: 1px solid rgba(255,255,255,0.07);
  color: #94a3b8;
  font-size: 13px;
}

.footer__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 56px 24px 40px;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 48px;
}

/* About */
.footer__brand-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
}
.footer__logo-ring {
  width: 44px; height: 44px;
  border-radius: 50%;
  border: 2px solid rgba(99,102,241,0.4);
  overflow: hidden;
  background: rgba(255,255,255,0.05);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.footer__logo-img  { width: 100%; height: 100%; object-fit: cover; }
.footer__logo-text { font-size: 16px; font-weight: 800; color: #a5b4fc; }
.footer__brand-name {
  font-size: 17px; font-weight: 700;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.footer__desc {
  color: #475569;
  line-height: 1.8;
  font-size: 13.5px;
  margin-bottom: 14px;
  max-width: 360px;
}
.footer__tagline {
  font-size: 11px;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(99,102,241,0.55);
}

/* Columns */
.footer__col-title {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.9px;
  color: #475569;
  margin-bottom: 18px;
}
.footer__links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.footer__links a {
  color: #64748b;
  text-decoration: none;
  font-size: 13.5px;
  transition: color 0.2s;
}
.footer__links a:hover { color: #a5b4fc; }
.footer__links--plain li { color: #64748b; font-size: 13.5px; }

/* Bottom bar */
.footer__bottom {
  border-top: 1px solid rgba(255,255,255,0.05);
  padding: 18px 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #334155;
  font-size: 12px;
  flex-wrap: wrap;
}
.footer__sep { color: rgba(255,255,255,0.1); }

/* Responsive */
@media (max-width: 860px) {
  .footer__inner { grid-template-columns: 1fr 1fr; gap: 36px; }
  .footer__about { grid-column: 1 / -1; }
}
@media (max-width: 540px) {
  .footer__inner { grid-template-columns: 1fr; padding: 36px 20px 28px; }
}
</style>
