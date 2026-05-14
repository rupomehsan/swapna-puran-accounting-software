<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetExpenseEntries
{
    public static function execute()
    {
        try {
            $entries = DB::table('expense_entries')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->orderByDesc('entry_date')
                ->get(['id', 'voucher_no', 'expense_category', 'amount', 'entry_date', 'description']);

            $total = (float) $entries->sum('amount');

            return entityResponse([
                'entries' => $entries,
                'total'   => $total,
                'count'   => $entries->count(),
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
