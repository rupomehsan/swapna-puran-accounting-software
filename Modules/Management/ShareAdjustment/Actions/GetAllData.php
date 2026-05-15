<?php

namespace Modules\Management\ShareAdjustment\Actions;

use Illuminate\Support\Facades\DB;

class GetAllData
{
    public static function execute()
    {
        try {
            $perPage = (int) (request()->input('limit') ?? 20);
            $page    = (int) (request()->input('page')  ?? 1);
            $search  = request()->input('search_key');

            $q = DB::table('share_adjustments as a')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->whereNull('a.deleted_at')
                ->where('a.status', 'active')
                ->select(
                    'a.id',
                    'a.user_id',
                    'a.adjustment_type',
                    'a.from_shares',
                    'a.to_shares',
                    'a.shares_delta',
                    'a.months_elapsed',
                    'a.share_price',
                    'a.adjustment_amount',
                    'a.refund_destination',
                    'a.effective_date',
                    'a.note',
                    'a.slug',
                    'a.created_at',
                    'u.name as member_name'
                )
                ->orderByDesc('a.created_at');

            if ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('u.name', 'like', "%$search%")
                      ->orWhere('a.note', 'like', "%$search%");
                });
            }

            $total = (clone $q)->count();
            $rows  = $q->forPage($page, $perPage)->get();

            return entityResponse([
                'data'              => $rows,
                'active_data_count' => $total,
                'per_page'          => $perPage,
                'current_page'      => $page,
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
