<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetBalanceSheet
{
    public static function execute()
    {
        try {
            $totalDeposits    = (float) DB::table('deposits')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalWithdrawals = (float) DB::table('withdrawals')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalIncome      = (float) DB::table('income_entries')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalExpense     = (float) DB::table('expense_entries')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalMembers     = DB::table('users')->where('role_id', 2)->whereNull('deleted_at')->where('status', 'active')->count();

            $netSavings  = $totalDeposits - $totalWithdrawals;
            $netBalance  = $netSavings + $totalIncome - $totalExpense;

            $monthlyDeposits = DB::table('deposits')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->whereNotNull('payment_date')
                ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
                ->select(
                    DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $shareDeposits   = (float) DB::table('deposits')->whereNull('deleted_at')->where('status', 'active')->where('deposit_type', 'share_deposit')->sum('amount');
            $savingsDeposits = (float) DB::table('deposits')->whereNull('deleted_at')->where('status', 'active')->where('deposit_type', 'extra_savings')->sum('amount');

            return entityResponse([
                'summary' => [
                    'total_deposits'    => $totalDeposits,
                    'share_deposits'    => $shareDeposits,
                    'savings_deposits'  => $savingsDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                    'net_savings'       => $netSavings,
                    'total_income'      => $totalIncome,
                    'total_expense'     => $totalExpense,
                    'net_balance'       => $netBalance,
                    'total_members'     => $totalMembers,
                ],
                'monthly_deposits' => $monthlyDeposits,
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
