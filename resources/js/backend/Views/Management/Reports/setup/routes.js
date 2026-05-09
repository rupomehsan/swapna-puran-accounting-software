import Layout       from "../pages/Layout.vue";
import Ledger        from "../pages/Ledger.vue";
import TrialBalance  from "../pages/TrialBalance.vue";
import ProfitLoss    from "../pages/ProfitLoss.vue";
import BalanceSheet  from "../pages/BalanceSheet.vue";

const routes = {
    path: "reports",
    component: Layout,
    children: [
        { path: "ledger",        name: "ReportLedger",       component: Ledger       },
        { path: "trial-balance", name: "ReportTrialBalance", component: TrialBalance },
        { path: "profit-loss",   name: "ReportProfitLoss",   component: ProfitLoss   },
        { path: "balance-sheet", name: "ReportBalanceSheet", component: BalanceSheet },
    ],
};

export default routes;
