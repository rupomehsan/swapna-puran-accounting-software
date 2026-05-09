<?php

namespace Modules\Management\Deposit\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

class UpdateData
{
    static $model = \Modules\Management\Deposit\Database\Models\Model::class;

    public static function execute($request, $slug)
    {
        try {
            $deposit = self::$model::where('slug', $slug)->first();
            if (!$deposit) {
                return messageResponse('Deposit not found', [], 404, 'error');
            }

            $requestData = $request->validated();

            if (request()->hasFile('image')) {
                $requestData['image'] = uploader(request()->file('image'), 'uploads/deposits');
            }

            $oldAmount  = (float) $deposit->amount;
            $oldDueId   = $deposit->due_id;
            $newAmount  = isset($requestData['amount']) ? (float) $requestData['amount'] : $oldAmount;
            $newForMonth = $requestData['for_month'] ?? $deposit->for_month;
            $newUserId   = $requestData['user_id']   ?? $deposit->user_id;

            $amountChanged   = $newAmount   !== $oldAmount;
            $monthChanged    = $newForMonth !== $deposit->for_month;
            $memberChanged   = $newUserId   != $deposit->user_id;
            $needsDueRefresh = $amountChanged || $monthChanged || $memberChanged;

            DB::beginTransaction();

            // Reverse old due if anything affecting it changed
            if ($needsDueRefresh && $oldDueId) {
                StoreData::updateDue($oldDueId, $oldAmount, 'reverse');
            }

            // Reversal transaction log entry
            if ($needsDueRefresh) {
                $cashAccountId    = TransactionLogService::accountId('1000');
                $capitalAccountId = TransactionLogService::accountId('2000');

                TransactionLogService::record([
                    'voucher_no'        => $deposit->voucher_no . '-REV',
                    'transaction_type'  => $deposit->deposit_type,
                    'related_type'      => 'Deposit',
                    'related_id'        => $deposit->id,
                    'user_id'           => $deposit->user_id,
                    'amount'            => $oldAmount,
                    'direction'         => 'debit',
                    'transaction_date'  => now(),
                    'description'       => "Reversal: update ({$deposit->voucher_no})",
                    'debit_account_id'  => $capitalAccountId,
                    'credit_account_id' => $cashAccountId,
                ]);
            }

            $deposit->update($requestData);

            // Auto-find new due by member + for_month
            if ($needsDueRefresh) {
                $newDue = StoreData::findDue($deposit->fresh()->user_id, $deposit->fresh()->for_month);
                if ($newDue) {
                    $deposit->due_id = $newDue->id;
                    $deposit->save();
                    StoreData::updateDue($newDue->id, $newAmount, 'add');
                } else {
                    $deposit->due_id = null;
                    $deposit->save();
                }

                $cashAccountId    = TransactionLogService::accountId('1000');
                $capitalAccountId = TransactionLogService::accountId('2000');

                TransactionLogService::record([
                    'voucher_no'        => $deposit->voucher_no . '-UPD',
                    'transaction_type'  => $deposit->fresh()->deposit_type,
                    'related_type'      => 'Deposit',
                    'related_id'        => $deposit->id,
                    'user_id'           => $deposit->fresh()->user_id,
                    'amount'            => $newAmount,
                    'direction'         => 'credit',
                    'transaction_date'  => now(),
                    'description'       => "Updated deposit ({$deposit->voucher_no})",
                    'debit_account_id'  => $cashAccountId,
                    'credit_account_id' => $capitalAccountId,
                ]);
            }

            DB::commit();

            return messageResponse('Deposit updated successfully', $deposit->fresh()->load('member', 'due'), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
