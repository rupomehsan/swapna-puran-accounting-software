<?php

namespace Modules\Management\ShareAdjustment\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PreviewAdjustment
{
    public static function execute()
    {
        try {
            $userId    = (int) request()->input('user_id');
            $type      = request()->input('adjustment_type'); // increase | decrease
            $count     = (int) request()->input('count');

            $user = DB::table('users')->where('id', $userId)->whereNull('deleted_at')->first();
            if (!$user) {
                return messageResponse('Member not found.', [], 404, 'error');
            }
            if (!in_array($type, ['increase', 'decrease'])) {
                return messageResponse('Invalid adjustment_type.', [], 422, 'error');
            }
            if ($count < 1) {
                return messageResponse('Count must be at least 1.', [], 422, 'error');
            }

            $config = DB::table('configurations')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->orderByDesc('updated_at')
                ->first();
            if (!$config || !$config->start_date || !$config->share_price) {
                return messageResponse('System configuration not set.', [], 422, 'error');
            }

            $sharePrice  = (float) $config->share_price;
            $currentShares = (int) $user->number_of_share;

            $newShares = $type === 'increase'
                ? $currentShares + $count
                : $currentShares - $count;

            if ($newShares < 1) {
                return messageResponse('Resulting share count must be at least 1.', [], 422, 'error');
            }

            // Months elapsed (inclusive of system start month)
            $startMonth   = Carbon::parse($config->start_date)->startOfMonth();
            $currentMonth = Carbon::now()->startOfMonth();
            $monthsElapsed = $currentMonth->gte($startMonth)
                ? $startMonth->diffInMonths($currentMonth) + 1
                : 0;

            $expectedOld = $currentShares * $sharePrice * $monthsElapsed;
            $expectedNew = $newShares      * $sharePrice * $monthsElapsed;

            $paidSoFar = (float) DB::table('deposits')
                ->where('user_id', $userId)
                ->where('deposit_type', 'share_deposit')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->sum('amount');

            $adjustmentAmount = abs($expectedNew - $expectedOld);

            return entityResponse([
                'user_id'           => $userId,
                'user_name'         => $user->name,
                'current_shares'    => $currentShares,
                'new_shares'        => $newShares,
                'adjustment_type'   => $type,
                'count'             => $count,
                'months_elapsed'    => $monthsElapsed,
                'share_price'       => $sharePrice,
                'expected_old'      => round($expectedOld, 2),
                'expected_new'      => round($expectedNew, 2),
                'paid_so_far'       => round($paidSoFar, 2),
                'adjustment_amount' => round($adjustmentAmount, 2),
                'direction_label'   => $type === 'increase' ? 'Member Owes' : 'Refund to Member',
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
