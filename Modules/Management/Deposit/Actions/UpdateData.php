<?php

namespace Modules\Management\Deposit\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;
use Modules\Management\Due\Actions\StoreData as DueStoreData;

class UpdateData
{
    static $model    = \Modules\Management\Deposit\Database\Models\Model::class;
    static $dueModel = \Modules\Management\Due\Database\Models\Model::class;

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

            $oldUserId = $deposit->user_id;
            $oldAmount = (float) $deposit->amount;

            DB::beginTransaction();

            $deposit->update($requestData);
            $deposit->refresh();

            // Re-reconcile both old and new user if member changed
            if ($deposit->deposit_type === 'share_deposit' || $oldUserId !== $deposit->user_id) {
                DueStoreData::reconcileMember($oldUserId);
                if ($oldUserId !== $deposit->user_id) {
                    DueStoreData::reconcileMember($deposit->user_id);
                }

                // Refresh due_id link
                if ($deposit->for_month) {
                    $monthDue = self::$dueModel::where('user_id', $deposit->user_id)
                        ->whereYear('for_month',  date('Y', strtotime($deposit->for_month)))
                        ->whereMonth('for_month', date('m', strtotime($deposit->for_month)))
                        ->whereNull('deleted_at')
                        ->first();
                    $deposit->due_id = $monthDue?->id;
                    $deposit->save();
                }
            }

            // Transaction log: reversal + new entry (only if amount/member changed)
            $newAmount = (float) $deposit->amount;
            if ($oldAmount != $newAmount || $oldUserId != $deposit->user_id) {
                $cashAccountId    = TransactionLogService::accountId('1000');
                $capitalAccountId = TransactionLogService::accountId('2000');

                TransactionLogService::record([
                    'voucher_no'        => $deposit->voucher_no . '-REV',
                    'transaction_type'  => $deposit->deposit_type,
                    'related_type'      => 'Deposit',
                    'related_id'        => $deposit->id,
                    'user_id'           => $oldUserId,
                    'amount'            => $oldAmount,
                    'direction'         => 'debit',
                    'transaction_date'  => now(),
                    'description'       => "Reversal: update ({$deposit->voucher_no})",
                    'debit_account_id'  => $capitalAccountId,
                    'credit_account_id' => $cashAccountId,
                ]);

                TransactionLogService::record([
                    'voucher_no'        => $deposit->voucher_no . '-UPD',
                    'transaction_type'  => $deposit->deposit_type,
                    'related_type'      => 'Deposit',
                    'related_id'        => $deposit->id,
                    'user_id'           => $deposit->user_id,
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
