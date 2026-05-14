<template>
  <footer class="site-footer" id="contact">
    <!-- Animated top border -->
    <div class="footer__top-border"></div>

    <div class="footer__inner">
      <!-- Col 1: Brand -->
      <div class="footer__brand">
        <div class="footer__brand-row">
          <div class="footer__logo-ring">
            <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="footer__logo-img" />
            <span v-else class="footer__logo-text">শপ</span>
          </div>
          <span class="footer__brand-name">{{ siteName }}</span>
        </div>
        <p class="footer__desc">
          {{ siteName }} একটি সদস্য-ভিত্তিক সঞ্চয় ও বিনিয়োগ সংগঠন। পারস্পরিক সহযোগিতা ও
          আস্থার ভিত্তিতে প্রতিটি সদস্যের আর্থিক স্বাধীনতা নিশ্চিত করতে প্রতিশ্রুতিবদ্ধ।
        </p>
        <div class="footer__tagline-row">
          <span class="footer__tagline">সঞ্চয় · সমৃদ্ধি · সহযোগিতা</span>
        </div>
        <div class="footer__social">
          <a href="#" class="social-btn" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-btn" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          <a href="#" class="social-btn" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
          <a href="mailto:info@shopnopuron.com" class="social-btn" aria-label="Email"><i class="fas fa-envelope"></i></a>
        </div>
      </div>

      <!-- Col 2: Navigation -->
      <div class="footer__col">
        <h4 class="footer__col-title">Navigation</h4>
        <ul class="footer__links">
          <li><a href="/" @click.prevent="scrollTo('top')"><i class="fas fa-home"></i> Home</a></li>
          <li><a href="/#home" @click.prevent="scrollTo('home')"><i class="fas fa-users"></i> Members</a></li>
          <li><a href="/#about" @click.prevent="scrollTo('about')"><i class="fas fa-info-circle"></i> About Us</a></li>
          <li><a href="/#objectives" @click.prevent="scrollTo('objectives')"><i class="fas fa-bullseye"></i> Objectives</a></li>
          <li><a href="/#terms" @click.prevent="scrollTo('terms')"><i class="fas fa-file-contract"></i> Terms &amp; Condition</a></li>
          <li><a href="/#contact" @click.prevent="scrollTo('contact')"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>
      </div>

      <!-- Col 3: Financial Pages -->
      <div class="footer__col">
        <h4 class="footer__col-title">Financial Reports</h4>
        <ul class="footer__links">
          <li><Link href="/transaction-log"><i class="fas fa-history"></i> Transaction Log</Link></li>
          <li><Link href="/income"><i class="fas fa-hand-holding-usd"></i> Income</Link></li>
          <li><Link href="/expense"><i class="fas fa-receipt"></i> Expense</Link></li>
          <li><Link href="/balance-sheet"><i class="fas fa-file-invoice-dollar"></i> Balance Sheet</Link></li>
        </ul>
      </div>

      <!-- Col 4: Contact -->
      <div class="footer__col">
        <h4 class="footer__col-title">Contact</h4>
        <ul class="footer__contact">
          <li>
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <div class="contact-text">
              <span class="contact-label">Email</span>
              <a href="mailto:info@shopnopuron.com">info@shopnopuron.com</a>
            </div>
          </li>
          <li>
            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
            <div class="contact-text">
              <span class="contact-label">Phone</span>
              <span>+880 168-3392241</span>
            </div>
          </li>
          <li>
            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="contact-text">
              <span class="contact-label">Address</span>
              <span>ঢাকা, বাংলাদেশ</span>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Divider -->
    <div class="footer__divider"></div>

    <!-- Bottom bar -->
    <div class="footer__bottom">
      <span class="footer__copy">© {{ year }} <strong>{{ siteName }}</strong> — সকল অধিকার সংরক্ষিত</span>
      <div class="footer__bottom-links">
        <a href="/#terms" @click.prevent="scrollTo('terms')">Terms &amp; Condition</a>
        <span class="footer__dot"></span>
        <Link href="/balance-sheet">Balance Sheet</Link>
        <span class="footer__dot"></span>
        <Link href="/transaction-log">Transaction Log</Link>
      </div>
    </div>
  </footer>
</template>

<script>
import { router }              from '@inertiajs/vue3';
import { site_settings_store } from '../GlobalStore/site_settings_store';

export default {
  name: 'SiteFooter',
  computed: {
    siteName() { return site_settings_store().get_setting_value('site_name') || 'শপনোপুরণ'; },
    siteLogo()  { return site_settings_store().get_setting_value('image') || site_settings_store().get_setting_value('header_logo') || null; },
    year()      { return new Date().getFullYear(); },
  },
  methods: {
    scrollTo(id) {
      const onHome = window.location.pathname === '/';
      if (!onHome) {
        if (id === 'top') { router.visit('/'); return; }
        if (id === 'contact') { router.visit('/'); window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); return; }
        sessionStorage.setItem('scrollTarget', id);
        router.visit('/');
        return;
      }
      if (id === 'top') { window.scrollTo({ top: 0, behavior: 'smooth' }); return; }
      if (id === 'contact') { window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); return; }
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },
  },
};
</script>

<style scoped>
.site-footer {
  background: #060a14;
  color: #94a3b8;
  font-size: 13px;
  position: relative;
}

/* Animated top border */
.footer__top-border {
  height: 2px;
  background: linear-gradient(90deg, #6366f1, #818cf8, #34d399, #2dd4bf, #6366f1);
  background-size: 300% 100%;
  animation: borderSlide 5s linear infinite;
}
@keyframes borderSlide {
  0%   { background-position: 0%   50%; }
  100% { background-position: 300% 50%; }
}

/* Inner grid */
.footer__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 60px 32px 40px;
  display: grid;
  grid-template-columns: 2.2fr 1fr 1fr 1.2fr;
  gap: 48px;
}

/* Brand column */
.footer__brand-row {
  display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
}
.footer__logo-ring {
  width: 44px; height: 44px; border-radius: 50%;
  border: 2px solid rgba(99,102,241,0.35);
  overflow: hidden; background: rgba(255,255,255,0.05);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.footer__logo-img  { width: 100%; height: 100%; object-fit: cover; }
.footer__logo-text { font-size: 16px; font-weight: 800; color: #a5b4fc; }

.footer__brand-name {
  font-size: 18px; font-weight: 800;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}

.footer__desc {
  color: #475569; line-height: 1.85; font-size: 13px;
  margin-bottom: 14px; max-width: 360px;
}

.footer__tagline-row { margin-bottom: 20px; }
.footer__tagline {
  font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
  color: rgba(99,102,241,0.5);
}

.footer__social { display: flex; gap: 8px; }
.social-btn {
  width: 34px; height: 34px; border-radius: 10px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  display: flex; align-items: center; justify-content: center;
  color: #475569; text-decoration: none; font-size: 13px;
  transition: all 0.2s;
}
.social-btn:hover {
  background: rgba(99,102,241,0.15);
  border-color: rgba(99,102,241,0.4);
  color: #a5b4fc;
  transform: translateY(-2px);
}

/* Columns */
.footer__col-title {
  font-size: 11px; font-weight: 800; text-transform: uppercase;
  letter-spacing: 1px; color: #334155; margin: 0 0 20px;
  display: flex; align-items: center; gap: 8px;
}
.footer__col-title::after {
  content: ''; flex: 1; height: 1px;
  background: linear-gradient(90deg, rgba(99,102,241,0.3), transparent);
}

.footer__links {
  list-style: none; padding: 0; margin: 0;
  display: flex; flex-direction: column; gap: 10px;
}
.footer__links a {
  display: flex; align-items: center; gap: 9px;
  color: #64748b; text-decoration: none; font-size: 13px;
  transition: color 0.2s, transform 0.2s; padding: 2px 0;
}
.footer__links a i {
  font-size: 11px; width: 14px; text-align: center; color: #334155; transition: color 0.2s;
}
.footer__links a:hover { color: #a5b4fc; transform: translateX(3px); }
.footer__links a:hover i { color: #818cf8; }

/* Contact list */
.footer__contact {
  list-style: none; padding: 0; margin: 0;
  display: flex; flex-direction: column; gap: 16px;
}
.footer__contact li { display: flex; align-items: flex-start; gap: 12px; }
.contact-icon {
  width: 32px; height: 32px; border-radius: 8px;
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; color: #475569; flex-shrink: 0; margin-top: 1px;
}
.contact-text { display: flex; flex-direction: column; gap: 2px; }
.contact-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; color: #334155; }
.contact-text span, .contact-text a {
  color: #64748b; font-size: 12.5px; text-decoration: none; transition: color 0.2s;
}
.contact-text a:hover { color: #a5b4fc; }

/* Divider */
.footer__divider {
  max-width: 1280px; margin: 0 auto;
  height: 1px; background: rgba(255,255,255,0.05);
  margin-left: 32px; margin-right: 32px;
}

/* Bottom bar */
.footer__bottom {
  max-width: 1280px; margin: 0 auto;
  padding: 18px 32px;
  display: flex; align-items: center;
  justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.footer__copy { font-size: 12px; color: #334155; }
.footer__copy strong { color: #475569; }

.footer__bottom-links {
  display: flex; align-items: center; gap: 8px;
}
.footer__bottom-links a {
  font-size: 12px; color: #334155; text-decoration: none; transition: color 0.2s;
}
.footer__bottom-links a:hover { color: #818cf8; }
.footer__dot {
  width: 3px; height: 3px; border-radius: 50%;
  background: rgba(255,255,255,0.15);
}

/* Responsive */
@media (max-width: 1024px) {
  .footer__inner { grid-template-columns: 1.8fr 1fr 1fr; gap: 36px; }
  .footer__brand { grid-column: 1 / -1; }
  .footer__inner > .footer__brand { grid-column: 1 / -1; }
}
@media (max-width: 1024px) {
  .footer__inner { grid-template-columns: 1fr 1fr 1fr; }
  .footer__brand { grid-column: 1 / -1; }
}
@media (max-width: 640px) {
  .footer__inner { grid-template-columns: 1fr 1fr; padding: 40px 20px 28px; gap: 28px; }
  .footer__brand { grid-column: 1 / -1; }
  .footer__bottom { flex-direction: column; align-items: flex-start; padding: 16px 20px; }
  .footer__divider { margin-left: 20px; margin-right: 20px; }
}
@media (max-width: 400px) {
  .footer__inner { grid-template-columns: 1fr; }
}
</style>
