<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetMemberDeposits
{
    public static function execute($userId)
    {
        try {
            $member = DB::table('users')
                ->where('id', $userId)
                ->where('role_id', 2)
                ->whereNull('deleted_at')
                ->select('id', 'name', 'image')
                ->first();

            if (!$member) {
                return messageResponse('Member not found', [], 404, 'error');
            }

            $deposits = DB::table('deposits')
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->orderByDesc('payment_date')
                ->get(['voucher_no', 'deposit_type', 'amount', 'for_month', 'payment_date', 'payment_method', 'note']);

            return entityResponse([
                'member'   => $member,
                'deposits' => $deposits,
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
