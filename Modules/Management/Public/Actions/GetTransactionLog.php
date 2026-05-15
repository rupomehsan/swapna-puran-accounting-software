<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetTransactionLog
{
    public static function execute()
    {
        try {
            $transactions = DB::table('transaction_logs as t')
                ->leftJoin('users as u', 'u.id', '=', 't.user_id')
                ->whereNull('t.deleted_at')
                ->where('t.status', 'active')
                ->select(
                    't.id',
                    't.voucher_no',
                    't.transaction_type',
                    't.amount',
                    't.direction',
                    't.balance_after',
                    't.transaction_date',
                    't.description',
                    'u.name as member_name'
                )
                ->orderByDesc('t.transaction_date')
                ->get();

            $totalCredit = (float) $transactions->where('direction', 'credit')->sum('amount');
            $totalDebit  = (float) $transactions->where('direction', 'debit')->sum('amount');

            return entityResponse([
                'transactions' => $transactions,
                'summary'      => [
                    'total_credit' => $totalCredit,
                    'total_debit'  => $totalDebit,
                    'total_count'  => $transactions->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
