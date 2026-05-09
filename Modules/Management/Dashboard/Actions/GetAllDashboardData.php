<?php

namespace Modules\Management\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GetAllDashboardData
{
    public static function execute()
    {
        try {
            // ── KPI totals ──────────────────────────────────────────────────
            $totalMembers     = DB::table('users')->where('role_id', 2)->whereNull('deleted_at')->where('status', 'active')->count();
            $totalDeposits    = (float) DB::table('deposits')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalWithdrawals = (float) DB::table('withdrawals')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalIncome      = (float) DB::table('income_entries')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalExpense     = (float) DB::table('expense_entries')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalDueUnpaid   = (float) DB::table('dues')->whereNull('deleted_at')->whereIn('payment_status', ['unpaid', 'partial'])->sum('remaining_amount');
            $netFund          = $totalDeposits - $totalWithdrawals;
            $netProfitLoss    = $totalIncome - $totalExpense;

            // ── Monthly trend — last 6 months ───────────────────────────────
            $labels = $deposits6 = $withdrawals6 = $income6 = $expense6 = [];
            for ($i = 5; $i >= 0; $i--) {
                $m        = Carbon::now()->subMonths($i);
                $ym       = $m->format('Y-m');
                $labels[] = $m->format('M Y');

                $deposits6[]    = (float) DB::table('deposits')       ->whereNull('deleted_at')->where('status', 'active')->whereRaw("DATE_FORMAT(created_at,'%Y-%m')=?", [$ym])->sum('amount');
                $withdrawals6[] = (float) DB::table('withdrawals')    ->whereNull('deleted_at')->where('status', 'active')->whereRaw("DATE_FORMAT(created_at,'%Y-%m')=?", [$ym])->sum('amount');
                $income6[]      = (float) DB::table('income_entries') ->whereNull('deleted_at')->where('status', 'active')->whereRaw("DATE_FORMAT(created_at,'%Y-%m')=?", [$ym])->sum('amount');
                $expense6[]     = (float) DB::table('expense_entries')->whereNull('deleted_at')->where('status', 'active')->whereRaw("DATE_FORMAT(created_at,'%Y-%m')=?", [$ym])->sum('amount');
            }

            // ── Recent 10 transactions ──────────────────────────────────────
            $recentTransactions = DB::table('transaction_logs as tl')
                ->leftJoin('users as u', 'u.id', '=', 'tl.user_id')
                ->whereNull('tl.deleted_at')
                ->select('tl.voucher_no', 'tl.transaction_type', 'tl.amount', 'tl.direction', 'tl.balance_after', 'tl.transaction_date', 'u.name as member_name')
                ->orderBy('tl.id', 'desc')
                ->limit(10)
                ->get();

            // ── Top 5 depositors ────────────────────────────────────────────
            $topDepositors = DB::table('deposits as d')
                ->join('users as u', 'u.id', '=', 'd.user_id')
                ->whereNull('d.deleted_at')
                ->where('d.status', 'active')
                ->groupBy('u.id', 'u.name')
                ->select('u.name', DB::raw('SUM(d.amount) as total_deposit'), DB::raw('COUNT(d.id) as deposit_count'))
                ->orderByDesc('total_deposit')
                ->limit(5)
                ->get();

            $data = [
                'total_members'       => $totalMembers,
                'total_deposits'      => $totalDeposits,
                'total_withdrawals'   => $totalWithdrawals,
                'total_income'        => $totalIncome,
                'total_expense'       => $totalExpense,
                'total_due_unpaid'    => $totalDueUnpaid,
                'net_fund'            => $netFund,
                'net_profit_loss'     => $netProfitLoss,
                'chart_labels'        => $labels,
                'chart_deposits'      => $deposits6,
                'chart_withdrawals'   => $withdrawals6,
                'chart_income'        => $income6,
                'chart_expense'       => $expense6,
                'recent_transactions' => $recentTransactions,
                'top_depositors'      => $topDepositors,
            ];

            return entityResponse($data);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
