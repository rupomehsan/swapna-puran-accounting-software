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
                ->select('id', 'name', 'image', 'phone', 'email', 'number_of_share', 'join_date', 'created_at', 'nominee_name', 'nominee_relation', 'nominee_nid', 'nominee_image')
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

            // Due from dues table (snapshots historical share count per month)
            $totalDue = (float) DB::table('dues')
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->sum('remaining_amount');

            // Latest contiguous paid-through month
            $paidTill = null;
            $userDues = DB::table('dues')
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->orderBy('for_month', 'asc')
                ->get(['for_month', 'payment_status']);
            foreach ($userDues as $d) {
                if ($d->payment_status === 'paid') {
                    $paidTill = $d->for_month;
                } else {
                    break;
                }
            }

            // Latest share adjustment (for direction indicator)
            $lastAdjustment = null;
            if (\Illuminate\Support\Facades\Schema::hasTable('share_adjustments')) {
                $lastAdjustment = DB::table('share_adjustments')
                    ->where('user_id', $userId)
                    ->whereNull('deleted_at')
                    ->where('status', 'active')
                    ->orderByDesc('id')
                    ->first(['adjustment_type', 'from_shares', 'to_shares', 'shares_delta', 'created_at']);
            }

            // Org settings for invoice generation
            $orgName = DB::table('con_setting_title as t')
                ->join('con_setting_title_values as v', 'v.setting_title_id', '=', 't.id')
                ->where('t.title', 'site_name')->whereNull('t.deleted_at')
                ->value('v.value') ?? 'Organization';
            $orgLogo = DB::table('con_setting_title as t')
                ->join('con_setting_title_values as v', 'v.setting_title_id', '=', 't.id')
                ->where('t.title', 'image')->whereNull('t.deleted_at')
                ->value('v.value');

            return entityResponse([
                'member'  => $member,
                'org'     => [
                    'name' => $orgName,
                    'logo' => $orgLogo ? (str_starts_with($orgLogo, 'http') || str_starts_with($orgLogo, '/') ? $orgLogo : '/' . $orgLogo) : null,
                ],
                'stats'   => [
                    'total_deposit'   => $totalDeposit,
                    'total_share'     => $totalShare,
                    'total_savings'   => $totalSavings,
                    'total_due'       => $totalDue,
                    'deposit_count'   => $deposits->count(),
                    'paid_till'       => $paidTill,
                    'last_adjustment' => $lastAdjustment,
                ],
                'deposits' => $deposits,
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
