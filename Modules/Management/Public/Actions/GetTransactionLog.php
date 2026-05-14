<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetTransactionLog
{
    public static function execute()
    {
        try {
            $deposits = DB::table('deposits as d')
                ->leftJoin('users as u', 'u.id', '=', 'd.user_id')
                ->whereNull('d.deleted_at')
                ->where('d.status', 'active')
                ->select(
                    'd.voucher_no',
                    'u.name as member_name',
                    DB::raw("'deposit' as type"),
                    'd.deposit_type',
                    DB::raw('CAST(d.amount AS DECIMAL(10,2)) as amount'),
                    'd.for_month',
                    'd.payment_date as date',
                    'd.payment_method',
                    'd.note'
                )
                ->get();

            $withdrawals = DB::table('withdrawals as w')
                ->leftJoin('users as u', 'u.id', '=', 'w.user_id')
                ->whereNull('w.deleted_at')
                ->where('w.status', 'active')
                ->select(
                    'w.voucher_no',
                    'u.name as member_name',
                    DB::raw("'withdrawal' as type"),
                    DB::raw("NULL as deposit_type"),
                    'w.amount',
                    DB::raw("NULL as for_month"),
                    'w.withdrawal_date as date',
                    'w.payment_method',
                    'w.note'
                )
                ->get();

            $transactions = $deposits->concat($withdrawals)
                ->sortByDesc('date')
                ->values();

            $summary = [
                'total_deposits'    => (float) $deposits->sum('amount'),
                'total_withdrawals' => (float) $withdrawals->sum('amount'),
                'total_count'       => $transactions->count(),
            ];

            return entityResponse([
                'transactions' => $transactions,
                'summary'      => $summary,
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
