<?php

namespace Modules\Management\ShareAdjustment\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;
use Modules\Management\Due\Actions\StoreData as DueStoreData;

class StoreAdjustment
{
    static $model           = \Modules\Management\ShareAdjustment\Database\Models\Model::class;
    static $userModel       = \Modules\Management\UserManagement\User\Database\Models\Model::class;
    static $dueModel        = \Modules\Management\Due\Database\Models\Model::class;
    static $depositModel    = \Modules\Management\Deposit\Database\Models\Model::class;
    static $withdrawalModel = \Modules\Management\Withdrawal\Database\Models\Model::class;

    public static function execute()
    {
        try {
            $userId             = (int) request()->input('user_id');
            $type               = request()->input('adjustment_type');
            $count              = (int) request()->input('count');
            $refundDestination  = request()->input('refund_destination'); // for decrease
            $paymentMethod      = request()->input('payment_method', 'cash');
            $effectiveDate      = request()->input('effective_date') ?? now()->toDateString();
            $note               = request()->input('note');

            if (!in_array($type, ['increase', 'decrease'])) {
                return messageResponse('Invalid adjustment_type.', [], 422, 'error');
            }
            if ($count < 1) {
                return messageResponse('Count must be at least 1.', [], 422, 'error');
            }

            $user = self::$userModel::find($userId);
            if (!$user) {
                return messageResponse('Member not found.', [], 404, 'error');
            }

            $config = DB::table('configurations')
                ->whereNull('deleted_at')->where('status','active')
                ->orderByDesc('updated_at')->first();
            if (!$config || !$config->start_date || !$config->share_price) {
                return messageResponse('System configuration not set.', [], 422, 'error');
            }

            $sharePrice    = (float) $config->share_price;
            $currentShares = (int) $user->number_of_share;
            $newShares = $type === 'increase'
                ? $currentShares + $count
                : $currentShares - $count;
            if ($newShares < 1) {
                return messageResponse('Resulting share count must be at least 1.', [], 422, 'error');
            }

            $startMonth   = Carbon::parse($config->start_date)->startOfMonth();
            $currentMonth = Carbon::now()->startOfMonth();
            $monthsElapsed = $currentMonth->gte($startMonth)
                ? $startMonth->diffInMonths($currentMonth) + 1
                : 0;

            $expectedOld = $currentShares * $sharePrice * $monthsElapsed;
            $expectedNew = $newShares      * $sharePrice * $monthsElapsed;
            $adjustmentAmount = abs($expectedNew - $expectedOld);

            $paidSoFar = (float) DB::table('deposits')
                ->where('user_id', $userId)
                ->where('deposit_type', 'share_deposit')
                ->whereNull('deleted_at')->where('status','active')
                ->sum('amount');

            DB::beginTransaction();

            // Update every existing due row for this user to the new monthly amount
            $newMonthlyDue = $newShares * $sharePrice;
            self::$dueModel::where('user_id', $userId)
                ->whereNull('deleted_at')
                ->update(['due_amount' => $newMonthlyDue]);

            $linkedDepositId    = null;
            $linkedWithdrawalId = null;

            if ($type === 'increase' && $adjustmentAmount > 0) {
                // Member owes more — create a back-payment deposit
                $voucher = TransactionLogService::generateVoucher('DEP');
                $deposit = self::$depositModel::create([
                    'user_id'        => $userId,
                    'voucher_no'     => $voucher,
                    'deposit_type'   => 'share_deposit',
                    'amount'         => $adjustmentAmount,
                    'for_month'      => $effectiveDate,
                    'payment_date'   => $effectiveDate,
                    'payment_method' => $paymentMethod,
                    'note'           => 'Share upgrade '.$currentShares.' → '.$newShares.' (back-payment, '.$monthsElapsed.' months)',
                    'received_by'    => auth()->id(),
                ]);
                $linkedDepositId = $deposit->id;

                TransactionLogService::record([
                    'voucher_no'        => $voucher,
                    'transaction_type'  => 'share_deposit',
                    'related_type'      => 'Deposit',
                    'related_id'        => $deposit->id,
                    'user_id'           => $userId,
                    'amount'            => $adjustmentAmount,
                    'direction'         => 'credit',
                    'transaction_date'  => now(),
                    'description'       => 'Share adjustment (increase) back-payment',
                    'debit_account_id'  => TransactionLogService::accountId('1000'),
                    'credit_account_id' => TransactionLogService::accountId('2000'),
                ]);
            }

            if ($type === 'decrease' && $adjustmentAmount > 0) {
                $destination = in_array($refundDestination, ['withdrawal','extra_savings'])
                    ? $refundDestination
                    : 'withdrawal';

                if ($destination === 'withdrawal') {
                    $voucher = TransactionLogService::generateVoucher('WTH');
                    $w = self::$withdrawalModel::create([
                        'user_id'         => $userId,
                        'voucher_no'      => $voucher,
                        'amount'          => $adjustmentAmount,
                        'withdrawal_date' => $effectiveDate,
                        'payment_method'  => $paymentMethod,
                        'note'            => 'Share downgrade '.$currentShares.' → '.$newShares.' (refund, '.$monthsElapsed.' months)',
                    ]);
                    $linkedWithdrawalId = $w->id;

                    TransactionLogService::record([
                        'voucher_no'        => $voucher,
                        'transaction_type'  => 'withdrawal',
                        'related_type'      => 'Withdrawal',
                        'related_id'        => $w->id,
                        'user_id'           => $userId,
                        'amount'            => $adjustmentAmount,
                        'direction'         => 'debit',
                        'transaction_date'  => now(),
                        'description'       => 'Share adjustment (decrease) refund as withdrawal',
                        'debit_account_id'  => TransactionLogService::accountId('2000'),
                        'credit_account_id' => TransactionLogService::accountId('1000'),
                    ]);
                } else {
                    // Transfer to extra_savings: add a negative share_deposit (reduces pool)
                    // AND a positive extra_savings deposit of the same amount.
                    // Simpler: insert one extra_savings deposit; reduce the share pool by
                    // creating a "share-refund" share_deposit with negative amount? No,
                    // amount column is unsigned. Instead, we directly soft-decrement past
                    // share_deposit pool by inserting an inverse marker as extra_savings,
                    // and rely on reconcile to redistribute the remaining share pool.
                    // To keep auditability we record this as: a new extra_savings deposit
                    // of $adjustmentAmount.
                    $voucher = TransactionLogService::generateVoucher('DEP');
                    $deposit = self::$depositModel::create([
                        'user_id'        => $userId,
                        'voucher_no'     => $voucher,
                        'deposit_type'   => 'extra_savings',
                        'amount'         => $adjustmentAmount,
                        'for_month'      => $effectiveDate,
                        'payment_date'   => $effectiveDate,
                        'payment_method' => $paymentMethod,
                        'note'           => 'Share downgrade '.$currentShares.' → '.$newShares.' (refund moved to extra savings)',
                        'received_by'    => auth()->id(),
                    ]);
                    $linkedDepositId = $deposit->id;

                    TransactionLogService::record([
                        'voucher_no'        => $voucher,
                        'transaction_type'  => 'extra_savings',
                        'related_type'      => 'Deposit',
                        'related_id'        => $deposit->id,
                        'user_id'           => $userId,
                        'amount'            => $adjustmentAmount,
                        'direction'         => 'credit',
                        'transaction_date'  => now(),
                        'description'       => 'Share adjustment (decrease) refund moved to extra savings',
                        'debit_account_id'  => TransactionLogService::accountId('1000'),
                        'credit_account_id' => TransactionLogService::accountId('2000'),
                    ]);
                }
            }

            // Update the member's share count
            $user->number_of_share = $newShares;
            $user->save();

            // Audit trail entry FIRST — so the reconciler can see decrease refunds
            $audit = self::$model::create([
                'user_id'             => $userId,
                'adjustment_type'     => $type,
                'from_shares'         => $currentShares,
                'to_shares'           => $newShares,
                'shares_delta'        => $newShares - $currentShares,
                'months_elapsed'      => $monthsElapsed,
                'share_price'         => $sharePrice,
                'expected_old'        => $expectedOld,
                'expected_new'        => $expectedNew,
                'paid_so_far'         => $paidSoFar,
                'adjustment_amount'   => $adjustmentAmount,
                'refund_destination'  => $type === 'decrease' ? ($refundDestination ?? 'withdrawal') : null,
                'linked_deposit_id'   => $linkedDepositId,
                'linked_withdrawal_id'=> $linkedWithdrawalId,
                'note'                => $note,
                'effective_date'      => $effectiveDate,
            ]);

            // Re-reconcile dues using the new amounts and updated pool
            DueStoreData::reconcileMember($userId);

            DB::commit();

            return messageResponse('Share adjustment applied successfully.', [
                'audit_id'          => $audit->id,
                'from_shares'       => $currentShares,
                'to_shares'         => $newShares,
                'adjustment_amount' => $adjustmentAmount,
                'direction'         => $type,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
