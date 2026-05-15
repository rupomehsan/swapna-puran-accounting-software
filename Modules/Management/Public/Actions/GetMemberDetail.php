<?php

namespace Modules\Management\Public\Actions;

use Illuminate\Support\Facades\DB;

class GetMemberDetail
{
    public static function execute($userId)
    {
        try {
            $member = DB::table('users')
                ->where('id', $userId)
                ->where('role_id', 2)
                ->whereNull('deleted_at')
                ->select('id', 'name', 'image', 'phone', 'email', 'number_of_share', 'join_date', 'created_at')
                ->first();

            if (!$member) {
                return messageResponse('Member not found', [], 404, 'error');
            }

            $deposits = DB::table('deposits')
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->orderByDesc('payment_date')
                ->get(['id', 'voucher_no', 'deposit_type', 'amount', 'for_month', 'payment_date', 'payment_method', 'note', 'image']);

            $totalDeposit = $deposits->sum(fn($d) => (float) $d->amount);
            $totalShare   = $deposits->filter(fn($d) => $d->deposit_type === 'share_deposit')->sum(fn($d) => (float) $d->amount);
            $totalSavings = $deposits->filter(fn($d) => $d->deposit_type === 'extra_savings')->sum(fn($d) => (float) $d->amount);

            // Config-based due calculation
            $config     = DB::table('configurations')->whereNull('deleted_at')->where('status', 'active')->orderByDesc('updated_at')->first();
            $sharePrice = $config ? (float) $config->share_price : 0;
            $startDate  = $config ? new \DateTime($config->start_date) : null;

            $monthsPassed = 0;
            if ($startDate) {
                $startMonth   = \DateTime::createFromFormat('Y-m-d', $startDate->format('Y-m-01'));
                $currentMonth = new \DateTime('first day of this month');
                if ($currentMonth >= $startMonth) {
                    $diff         = $startMonth->diff($currentMonth);
                    $monthsPassed = $diff->y * 12 + $diff->m + 1; // inclusive
                }
            }

            $expected = $monthsPassed * $member->number_of_share * $sharePrice;
            $totalDue = max(0, $expected - $totalShare);

            return entityResponse([
                'member'  => $member,
                'stats'   => [
                    'total_deposit' => $totalDeposit,
                    'total_share'   => $totalShare,
                    'total_savings' => $totalSavings,
                    'total_due'     => $totalDue,
                    'deposit_count' => $deposits->count(),
                ],
                'deposits' => $deposits,
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
