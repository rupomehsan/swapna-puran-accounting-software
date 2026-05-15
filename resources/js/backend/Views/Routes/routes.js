//app layout
import Layout from "../Layouts/Layout.vue";
//Dashboard
import Dashboard from "../Management/Dashboard/Dashboard.vue";
//SettingsRoutes
import SettingsRoutes from "../Management/Settings/setup/routes.js";
//UserRoutes
import UserRoutes from "../Management/UserManagement/User/setup/routes.js";
//routesimport ConfigurationRoutes from '../Management/Configuration/setup/routes.js';
import DepositRoutes from '../Management/Deposit/setup/routes.js';
import JournalEntryRoutes from '../Management/JournalEntry/setup/routes.js';
import JournalRoutes from '../Management/Journal/setup/routes.js';
import AccountRoutes from '../Management/Account/setup/routes.js';
import WithdrawalRoutes from '../Management/Withdrawal/setup/routes.js';
import DueRoutes            from '../Management/Due/setup/routes.js';
import ShareAdjustmentRoutes from '../Management/ShareAdjustment/setup/routes.js';
import ExpenseEntryRoutes   from '../Management/ExpenseEntry/setup/routes.js';
import IncomeEntryRoutes    from '../Management/IncomeEntry/setup/routes.js';
import TransactionLogRoutes from '../Management/TransactionLog/setup/routes.js';
import ReportRoutes         from '../Management/Reports/setup/routes.js';

import TodoListRoutes from "../Management/TodoListManagement/TodoList/setup/routes.js";
import PersonalNoteRoutes from "../Management/PersonalNoteManagement/PersonalNote/setup/routes.js";

import UserRoleRoutes from "../Management/UserManagement/Role/setup/routes.js";

const routes = {
  path: "",
  component: Layout,
  children: [
    {
      path: "dashboard",
      component: Dashboard,
      name: "adminDashboard",
    },
    //management routes        ConfigurationRoutes,
        DepositRoutes,
        JournalEntryRoutes,
        JournalRoutes,
        AccountRoutes,
        WithdrawalRoutes,
        DueRoutes,
        ShareAdjustmentRoutes,
        ExpenseEntryRoutes,
        IncomeEntryRoutes,
        TransactionLogRoutes,
        ReportRoutes,



    TodoListRoutes,
    PersonalNoteRoutes,

    //user routes
    UserRoutes,
    UserRoleRoutes,
    //settings
    SettingsRoutes,
  ],
};

export default routes;
