<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetTransactionLog
{
    public static function execute()
    {
        try {
            // Fetch oldest-first so we can compute a correct global running balance
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
                    't.transaction_date',
                    't.description',
                    'u.name as member_name'
                )
                ->orderBy('t.transaction_date')
                ->orderBy('t.id')
                ->get();

            // Compute a single global running balance across all transaction types
            $runningBalance = 0;
            $transactions = $transactions->map(function ($row) use (&$runningBalance) {
                if ($row->direction === 'credit') {
                    $runningBalance += (float) $row->amount;
                } else {
                    $runningBalance -= (float) $row->amount;
                }
                $row->balance_after = $runningBalance;
                return $row;
            });

            // Reverse to newest-first for display
            $transactions = $transactions->reverse()->values();

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
