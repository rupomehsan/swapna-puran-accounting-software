<template>
  <!--Start sidebar-wrapper-->
  <div id="sidebar-wrapper">
    <div class="brand-logo">
      <router-link
        :to="{ name: `adminDashboard` }"
        class="d-flex align-items-center"
      >
        <img
          :src="`${get_setting_value('image') ?? 'avatar.png'} `"
          class="logo-icon"
          alt="logo icon"
        />
        <h5 class="logo-text">Super Admin Panel</h5>
      </router-link>
      <div class="close-btn">
        <i class="zmdi zmdi-close" @click="toggle_menu"></i>
      </div>
    </div>

    <div class="text-center mt-3">
      <img
        class="rounded-circle p-1"
        height="70"
        width="70"
        :src="`${auth_info.image ?? 'avatar.png'}`"
        alt=""
      />
      <p class="mt-2">Mr. {{ auth_info.name }}</p>
    </div>
    <hr />
    <ul class="metismenu" id="menu">

      <!-- ── Overview ─────────────────────────────── -->
      <side-bar-single-menu
        :icon="`zmdi zmdi-view-dashboard`"
        :menu_title="`Dashboard`"
        :route_name="`adminDashboard`"
        :class="'border border-primary rounded mb-1'"
      />

      <!-- ── Members ──────────────────────────────── -->
      <li class="menu-label">Members</li>
      <side-bar-drop-down-menus
        :icon="`fa fa-users`"
        :menu_title="`User Management`"
        :menus="[
          { route_name: 'AllUser', title: 'Members',    icon: 'fa fa-user'   },
          { route_name: 'AllRole', title: 'User Roles', icon: 'fa fa-shield' },
        ]"
      />

      <!-- ── Member Transactions ──────────────────── -->
      <li class="menu-label">Transactions</li>
      <side-bar-drop-down-menus
        :icon="`fa fa-money`"
        :menu_title="`Member Finance`"
        :menus="[
          { route_name: 'AllDeposit',    title: 'Deposits',     icon: 'fa fa-arrow-circle-down' },
          { route_name: 'AllDue',        title: 'Due',          icon: 'fa fa-clock-o'           },
          { route_name: 'AllWithdrawal', title: 'Withdrawals',  icon: 'fa fa-arrow-circle-up'   },
        ]"
      />
      <side-bar-drop-down-menus
        :icon="`fa fa-file-text`"
        :menu_title="`Income & Expense`"
        :menus="[
          { route_name: 'AllIncomeEntry',  title: 'Income Entry',  icon: 'fa fa-plus-square'  },
          { route_name: 'AllExpenseEntry', title: 'Expense Entry', icon: 'fa fa-minus-square' },
        ]"
      />

      <!-- ── Bookkeeping ────────────────────────── -->
      <li class="menu-label">Bookkeeping</li>
      <side-bar-single-menu :icon="`fa fa-university`"      :menu_title="`Accounts`"        :route_name="`AllAccount`" />
      <side-bar-drop-down-menus
        :icon="`fa fa-book`"
        :menu_title="`Journals`"
        :menus="[
          { route_name: 'AllJournal',      title: 'Journal',         icon: 'fa fa-book'            },
          { route_name: 'AllJournalEntry', title: 'Journal Entries', icon: 'fa fa-pencil-square-o' },
        ]"
      />

      <!-- ── Reports ──────────────────────────────── -->
      <li class="menu-label">Reports</li>
      <side-bar-single-menu :icon="`fa fa-exchange`" :menu_title="`Transaction Log`" :route_name="`AllTransactionLog`" />
      <side-bar-drop-down-menus
        :icon="`fa fa-bar-chart`"
        :menu_title="`Financial Reports`"
        :menus="[
          { route_name: 'ReportLedger',       title: 'Ledger',        icon: 'fa fa-list-alt'      },
          { route_name: 'ReportTrialBalance', title: 'Trial Balance', icon: 'fa fa-balance-scale' },
          { route_name: 'ReportProfitLoss',   title: 'Profit & Loss', icon: 'fa fa-line-chart'    },
          { route_name: 'ReportBalanceSheet', title: 'Balance Sheet', icon: 'fa fa-table'         },
        ]"
      />

      <!-- ── Settings ─────────────────────────────── -->
      <li class="menu-label">Settings</li>
      <side-bar-single-menu :icon="`fa fa-cog`" :menu_title="`Configuration`" :route_name="`AllConfiguration`" />

    </ul>
  </div>
</template>

<script>
//auth_store
import { auth_store } from "../../../../GlobalStore/auth_store";
import { site_settings_store } from "../../../../GlobalStore/site_settings_store";
import { mapState, mapActions } from "pinia";
//components
import SideBarDropDownMenus from "./SideBarDropDownMenus.vue";
import SideBarSingleMenu from "./SideBarSingleMenu.vue";
export default {
  components: { SideBarDropDownMenus, SideBarSingleMenu },
  methods: {
    ...mapActions(site_settings_store, {
      get_setting_value: "get_setting_value",
    }),

    logout_submit: function () {
      let confirm = window.confirm("logout");
      if (confirm) {
        this.log_out();
      }
    },
    toggle_menu: function () {
      document.getElementById("wrapper").classList.toggle("toggled");
    },
    hide_menu: function () {
      document.getElementById("wrapper").classList.add("toggled");
    },
    onDashboardClick() {
      // Close all dropdown menus when dashboard is clicked
      window.dispatchEvent(
        new CustomEvent("collapse-all-menus", {
          detail: { except: null },
        }),
      );
    },
  },
  watch: {
    $route(to, from) {
      // Auto-hide sidebar when navigating to task board
      if (to.name === "TaskBoard") {
        this.hide_menu();
      }
    },
  },
  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
  },
};
</script>

<style>
/* Dashboard active state styling */
#menu > li.active > a,
#menu > li > a.active {
  background-color: #007bff !important;
  color: white !important;
  border-radius: 4px;
  margin: 2px;
}

/* Section label dividers */
#menu li.menu-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  color: #8a9bb0;
  padding: 14px 15px 4px;
  pointer-events: none;
  border-top: 1px solid rgba(255,255,255,0.07);
  margin-top: 4px;
}
</style>
<!-- <side-bar-drop-down-menus :icon="`fa fa-plus`" :icon_image="`https://files.etek.com.bd/images/icon_sales.png`"
    :menu_title="`title Management`" :menus="[
                {
                    route_name: `AllUser`,
                    title: `title`,
                    icon: `zmdi zmdi-dot-circle-alt`,
                },
            ]" /> -->
