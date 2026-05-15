<?php

namespace Modules\Management\Due\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreData
{
    static $model = \Modules\Management\Due\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            $due = self::$model::query()->create($requestData);

            // Re-distribute all of this member's share_deposits across all their dues
            self::reconcileMember($due->user_id);

            return messageResponse('Item added successfully', $due, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }

    /**
     * Redistribute the entire pool of a member's share_deposits across their dues
     * in chronological order (oldest due first). Supports lump-sum payments.
     *
     * If the pool still has money after all existing dues are settled, future
     * monthly dues are auto-created so the overpayment is fully credited.
     */
    public static function reconcileMember(int $userId): void
    {
        // Total pool of share_deposits for this user
        $pool = (float) DB::table('deposits')
            ->where('user_id', $userId)
            ->where('deposit_type', 'share_deposit')
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->sum('amount');

        // Subtract any share-decrease adjustments (money refunded out of share pool)
        if (\Illuminate\Support\Facades\Schema::hasTable('share_adjustments')) {
            $refundOut = (float) DB::table('share_adjustments')
                ->where('user_id', $userId)
                ->where('adjustment_type', 'decrease')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->sum('adjustment_amount');
            $pool = max(0, $pool - $refundOut);
        }

        // Apply pool to existing dues (oldest first)
        $dues = self::$model::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderBy('for_month', 'asc')
            ->get();

        foreach ($dues as $due) {
            $dueAmt  = (float) $due->due_amount;
            $applied = min($pool, $dueAmt);

            $due->paid_amount      = $applied;
            $due->remaining_amount = max(0, $dueAmt - $applied);
            $due->payment_status   = match (true) {
                $due->remaining_amount <= 0 => 'paid',
                $applied > 0                => 'partial',
                default                     => 'unpaid',
            };
            $due->save();

            $pool -= $applied;
        }

        // Overpayment? Auto-create future monthly dues until pool runs out.
        if ($pool > 0) {
            self::createFutureDuesFromOverpayment($userId, $pool);
        }
    }

    /**
     * When a member overpays (e.g. ৳10,000 but only one ৳5,000 due exists),
     * auto-create future monthly due records and apply the surplus.
     * Uses member's current `number_of_share` and configured `share_price`.
     */
    protected static function createFutureDuesFromOverpayment(int $userId, float $pool): void
    {
        $user   = DB::table('users')->where('id', $userId)->first();
        $config = DB::table('configurations')
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->first();

        if (!$user || !$config) return;
        if (!$user->number_of_share || $user->number_of_share <= 0) return;
        if (!$config->share_price || $config->share_price <= 0) return;

        $monthlyDue = (float) $user->number_of_share * (float) $config->share_price;
        if ($monthlyDue <= 0) return;

        // Start the month AFTER the latest existing due (or current month if none)
        $lastDue = self::$model::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderBy('for_month', 'desc')
            ->first();

        $cursor = $lastDue
            ? Carbon::parse($lastDue->for_month)->addMonth()->startOfMonth()
            : Carbon::now()->startOfMonth();

        // Safety cap to prevent runaway loops
        $maxMonths = 600;
        $i = 0;

        while ($pool > 0 && $i < $maxMonths) {
            $applied   = min($pool, $monthlyDue);
            $remaining = max(0, $monthlyDue - $applied);
            $status    = $remaining <= 0 ? 'paid' : 'partial';

            self::$model::create([
                'user_id'          => $userId,
                'due_amount'       => $monthlyDue,
                'paid_amount'      => $applied,
                'remaining_amount' => $remaining,
                'for_month'        => $cursor->toDateString(),
                'due_date'         => $cursor->copy()->endOfMonth()->toDateString(),
                'payment_status'   => $status,
            ]);

            $pool -= $applied;
            $cursor->addMonth();
            $i++;
        }
    }

    /**
     * Backward-compat shim: reconcile a single due by re-running full member reconcile.
     */
    public static function reconcile($due): void
    {
        if (!$due || !$due->user_id) return;
        self::reconcileMember($due->user_id);
    }
}
