<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetPublicSummary
{
    public static function execute()
    {
        try {
            $totalDeposits    = (float) DB::table('deposits')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalWithdrawals = (float) DB::table('withdrawals')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalIncome      = (float) DB::table('income_entries')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalExpense     = (float) DB::table('expense_entries')->whereNull('deleted_at')->where('status', 'active')->sum('amount');
            $totalMembers     = DB::table('users')->where('role_id', 2)->whereNull('deleted_at')->where('status', 'active')->count();
            $netFund          = $totalDeposits - $totalWithdrawals;

            $members = DB::table('users as u')
                ->where('u.role_id', 2)
                ->whereNull('u.deleted_at')
                ->where('u.status', 'active')
                ->leftJoinSub(
                    DB::table('deposits')
                        ->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->select('user_id', DB::raw('SUM(amount) as total_deposit'), DB::raw('COUNT(id) as deposit_count'))
                        ->groupBy('user_id'),
                    'dep', 'dep.user_id', '=', 'u.id'
                )
                ->leftJoinSub(
                    DB::table('deposits')
                        ->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->where('deposit_type', 'share_deposit')
                        ->select('user_id', DB::raw('SUM(amount) as total_share'))
                        ->groupBy('user_id'),
                    'sha', 'sha.user_id', '=', 'u.id'
                )
                ->leftJoinSub(
                    DB::table('deposits')
                        ->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->where('deposit_type', 'extra_savings')
                        ->select('user_id', DB::raw('SUM(amount) as total_savings'))
                        ->groupBy('user_id'),
                    'sav', 'sav.user_id', '=', 'u.id'
                )
                ->leftJoinSub(
                    DB::table('dues')
                        ->whereNull('deleted_at')
                        ->whereIn('payment_status', ['unpaid', 'partial'])
                        ->select('user_id', DB::raw('SUM(remaining_amount) as total_due'))
                        ->groupBy('user_id'),
                    'due', 'due.user_id', '=', 'u.id'
                )
                ->select(
                    'u.id',
                    'u.name',
                    'u.image',
                    DB::raw('COALESCE(dep.total_deposit, 0) as total_deposit'),
                    DB::raw('COALESCE(dep.deposit_count, 0) as deposit_count'),
                    DB::raw('COALESCE(sha.total_share, 0) as total_share'),
                    DB::raw('COALESCE(sav.total_savings, 0) as total_savings'),
                    DB::raw('COALESCE(due.total_due, 0) as total_due')
                )
                ->orderByDesc('total_deposit')
                ->get();

            $data = [
                'org' => [
                    'total_deposits'    => $totalDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                    'total_income'      => $totalIncome,
                    'total_expense'     => $totalExpense,
                    'total_members'     => $totalMembers,
                    'net_fund'          => $netFund,
                ],
                'members' => $members,
            ];

            return entityResponse($data);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
