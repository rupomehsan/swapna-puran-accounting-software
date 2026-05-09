<?php

namespace Modules\Management\Due\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

class GenerateDue
{
    static $model       = \Modules\Management\Due\Database\Models\Model::class;
    static $userModel   = \Modules\Management\UserManagement\User\Database\Models\Model::class;
    static $configModel = \Modules\Management\Configuration\Database\Models\Model::class;

    public static function execute()
    {
        try {
            $config = self::$configModel::first();
            if (!$config || !$config->start_date) {
                return messageResponse(
                    'System configuration not set. Please set share price and start date first.',
                    [], 422, 'error'
                );
            }

            $members = self::$userModel::where('role_id', 2)
                ->where('status', 'active')
                ->where('number_of_share', '>', 0)
                ->get();

            if ($members->isEmpty()) {
                return messageResponse('No active members with shares found.', [], 200);
            }

            $systemStart  = Carbon::parse($config->start_date)->startOfMonth();
            $currentMonth = Carbon::now()->startOfMonth();

            // Admin can pass a custom from_date (override)
            $fromDateInput = request()->input('from_date');
            $baseStart = $fromDateInput
                ? Carbon::parse($fromDateInput)->startOfMonth()
                : $systemStart;

            // Build all month slots from base start → now
            $allMonths = [];
            $cursor = $baseStart->copy();
            while ($cursor->lte($currentMonth)) {
                $allMonths[] = $cursor->copy();
                $cursor->addMonth();
            }

            $created = 0;
            $skipped = 0;

            DB::beginTransaction();

            foreach ($members as $member) {
                $dueAmount = $member->number_of_share * $config->share_price;

                // Member's effective start: whichever is EARLIER — system start or join_date
                // This means founding members (join_date > system_start) still get dues
                // from system start. New future members get dues from their join_date.
                $memberStart = $member->join_date
                    ? Carbon::parse($member->join_date)->startOfMonth()
                    : $baseStart;

                // Use the earlier of the two so founding members get back-dues
                $effectiveStart = $memberStart->lte($baseStart) ? $memberStart : $baseStart;

                foreach ($allMonths as $month) {
                    // Skip months before this member's effective start
                    if ($month->lt($effectiveStart)) {
                        continue;
                    }

                    // Skip if due already exists
                    $exists = self::$model::where('user_id', $member->id)
                        ->whereYear('for_month',  $month->year)
                        ->whereMonth('for_month', $month->month)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    $due = self::$model::create([
                        'user_id'          => $member->id,
                        'due_amount'       => $dueAmount,
                        'paid_amount'      => 0,
                        'remaining_amount' => $dueAmount,
                        'for_month'        => $month->toDateString(),
                        'due_date'         => $month->copy()->endOfMonth()->toDateString(),
                        'payment_status'   => 'unpaid',
                    ]);

                    TransactionLogService::record([
                        'voucher_no'        => TransactionLogService::generateVoucher('DUE'),
                        'transaction_type'  => 'due_created',
                        'related_type'      => 'Due',
                        'related_id'        => $due->id,
                        'user_id'           => $member->id,
                        'amount'            => $dueAmount,
                        'direction'         => 'debit',
                        'transaction_date'  => now(),
                        'description'       => "Monthly due — " . $month->format('F Y'),
                        'debit_account_id'  => null,
                        'credit_account_id' => null,
                    ]);

                    $created++;
                }
            }

            DB::commit();

            $msg = "Due generation complete. Created: {$created}, Skipped (already existed): {$skipped}.";
            return messageResponse($msg, [
                'created' => $created,
                'skipped' => $skipped,
                'months'  => count($allMonths),
                'members' => $members->count(),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
