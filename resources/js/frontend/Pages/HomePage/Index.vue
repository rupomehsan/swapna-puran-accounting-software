<template>
  <div>
    <Head :title="siteName + ' — Member Portal'" />

    <!-- ═══════════════════════════════════════════
         HERO BANNER
    ════════════════════════════════════════════════ -->
    <section class="hero">
      <!-- Animated background orbs -->
      <div class="hero__orb hero__orb--1"></div>
      <div class="hero__orb hero__orb--2"></div>
      <div class="hero__orb hero__orb--3"></div>
      <div class="hero__orb hero__orb--4"></div>
      <!-- Dot-grid texture -->
      <div class="hero__grid"></div>
      <div class="hero__overlay"></div>

      <div class="hero__content">
        <!-- Identity column -->
        <div class="hero__identity">
          <div class="hero__badge">
            <span class="hero__badge-pulse"></span>
            অফিসিয়াল মেম্বার পোর্টাল
          </div>

          <div class="hero__logo-wrap">
            <div class="hero__logo-ring-spin"></div>
            <div class="hero__logo-ring-spin hero__logo-ring-spin--rev"></div>
            <div class="hero__logo-ring">
              <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="hero__logo-img" />
              <span v-else class="hero__logo-text">শপ</span>
            </div>
          </div>

          <h1 class="hero__name">{{ siteName }}</h1>

          <div class="hero__slogan-row">
            <span class="hero__slogan-sep"></span>
            <p class="hero__slogan">সঞ্চয় &nbsp;·&nbsp; সমৃদ্ধি &nbsp;·&nbsp; সহযোগিতা</p>
            <span class="hero__slogan-sep"></span>
          </div>

          <p class="hero__desc">আমাদের সদস্যদের আর্থিক উন্নয়নে প্রতিশ্রুতিবদ্ধ<br/>একটি বিশ্বস্ত সঞ্চয় সংগঠন।</p>

          <div class="hero__cta-row">
            <a href="#home" @click.prevent="scrollTo('home')" class="hero__cta hero__cta--primary">
              <i class="fas fa-users"></i> সদস্য দেখুন
            </a>
            <a href="#about" @click.prevent="scrollTo('about')" class="hero__cta hero__cta--outline">
              আমাদের সম্পর্কে
            </a>
          </div>
        </div>

        <!-- Stats grid -->
        <div class="hero__stats">
          <div class="stat-card stat-card--green">
            <div class="stat-card__accent"></div>
            <div class="stat-card__top">
              <div class="stat-card__icon-wrap"><i class="fas fa-coins"></i></div>
              <span class="stat-card__trend stat-card__trend--up"><i class="fas fa-arrow-trend-up"></i></span>
            </div>
            <span class="stat-card__label">Net Fund</span>
            <span class="stat-card__value">৳ {{ fmt(org.net_fund) }}</span>
            <div class="stat-card__bar"><div class="stat-card__bar-fill"></div></div>
          </div>

          <div class="stat-card stat-card--blue">
            <div class="stat-card__accent"></div>
            <div class="stat-card__top">
              <div class="stat-card__icon-wrap"><i class="fas fa-piggy-bank"></i></div>
              <span class="stat-card__trend stat-card__trend--up"><i class="fas fa-arrow-trend-up"></i></span>
            </div>
            <span class="stat-card__label">Total Deposits</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_deposits) }}</span>
            <div class="stat-card__bar"><div class="stat-card__bar-fill"></div></div>
          </div>

          <div class="stat-card stat-card--purple">
            <div class="stat-card__accent"></div>
            <div class="stat-card__top">
              <div class="stat-card__icon-wrap"><i class="fas fa-chart-line"></i></div>
              <span class="stat-card__trend stat-card__trend--up"><i class="fas fa-arrow-trend-up"></i></span>
            </div>
            <span class="stat-card__label">Total Income</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_income) }}</span>
            <div class="stat-card__bar"><div class="stat-card__bar-fill"></div></div>
          </div>

          <div class="stat-card stat-card--orange">
            <div class="stat-card__accent"></div>
            <div class="stat-card__top">
              <div class="stat-card__icon-wrap"><i class="fas fa-receipt"></i></div>
              <span class="stat-card__trend stat-card__trend--down"><i class="fas fa-arrow-trend-down"></i></span>
            </div>
            <span class="stat-card__label">Total Expense</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_expense) }}</span>
            <div class="stat-card__bar"><div class="stat-card__bar-fill"></div></div>
          </div>

          <div class="stat-card stat-card--red">
            <div class="stat-card__accent"></div>
            <div class="stat-card__top">
              <div class="stat-card__icon-wrap"><i class="fas fa-arrow-up-from-bracket"></i></div>
              <span class="stat-card__trend stat-card__trend--down"><i class="fas fa-arrow-trend-down"></i></span>
            </div>
            <span class="stat-card__label">Withdrawals</span>
            <span class="stat-card__value">৳ {{ fmt(org.total_withdrawals) }}</span>
            <div class="stat-card__bar"><div class="stat-card__bar-fill"></div></div>
          </div>

          <div class="stat-card stat-card--teal">
            <div class="stat-card__accent"></div>
            <div class="stat-card__top">
              <div class="stat-card__icon-wrap"><i class="fas fa-user-group"></i></div>
              <span class="stat-card__trend stat-card__trend--up"><i class="fas fa-arrow-trend-up"></i></span>
            </div>
            <span class="stat-card__label">Total Members</span>
            <span class="stat-card__value">{{ org.total_members }}</span>
            <div class="stat-card__bar"><div class="stat-card__bar-fill"></div></div>
          </div>
        </div>
      </div>

      <!-- Animated wave bottom — 3 stacked layers -->
      <div class="hero__wave">
        <svg class="wl wl--1" viewBox="0 0 1440 130" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,55 C200,110 500,10 720,65 C940,115 1200,20 1440,70 L1440,130 L0,130 Z" fill="rgba(99,102,241,0.55)"/>
        </svg>
        <svg class="wl wl--2" viewBox="0 0 1440 130" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,70 C300,20 600,110 900,55 C1100,15 1300,85 1440,50 L1440,130 L0,130 Z" fill="rgba(16,185,129,0.50)"/>
        </svg>
        <svg class="wl wl--3" viewBox="0 0 1440 130" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,85 C240,35 600,120 900,70 C1100,30 1280,95 1440,60 L1440,130 L0,130 Z" fill="rgba(139,92,246,0.35)"/>
        </svg>
        <svg class="wl wl--base" viewBox="0 0 1440 130" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,100 C360,50 900,130 1440,75 L1440,130 L0,130 Z" fill="#0d1320"/>
        </svg>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         MEMBER LIST
    ════════════════════════════════════════════════ -->
    <section id="home" class="members">
      <div class="members__wrap">
        <!-- Section heading -->
        <div class="members__heading">
          <div class="members__heading-left">
            <h2 class="members__title">Member Overview</h2>
            <span class="members__count">{{ members.length }} active members</span>
          </div>
          <div class="members__menu">
            <Link href="/transaction-log" class="members__menu-link"><i class="fas fa-history"></i> Transaction Log</Link>
            <Link href="/income" class="members__menu-link"><i class="fas fa-arrow-down"></i> Income</Link>
            <Link href="/expense" class="members__menu-link"><i class="fas fa-arrow-up"></i> Expense</Link>
            <Link href="/balance-sheet" class="members__menu-link"><i class="fas fa-file-invoice-dollar"></i> Balance Sheet</Link>
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
            <div class="mrow">
              <div class="mrow__avatar" :style="{ background: avatarColor(idx) }">
                <img v-if="m.image" :src="imgUrl(m.image)" :alt="m.name" @error="(e) => (e.target.style.display = 'none')" />
                <span v-else>{{ initials(m.name) }}</span>
              </div>

              <div class="mrow__info">
                <div class="mrow__name">{{ m.name }}</div>
                <div class="mrow__txn">{{ m.deposit_count }} deposit{{ m.deposit_count !== 1 ? "s" : "" }}</div>
              </div>

              <div class="mrow__share-badge"
                   :class="m.last_adjustment ? (m.last_adjustment.adjustment_type === 'increase' ? 'mrow__share-badge--up' : 'mrow__share-badge--down') : ''">
                <span class="share-num">{{ m.number_of_share ?? 0 }}</span>
                <span class="share-lbl"><i class="fas fa-layer-group"></i> শেয়ার</span>
                <span v-if="m.last_adjustment" class="share-trend"
                      :title="adjustmentTitle(m.last_adjustment)">
                  <i :class="m.last_adjustment.adjustment_type === 'increase' ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ m.last_adjustment.adjustment_type === 'increase' ? '+' : '−' }}{{ Math.abs(m.last_adjustment.shares_delta || 0) }}
                </span>
              </div>

              <div class="mrow__pills">
                <span class="pill pill--blue">Total ৳{{ fmt(m.total_deposit) }}</span>
                <span v-if="isAdvancePaid(m.paid_till)" class="pill pill--paidtill" :title="'Paid in advance through ' + fmtMonth(m.paid_till)">
                  <i class="fas fa-calendar-check"></i>
                  Paid till {{ fmtMonth(m.paid_till) }}
                </span>
                <span class="pill" :class="m.total_due > 0 ? 'pill--red' : 'pill--complete'">
                  <i :class="m.total_due > 0 ? 'fas fa-circle-exclamation' : 'fas fa-circle-check'"></i>
                  {{ m.total_due > 0 ? "Due ৳" + fmt(m.total_due) : "সম্পূর্ণ পরিশোধ" }}
                </span>
              </div>

              <div class="mrow__right">
                <Link :href="'/member/' + m.id" class="mrow__btn">
                  <i class="fas fa-eye"></i> Details
                </Link>
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
    <!-- ═══════════════════════════════════════════
         ABOUT
    ════════════════════════════════════════════════ -->
    <section id="about" class="info-section">
      <div class="info-section__inner">
        <div class="info-section__header">
          <span class="info-section__badge">আমাদের সম্পর্কে</span>
          <h2 class="info-section__title">About Us</h2>
          <p class="info-section__sub">{{ siteName }} একটি সদস্য-ভিত্তিক সঞ্চয় ও বিনিয়োগ সংগঠন যেখানে প্রতিটি সদস্য একে অপরের আর্থিক উন্নয়নে অবদান রাখে।</p>
        </div>
        <div class="info-cards">
          <div class="info-card">
            <div class="info-card__icon">🏦</div>
            <h3 class="info-card__title">আমরা কারা</h3>
            <p class="info-card__text">আমরা একটি বিশ্বস্ত সঞ্চয় সংগঠন। আমাদের লক্ষ্য সদস্যদের মধ্যে আর্থিক সচেতনতা তৈরি করা এবং সঞ্চয়ের মাধ্যমে ভবিষ্যৎ নিরাপদ করা।</p>
          </div>
          <div class="info-card">
            <div class="info-card__icon">🤝</div>
            <h3 class="info-card__title">আমাদের দর্শন</h3>
            <p class="info-card__text">পারস্পরিক সহযোগিতা ও আস্থার উপর ভিত্তি করে আমরা প্রতিটি সদস্যের আর্থিক স্বাধীনতা নিশ্চিত করতে প্রতিশ্রুতিবদ্ধ।</p>
          </div>
          <div class="info-card">
            <div class="info-card__icon">📊</div>
            <h3 class="info-card__title">স্বচ্ছতা</h3>
            <p class="info-card__text">প্রতিটি লেনদেন ডিজিটালভাবে সংরক্ষিত এবং সকল সদস্যের কাছে সম্পূর্ণ স্বচ্ছ ও অ্যাক্সেসযোগ্য।</p>
          </div>
          <div class="info-card">
            <div class="info-card__icon">🏠</div>
            <h3 class="info-card__title">নিজস্ব বাসস্থান</h3>
            <p class="info-card__text">আমাদের মূল স্বপ্ন — একটি নিজস্ব বাসস্থান গড়ে তোলা, যেখানে আমরা সকল সদস্য ভবিষ্যতে একসাথে বসবাস করতে পারব।</p>
          </div>
          <div class="info-card">
            <div class="info-card__icon">🤲</div>
            <h3 class="info-card__title">অলাভজনক উদ্যোগ</h3>
            <p class="info-card__text">আমরা কোনো মুনাফামুখী প্রতিষ্ঠান নই। আমাদের প্রতিটি সঞ্চয় শুধুমাত্র সদস্যদের সম্মিলিত ভবিষ্যৎ পরিকল্পনা বাস্তবায়নের জন্য।</p>
          </div>
          <div class="info-card">
            <div class="info-card__icon">🏗️</div>
            <h3 class="info-card__title">রিয়েল এস্টেট পরিকল্পনা</h3>
            <p class="info-card__text">পর্যাপ্ত তহবিল সংগ্রহের পর আমরা রিয়েল এস্টেট ব্যবসায় বিনিয়োগ করব এবং সদস্যদের জন্য একটি স্থায়ী আবাসন নিশ্চিত করব।</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         OBJECTIVES
    ════════════════════════════════════════════════ -->
    <section id="objectives" class="info-section info-section--alt">
      <div class="info-section__inner">
        <div class="info-section__header">
          <span class="info-section__badge">আমাদের লক্ষ্য</span>
          <h2 class="info-section__title">Objectives</h2>
          <p class="info-section__sub">আমাদের সংগঠনের মূল উদ্দেশ্য সমূহ যা আমাদের কার্যক্রম পরিচালনার মূল ভিত্তি।</p>
        </div>
        <div class="objectives-list">
          <div class="obj-item">
            <div class="obj-item__num">০১</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">নিয়মিত সঞ্চয়ের অভ্যাস তৈরি</h4>
              <p class="obj-item__text">প্রতিটি সদস্যকে নিয়মিত সঞ্চয়ের অভ্যাস গড়ে তুলতে উৎসাহিত করা এবং আর্থিক শৃঙ্খলা প্রতিষ্ঠা করা।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০২</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">সদস্যদের আর্থিক নিরাপত্তা</h4>
              <p class="obj-item__text">সদস্যদের জমানো অর্থ সুরক্ষিত রাখা এবং প্রয়োজনে উপযুক্ত বিনিয়োগের মাধ্যমে লাভজনক রিটার্ন নিশ্চিত করা।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০৩</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">পারস্পরিক সহযোগিতা</h4>
              <p class="obj-item__text">সদস্যদের মধ্যে বন্ধুত্বপূর্ণ সম্পর্ক তৈরি করা এবং পরস্পরের সমস্যায় সহায়তা করার মানসিকতা গড়ে তোলা।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০৪</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">ডিজিটাল ও স্বচ্ছ ব্যবস্থাপনা</h4>
              <p class="obj-item__text">প্রযুক্তির সাহায্যে সকল আর্থিক কার্যক্রম ডিজিটালভাবে পরিচালনা করা এবং সকলের কাছে হিসাব-নিকাশ স্বচ্ছ রাখা।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০৫</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">বহুমুখী ব্যবসায়িক উদ্যোগ</h4>
              <p class="obj-item__text">পর্যাপ্ত তহবিল গড়ে ওঠার পর সদস্যদের সম্মিলিত সিদ্ধান্তে বিভিন্ন সেক্টরে ব্যবসায়িক উদ্যোগ গ্রহণ করা হবে, যা সংগঠনের আর্থিক ভিত্তিকে আরও মজবুত করবে।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০৬</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">কৌশলগত বিনিয়োগ</h4>
              <p class="obj-item__text">রিয়েল এস্টেটসহ বিভিন্ন লাভজনক খাতে সুচিন্তিতভাবে বিনিয়োগ করা হবে, যাতে সদস্যদের জমানো অর্থ দীর্ঘমেয়াদে সর্বোচ্চ মুনাফা অর্জন করতে পারে।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০৭</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">সামাজিক উন্নয়ন কার্যক্রম</h4>
              <p class="obj-item__text">শুধু নিজেদের উন্নয়নেই নয়, সমাজের সুবিধাবঞ্চিত মানুষদের পাশে দাঁড়ানো আমাদের দায়িত্ব। এলাকার শিক্ষা, স্বাস্থ্য ও অবকাঠামো উন্নয়নে আমরা সক্রিয় ভূমিকা রাখব।</p>
            </div>
          </div>
          <div class="obj-item">
            <div class="obj-item__num">০৮</div>
            <div class="obj-item__body">
              <h4 class="obj-item__title">ধর্মীয় ও মানবিক দান-সদকা</h4>
              <p class="obj-item__text">আখেরাতের পাথেয় হিসেবে মাদ্রাসা, মসজিদ ও এতিমখানার উন্নয়নে নিয়মিত দান করা হবে। দুনিয়ার পাশাপাশি পরকালীন কল্যাণের কথা মাথায় রেখে আমরা একটি ন্যায়ভিত্তিক সমাজ গড়তে চাই।</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         TERMS & CONDITION
    ════════════════════════════════════════════════ -->
    <section id="terms" class="info-section">
      <div class="info-section__inner">
        <div class="info-section__header">
          <span class="info-section__badge">নিয়মাবলী</span>
          <h2 class="info-section__title">Terms &amp; Condition</h2>
          <p class="info-section__sub">সংগঠনের সদস্যপদ ও কার্যক্রমের সাথে সম্পর্কিত গুরুত্বপূর্ণ শর্তাবলী।</p>
        </div>
        <div class="terms-grid">
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">💰</span> শুধুমাত্র জমার নীতি</h4>
            <ul class="terms-card__list">
              <li>সদস্যরা কেবলমাত্র অর্থ জমা দিতে পারবেন — সাধারণ অবস্থায় উত্তোলন সম্পূর্ণ নিষিদ্ধ।</li>
              <li>এটি একটি দীর্ঘমেয়াদী পরিকল্পনা — তাৎক্ষণিক প্রয়োজনে এখানে হাত দেওয়া যাবে না।</li>
              <li>জমানো অর্থ সংগঠনের চূড়ান্ত লক্ষ্য পূরণের আগ পর্যন্ত সম্পূর্ণ সুরক্ষিত থাকবে।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">🤝</span> উত্তোলনের বিশেষ বিধান</h4>
            <ul class="terms-card__list">
              <li>কোনো সদস্য উত্তোলন করতে চাইলে সকল সদস্যের সর্বসম্মতিক্রমে তা বিবেচনা করা যেতে পারে।</li>
              <li>সর্বসম্মতি ছাড়া কোনো অবস্থায়ই উত্তোলনের সুযোগ দেওয়া হবে না।</li>
              <li>একজনের উত্তোলন সংগঠনের সামগ্রিক লক্ষ্যকে ক্ষতিগ্রস্ত করতে পারে — এ বিষয়ে সকলকে সচেতন থাকতে হবে।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">⚖️</span> লাভ-ক্ষতির সম্মিলিত দায়বদ্ধতা</h4>
            <ul class="terms-card__list">
              <li>যেকোনো বিনিয়োগে লাভ বা ক্ষতি হতে পারে — এটি স্বাভাবিক ব্যবসায়িক ঝুঁকি।</li>
              <li>লাভ-ক্ষতির জন্য কেউ কাউকে কোনোভাবেই দোষারোপ করতে পারবেন না।</li>
              <li>সকল সদস্য স্বেচ্ছায় ও সজ্ঞানে এই ঝুঁকি গ্রহণ করেছেন বলে বিবেচিত হবে।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">🗳️</span> সংখ্যাগরিষ্ঠতার ভিত্তিতে সিদ্ধান্ত</h4>
            <ul class="terms-card__list">
              <li>সংগঠনের যেকোনো নতুন সিদ্ধান্ত সংখ্যাগরিষ্ঠ সদস্যদের ভোটের মাধ্যমে নির্ধারিত হবে।</li>
              <li>কোনো একজনের ইচ্ছা বা স্বার্থে গোটা সংগঠন পরিচালিত হবে না।</li>
              <li>ভোটের রায় সকলের জন্য বাধ্যকর এবং তা মেনে নেওয়া প্রতিটি সদস্যের দায়িত্ব।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">📅</span> মাসিক সঞ্চয়ের দায়িত্ব</h4>
            <ul class="terms-card__list">
              <li>প্রতিটি সদস্যের প্রতি মাসে নির্ধারিত পরিমাণ জমা দেওয়ার চেষ্টা করতে হবে।</li>
              <li>এক মাস দিতে না পারলে পরের মাসে দুই মাসের টাকা একসাথে দেওয়া সম্পূর্ণ গ্রহণযোগ্য।</li>
              <li>ধারাবাহিকভাবে দীর্ঘসময় জমা না দিলে কমিটি বিষয়টি আলোচনার মাধ্যমে সমাধান করবে।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">📊</span> স্বচ্ছ হিসাব ও জবাবদিহিতা</h4>
            <ul class="terms-card__list">
              <li>সংগঠনের সকল আয়-ব্যয়ের হিসাব প্রতিটি সদস্যের কাছে সম্পূর্ণ উন্মুক্ত থাকবে।</li>
              <li>নিয়মিত সভায় আর্থিক প্রতিবেদন উপস্থাপন করা হবে।</li>
              <li>যেকোনো সদস্য যেকোনো সময় হিসাব যাচাই করে দেখার অধিকার রাখেন।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">🤲</span> পারস্পরিক শ্রদ্ধা ও ঐক্য</h4>
            <ul class="terms-card__list">
              <li>সংগঠনের সকল সদস্য একে অপরকে আন্তরিকভাবে সম্মান ও শ্রদ্ধা করবেন।</li>
              <li>মতপার্থক্য হলে আলোচনার মাধ্যমে সমাধান করতে হবে — কোনো বিরোধ বাইরে নেওয়া যাবে না।</li>
              <li>সংগঠনের স্বার্থকে ব্যক্তিস্বার্থের উপরে স্থান দেওয়া প্রতিটি সদস্যের নৈতিক দায়িত্ব।</li>
            </ul>
          </div>
          <div class="terms-card">
            <h4 class="terms-card__title"><span class="terms-card__icon">🚪</span> সংগঠন ত্যাগের বিধান</h4>
            <ul class="terms-card__list">
              <li>কোনো সদস্য সংগঠন ছেড়ে যেতে চাইলে তা সকল সদস্যের সভায় আলোচনার মাধ্যমে নিষ্পত্তি করতে হবে।</li>
              <li>সংগঠন ত্যাগের ক্ষেত্রে জমানো অর্থের বিষয়টি সংগঠনের লক্ষ্য ও সার্বিক অবস্থা বিবেচনা করে নির্ধারিত হবে।</li>
              <li>সংগঠনের কোনো গোপনীয় তথ্য, পরিকল্পনা বা আর্থিক বিষয় বাইরে প্রকাশ করা সম্পূর্ণ নিষিদ্ধ।</li>
            </ul>
          </div>

          <div class="terms-card terms-card--nominee">
            <h4 class="terms-card__title"><span class="terms-card__icon">🛡️</span> নমিনি তথ্য প্রদানের বাধ্যবাধকতা</h4>
            <ul class="terms-card__list">
              <li>প্রতিটি সদস্যকে সংগঠনে যোগদানের সময় অথবা যত দ্রুত সম্ভব একজন নমিনির নাম, সম্পর্ক ও জাতীয় পরিচয়পত্র নম্বর (NID) প্রদান করতে হবে।</li>
              <li>নমিনির তথ্য না থাকলে ভবিষ্যতে যেকোনো জরুরি পরিস্থিতিতে অর্থ প্রাপ্তির প্রক্রিয়া জটিল হয়ে যেতে পারে — তাই এটি আবশ্যিক।</li>
              <li>নমিনির তথ্য পরিবর্তন করতে হলে সংগঠনের কর্তৃপক্ষকে লিখিতভাবে অবহিত করতে হবে।</li>
            </ul>
          </div>

          <div class="terms-card terms-card--nominee">
            <h4 class="terms-card__title"><span class="terms-card__icon">👨‍👩‍👧</span> নমিনির মাধ্যমে অর্থ প্রাপ্তির বিধান</h4>
            <ul class="terms-card__list">
              <li>কোনো সদস্যের মৃত্যু, দুর্ঘটনা বা গুরুতর অসুস্থতাজনিত কারণে তিনি নিজে অর্থ গ্রহণ করতে অক্ষম হলে — তাঁর নমিনি সেই সদস্যের জমানো সমস্ত অর্থ গ্রহণ করার অধিকারী হবেন।</li>
              <li>সংগঠনের সকল সদস্য মিলে আলোচনা করে সর্বসম্মতিক্রমে সিদ্ধান্ত গ্রহণের পর নমিনিকে সদস্যের সমুদয় জমানো অর্থ যথাযথভাবে বুঝিয়ে দেওয়া হবে।</li>
              <li>এই বিধান নিশ্চিত করে যে কোনো সদস্যের কষ্টার্জিত সঞ্চয় তাঁর অনুপস্থিতিতেও তাঁর পরিবারের কাছে নিরাপদে পৌঁছাবে।</li>
            </ul>
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
    const animObserver = new IntersectionObserver(
      (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add("is-visible"); }),
      { threshold: 0.1, rootMargin: "0px 0px -50px 0px" },
    );
    setTimeout(() => {
      document.querySelectorAll(".animate-on-scroll, .section-title, .section-sub, .service-card, .about-stat, .tcard").forEach((el) => {
        el.classList.add("animate-on-scroll");
        animObserver.observe(el);
      });
    }, 100);

    // Handle scroll target set by header when navigating from sub-pages
    const target = sessionStorage.getItem('scrollTarget');
    if (target) {
      sessionStorage.removeItem('scrollTarget');
      setTimeout(() => {
        if (target === 'contact') {
          window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        } else {
          const el = document.getElementById(target);
          if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }, 150);
    }
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
    fmt(v) {
      return Number(v || 0).toLocaleString("en-BD", { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },
    fmtDate(d) {
      if (!d) return "—";
      return new Date(d).toLocaleDateString("en-BD", { year: "numeric", month: "short", day: "numeric" });
    },
    fmtMonth(d) {
      if (!d) return "—";
      return new Date(d).toLocaleDateString("en-BD", { year: "numeric", month: "long" });
    },
    isAdvancePaid(paidTill) {
      // Show "Paid till X" only when X is BEYOND the current month
      // (current month payment is just basic, not advance)
      if (!paidTill) return false;
      const now = new Date();
      const currentYM = now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0");
      const paidYM = String(paidTill).slice(0, 7);
      return paidYM > currentYM;
    },
    adjustmentTitle(adj) {
      if (!adj) return "";
      const arrow = adj.adjustment_type === "increase" ? "↑" : "↓";
      return `${arrow} ${adj.from_shares} → ${adj.to_shares} on ${this.fmtDate(adj.created_at)}`;
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
    imgUrl(path) {
      if (!path) return null;
      if (/^https?:\/\//.test(path) || path.startsWith('/')) return path;
      return '/' + path;
    },
    scrollTo(id) {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
      this.activeSection = id;
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
  padding: 72px 0px 0;
  overflow: hidden;
  background:
    linear-gradient(160deg, rgba(3, 6, 18, 0.94) 0%, rgba(8, 14, 38, 0.90) 100%),
    url("/frontend/asset/img/home_page/banner.png") center/cover no-repeat;
  color: #e2e8f0;
}

/* Animated floating orbs */
.hero__orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  opacity: 0.35;
}
.hero__orb--1 {
  width: 500px; height: 500px;
  background: radial-gradient(circle, #6366f1, transparent 70%);
  top: -150px; left: -100px;
  animation: orbFloat 8s ease-in-out infinite;
}
.hero__orb--2 {
  width: 400px; height: 400px;
  background: radial-gradient(circle, #10b981, transparent 70%);
  top: 50px; right: -80px;
  animation: orbFloat 10s ease-in-out infinite reverse;
}
.hero__orb--3 {
  width: 300px; height: 300px;
  background: radial-gradient(circle, #8b5cf6, transparent 70%);
  bottom: 80px; left: 30%;
  animation: orbFloat 7s ease-in-out infinite 2s;
}
.hero__orb--4 {
  width: 250px; height: 250px;
  background: radial-gradient(circle, #14b8a6, transparent 70%);
  bottom: 40px; right: 25%;
  animation: orbFloat 9s ease-in-out infinite 1s reverse;
}

/* Dot-grid texture */
.hero__grid {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
  background-size: 32px 32px;
  pointer-events: none;
}

.hero__overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(ellipse at 15% 50%, rgba(99,102,241,0.12) 0%, transparent 50%),
    radial-gradient(ellipse at 85% 40%, rgba(16,185,129,0.10) 0%, transparent 50%);
}

.hero__content {
  position: relative;
  z-index: 1;
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 56px;
  padding-bottom: 100px;
}

/* ── Identity ──────────────────────────────────────────────────── */
.hero__identity {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
  min-width: 200px;
  animation: fadeSlideUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.hero__badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 6px 16px;
  background: rgba(99,102,241,0.10);
  border: 1px solid rgba(99,102,241,0.35);
  border-radius: 30px;
  font-size: 11px;
  font-weight: 600;
  color: #a5b4fc;
  letter-spacing: 0.6px;
  margin-bottom: 20px;
  text-transform: uppercase;
}
.hero__badge-pulse {
  display: inline-block;
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #34d399;
  box-shadow: 0 0 0 0 rgba(52,211,153,0.6);
  animation: badgePulse 1.8s ease-out infinite;
}

/* Logo rings */
.hero__logo-wrap {
  position: relative;
  width: 150px; height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 22px;
}
.hero__logo-ring-spin {
  position: absolute;
  inset: -6px;
  border-radius: 50%;
  border: 2px solid transparent;
  border-top-color: rgba(99,102,241,0.7);
  border-right-color: rgba(16,185,129,0.5);
  animation: spinRing 4s linear infinite;
}
.hero__logo-ring-spin--rev {
  inset: -14px;
  border-top-color: rgba(139,92,246,0.5);
  border-right-color: transparent;
  border-bottom-color: rgba(16,185,129,0.4);
  animation-duration: 6s;
  animation-direction: reverse;
}
.hero__logo-ring {
  width: 140px; height: 140px;
  border-radius: 50%;
  overflow: hidden;
  background: rgba(255,255,255,0.07);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid rgba(99,102,241,0.4);
  box-shadow:
    0 0 40px rgba(99,102,241,0.22),
    0 0 80px rgba(99,102,241,0.10),
    inset 0 0 30px rgba(99,102,241,0.08);
}
.hero__logo-img {
  width: 100%; height: 100%;
  object-fit: cover;
}
.hero__logo-text {
  font-size: 44px;
  font-weight: 900;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero__name {
  font-size: 28px;
  font-weight: 900;
  text-align: center;
  margin: 0 0 10px;
  background: linear-gradient(135deg, #e0e7ff 0%, #a5b4fc 40%, #34d399 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 2px 16px rgba(99,102,241,0.4));
  letter-spacing: 0.5px;
}

.hero__slogan-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}
.hero__slogan-sep {
  display: inline-block;
  width: 28px; height: 1px;
  background: linear-gradient(90deg, transparent, rgba(99,102,241,0.5));
}
.hero__slogan-row .hero__slogan-sep:last-child {
  background: linear-gradient(90deg, rgba(99,102,241,0.5), transparent);
}
.hero__slogan {
  font-size: 13px;
  font-weight: 700;
  color: #94a3b8;
  letter-spacing: 1.8px;
  text-transform: uppercase;
  margin: 0;
}

.hero__desc {
  font-size: 13.5px;
  color: #64748b;
  text-align: center;
  line-height: 1.75;
  margin: 0 0 24px;
}

.hero__cta-row {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: center;
}
.hero__cta {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 10px 22px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.25s;
}
.hero__cta--primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  box-shadow: 0 4px 20px rgba(99,102,241,0.35);
}
.hero__cta--primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(99,102,241,0.5);
}
.hero__cta--outline {
  background: transparent;
  border: 1px solid rgba(99,102,241,0.4);
  color: #a5b4fc;
}
.hero__cta--outline:hover {
  background: rgba(99,102,241,0.12);
  border-color: rgba(99,102,241,0.65);
  transform: translateY(-2px);
}

/* ── Stats grid ────────────────────────────────────────────────── */
.hero__stats {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.stat-card {
  position: relative;
  overflow: hidden;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 22px 20px 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  backdrop-filter: blur(20px);
  transition: transform 0.3s cubic-bezier(0.22,1,0.36,1), box-shadow 0.3s, border-color 0.3s;
  animation: fadeSlideUp 0.6s cubic-bezier(0.22,1,0.36,1) both;
}
.stat-card:nth-child(1) { animation-delay: 0.10s; }
.stat-card:nth-child(2) { animation-delay: 0.18s; }
.stat-card:nth-child(3) { animation-delay: 0.26s; }
.stat-card:nth-child(4) { animation-delay: 0.34s; }
.stat-card:nth-child(5) { animation-delay: 0.42s; }
.stat-card:nth-child(6) { animation-delay: 0.50s; }

.stat-card:hover {
  transform: translateY(-6px) scale(1.02);
}

/* Top colored accent bar */
.stat-card__accent {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  border-radius: 20px 20px 0 0;
}

/* Shimmer sweep on hover */
.stat-card::after {
  content: "";
  position: absolute;
  top: 0; left: -100%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
  transition: left 0.5s;
}
.stat-card:hover::after { left: 150%; }

.stat-card__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}
.stat-card__icon-wrap {
  width: 40px; height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
}
.stat-card__trend {
  font-size: 13px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
}
.stat-card__trend--up   { background: rgba(16,185,129,0.12); color: #34d399; }
.stat-card__trend--down { background: rgba(239,68,68,0.12);  color: #f87171; }

.stat-card__label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.9px;
  color: #475569;
  font-weight: 600;
}
.stat-card__value {
  font-size: 22px;
  font-weight: 900;
  letter-spacing: -0.3px;
}

/* Progress bar at bottom */
.stat-card__bar {
  margin-top: 10px;
  height: 3px;
  background: rgba(255,255,255,0.06);
  border-radius: 10px;
  overflow: hidden;
}
.stat-card__bar-fill {
  height: 100%;
  border-radius: 10px;
  width: 0;
  animation: barGrow 1.2s cubic-bezier(0.22,1,0.36,1) forwards 0.8s;
}

/* Per-colour theming */
.stat-card--green {
  border-color: rgba(16,185,129,0.22);
}
.stat-card--green:hover { box-shadow: 0 12px 40px rgba(16,185,129,0.18); border-color: rgba(16,185,129,0.45); }
.stat-card--green .stat-card__accent   { background: linear-gradient(90deg,#10b981,#34d399); }
.stat-card--green .stat-card__icon-wrap{ background: rgba(16,185,129,0.12); color: #34d399; }
.stat-card--green .stat-card__value    { color: #34d399; }
.stat-card--green .stat-card__bar-fill { background: linear-gradient(90deg,#10b981,#34d399); width: 78%; }

.stat-card--blue {
  border-color: rgba(59,130,246,0.22);
}
.stat-card--blue:hover { box-shadow: 0 12px 40px rgba(59,130,246,0.18); border-color: rgba(59,130,246,0.45); }
.stat-card--blue .stat-card__accent   { background: linear-gradient(90deg,#3b82f6,#60a5fa); }
.stat-card--blue .stat-card__icon-wrap{ background: rgba(59,130,246,0.12); color: #60a5fa; }
.stat-card--blue .stat-card__value    { color: #60a5fa; }
.stat-card--blue .stat-card__bar-fill { background: linear-gradient(90deg,#3b82f6,#60a5fa); width: 85%; }

.stat-card--purple {
  border-color: rgba(139,92,246,0.22);
}
.stat-card--purple:hover { box-shadow: 0 12px 40px rgba(139,92,246,0.18); border-color: rgba(139,92,246,0.45); }
.stat-card--purple .stat-card__accent   { background: linear-gradient(90deg,#8b5cf6,#a78bfa); }
.stat-card--purple .stat-card__icon-wrap{ background: rgba(139,92,246,0.12); color: #a78bfa; }
.stat-card--purple .stat-card__value    { color: #a78bfa; }
.stat-card--purple .stat-card__bar-fill { background: linear-gradient(90deg,#8b5cf6,#a78bfa); width: 65%; }

.stat-card--orange {
  border-color: rgba(245,158,11,0.22);
}
.stat-card--orange:hover { box-shadow: 0 12px 40px rgba(245,158,11,0.18); border-color: rgba(245,158,11,0.45); }
.stat-card--orange .stat-card__accent   { background: linear-gradient(90deg,#f59e0b,#fbbf24); }
.stat-card--orange .stat-card__icon-wrap{ background: rgba(245,158,11,0.12); color: #fbbf24; }
.stat-card--orange .stat-card__value    { color: #fbbf24; }
.stat-card--orange .stat-card__bar-fill { background: linear-gradient(90deg,#f59e0b,#fbbf24); width: 52%; }

.stat-card--red {
  border-color: rgba(239,68,68,0.22);
}
.stat-card--red:hover { box-shadow: 0 12px 40px rgba(239,68,68,0.18); border-color: rgba(239,68,68,0.45); }
.stat-card--red .stat-card__accent   { background: linear-gradient(90deg,#ef4444,#f87171); }
.stat-card--red .stat-card__icon-wrap{ background: rgba(239,68,68,0.12); color: #f87171; }
.stat-card--red .stat-card__value    { color: #f87171; }
.stat-card--red .stat-card__bar-fill { background: linear-gradient(90deg,#ef4444,#f87171); width: 40%; }

.stat-card--teal {
  border-color: rgba(20,184,166,0.22);
}
.stat-card--teal:hover { box-shadow: 0 12px 40px rgba(20,184,166,0.18); border-color: rgba(20,184,166,0.45); }
.stat-card--teal .stat-card__accent   { background: linear-gradient(90deg,#14b8a6,#2dd4bf); }
.stat-card--teal .stat-card__icon-wrap{ background: rgba(20,184,166,0.12); color: #2dd4bf; }
.stat-card--teal .stat-card__value    { color: #2dd4bf; }
.stat-card--teal .stat-card__bar-fill { background: linear-gradient(90deg,#14b8a6,#2dd4bf); width: 70%; }

/* ─── Wave ───────────────────────────────────────────────────── */
.hero__wave {
  position: relative;
  width: 100%;
  height: 130px;
  line-height: 0;
  overflow: hidden;
}
.wl {
    position: absolute;
    bottom: -40px;
    left: -20px;
    width: 120%;
    height: 170px;
}
.wl--1  { animation: waveShift1 7s ease-in-out infinite; z-index: 1; }
.wl--2  { animation: waveShift2 9s ease-in-out infinite; z-index: 2; }
.wl--3  { animation: waveShift3 5s ease-in-out infinite; z-index: 3; }
.wl--base { z-index: 4; }

@keyframes waveShift1 {
  0%, 100% { transform: translateX(0px) scaleX(1); }
  50%       { transform: translateX(-35px) scaleX(1.03); }
}
@keyframes waveShift2 {
  0%, 100% { transform: translateX(0px) scaleX(1); }
  50%       { transform: translateX(28px) scaleX(1.02); }
}
@keyframes waveShift3 {
  0%, 100% { transform: translateX(0px) scaleX(1); }
  50%       { transform: translateX(-18px) scaleX(1.01); }
}

/* ─── KEYFRAMES ──────────────────────────────────────────────── */
@keyframes orbFloat {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33%       { transform: translate(30px, -40px) scale(1.05); }
  66%       { transform: translate(-20px, 20px) scale(0.97); }
}
@keyframes spinRing {
  to { transform: rotate(360deg); }
}
@keyframes badgePulse {
  0%   { box-shadow: 0 0 0 0 rgba(52,211,153,0.6); }
  70%  { box-shadow: 0 0 0 8px rgba(52,211,153,0); }
  100% { box-shadow: 0 0 0 0 rgba(52,211,153,0); }
}
@keyframes fadeSlideUp {
  from { opacity: 0; transform: translateY(28px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes barGrow {
  from { width: 0; }
}

/* ─── MEMBERS ────────────────────────────────────────────────── */
.members {
  background: #060a14;
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

.mrow__share-badge {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  min-width: 72px;
  background: rgba(99,102,241,0.1);
  border: 1px solid rgba(99,102,241,0.35);
  border-radius: 14px;
  padding: 8px 14px;
  gap: 2px;
  position: relative;
}
.mrow__share-badge--up {
  background: rgba(16,185,129,0.1);
  border-color: rgba(16,185,129,0.4);
}
.mrow__share-badge--down {
  background: rgba(248,113,113,0.1);
  border-color: rgba(248,113,113,0.4);
}
.share-trend {
  position: absolute;
  top: -8px;
  right: -8px;
  display: inline-flex;
  align-items: center;
  gap: 2px;
  padding: 2px 7px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.3px;
  border: 1.5px solid #0f172a;
  white-space: nowrap;
  cursor: help;
}
.mrow__share-badge--up   .share-trend {
  background: #10b981;
  color: #fff;
}
.mrow__share-badge--down .share-trend {
  background: #f87171;
  color: #fff;
}
.share-trend i { font-size: 9px; }
.share-num {
  font-size: 26px;
  font-weight: 900;
  line-height: 1;
  background: linear-gradient(135deg, #a5b4fc, #818cf8);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.share-lbl {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #6366f1;
  display: flex;
  align-items: center;
  gap: 4px;
}
.share-lbl i { font-size: 9px; }
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

/* (deposit history panel moved to MemberDetail page) */

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
.pill--complete {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.22);
}
.pill--paidtill {
  background: rgba(168, 85, 247, 0.1);
  color: #c4b5fd;
  border: 1px solid rgba(168, 85, 247, 0.22);
}
.pill--paidtill i {
  margin-right: 4px;
  font-size: 10px;
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
@media (max-width: 1024px) {
  .hero__stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 900px) {
  .hero__content {
    flex-direction: column;
    gap: 36px;
    padding-bottom: 72px;
  }
  .hero__identity { min-width: unset; width: 100%; }
  .hero__stats {
    grid-template-columns: repeat(3, 1fr);
    width: 100%;
  }
  .mrow { flex-wrap: wrap; }
  .mrow__info { min-width: auto; }
}
@media (max-width: 640px) {
  .hero__stats { grid-template-columns: repeat(2, 1fr); }
  .mrow__pills { gap: 5px; }
  .hero { padding: 48px 16px 0; }
  .members { padding: 32px 16px 48px; }
  .hero__name { font-size: 22px; }
  .stat-card__value { font-size: 18px; }
}


/* ─── INFO SECTIONS ──────────────────────────────────────────── */
.info-section {
  background: #0d1320;
  padding: 72px 24px;
}
.info-section--alt {
  background: #0a0f1d;
}
.info-section__inner {
  max-width: 1280px;
  margin: 0 auto;
}
.info-section__header {
  text-align: center;
  margin-bottom: 48px;
}
.info-section__badge {
  display: inline-block;
  padding: 5px 16px;
  background: rgba(99, 102, 241, 0.12);
  border: 1px solid rgba(99, 102, 241, 0.3);
  border-radius: 30px;
  font-size: 12px;
  font-weight: 600;
  color: #a5b4fc;
  letter-spacing: 0.5px;
  margin-bottom: 14px;
}
.info-section__title {
  font-size: 32px;
  font-weight: 800;
  color: #f1f5f9;
  margin: 0 0 12px;
  background: linear-gradient(135deg, #a5b4fc, #34d399);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.info-section__sub {
  font-size: 15px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.75;
}

/* About cards */
.info-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
.info-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 20px;
  padding: 32px 28px;
  transition: transform 0.2s, border-color 0.2s;
}
.info-card:hover {
  transform: translateY(-4px);
  border-color: rgba(99, 102, 241, 0.35);
}
.info-card__icon {
  font-size: 36px;
  display: block;
  margin-bottom: 16px;
}
.info-card__title {
  font-size: 17px;
  font-weight: 700;
  color: #e2e8f0;
  margin: 0 0 10px;
}
.info-card__text {
  font-size: 13.5px;
  color: #64748b;
  line-height: 1.75;
  margin: 0;
}

/* Objectives list */
.objectives-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.obj-item {
  display: flex;
  gap: 20px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 28px 24px;
  transition: border-color 0.2s;
}
.obj-item:hover {
  border-color: rgba(16, 185, 129, 0.3);
}
.obj-item__num {
  font-size: 28px;
  font-weight: 900;
  color: rgba(16, 185, 129, 0.25);
  flex-shrink: 0;
  line-height: 1;
}
.obj-item__title {
  font-size: 15px;
  font-weight: 700;
  color: #e2e8f0;
  margin: 0 0 8px;
}
.obj-item__text {
  font-size: 13.5px;
  color: #64748b;
  line-height: 1.75;
  margin: 0;
}

/* Terms grid */
.terms-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.terms-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 28px 24px;
  transition: border-color 0.2s;
}
.terms-card:hover {
  border-color: rgba(245, 158, 11, 0.3);
}
.terms-card--nominee {
  border-color: rgba(139, 92, 246, 0.2);
  background: rgba(139, 92, 246, 0.04);
}
.terms-card--nominee:hover {
  border-color: rgba(139, 92, 246, 0.4);
}
.terms-card--nominee .terms-card__title {
  color: #c4b5fd;
}
.terms-card__title {
  font-size: 15px;
  font-weight: 700;
  color: #e2e8f0;
  margin: 0 0 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.terms-card__icon {
  font-style: normal;
}
.terms-card__list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.terms-card__list li {
  font-size: 13.5px;
  color: #64748b;
  line-height: 1.6;
  padding-left: 16px;
  position: relative;
}
.terms-card__list li::before {
  content: "›";
  position: absolute;
  left: 0;
  color: #f59e0b;
  font-weight: 700;
}

@media (max-width: 860px) {
  .info-cards { grid-template-columns: 1fr; }
  .objectives-list { grid-template-columns: 1fr; }
  .terms-grid { grid-template-columns: 1fr; }
  .info-section { padding: 48px 20px; }
}
@media (max-width: 540px) {
  .info-section__title { font-size: 24px; }
}
</style>
