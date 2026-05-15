<?php

namespace Modules\Management\Deposit\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;
use Modules\Management\Due\Actions\StoreData as DueStoreData;

class StoreData
{
    static $model    = \Modules\Management\Deposit\Database\Models\Model::class;
    static $dueModel = \Modules\Management\Due\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            $requestData['voucher_no']  = TransactionLogService::generateVoucher('DEP');
            $requestData['received_by'] = $requestData['received_by'] ?? auth()->id();

            if (request()->hasFile('image')) {
                $requestData['image'] = uploader(request()->file('image'), 'uploads/deposits');
            }

            DB::beginTransaction();

            $deposit = self::$model::create($requestData);

            // For share deposits: auto-distribute across all unpaid dues (oldest first)
            // This supports lump-sum payments covering multiple months.
            if ($deposit->deposit_type === 'share_deposit') {
                DueStoreData::reconcileMember($deposit->user_id);

                // Link this deposit to the due matching its for_month (for reporting)
                if ($deposit->for_month) {
                    $monthDue = self::$dueModel::where('user_id', $deposit->user_id)
                        ->whereYear('for_month',  date('Y', strtotime($deposit->for_month)))
                        ->whereMonth('for_month', date('m', strtotime($deposit->for_month)))
                        ->whereNull('deleted_at')
                        ->first();
                    if ($monthDue) {
                        $deposit->due_id = $monthDue->id;
                        $deposit->save();
                    }
                }
            }

            $cashAccountId    = TransactionLogService::accountId('1000');
            $capitalAccountId = TransactionLogService::accountId('2000');

            TransactionLogService::record([
                'voucher_no'        => $deposit->voucher_no,
                'transaction_type'  => $deposit->deposit_type,
                'related_type'      => 'Deposit',
                'related_id'        => $deposit->id,
                'user_id'           => $deposit->user_id,
                'amount'            => $deposit->amount,
                'direction'         => 'credit',
                'transaction_date'  => now(),
                'description'       => $deposit->note ?? "Deposit ({$deposit->deposit_type})",
                'debit_account_id'  => $cashAccountId,
                'credit_account_id' => $capitalAccountId,
            ]);

            DB::commit();

            return messageResponse('Deposit recorded successfully', $deposit->load('member', 'due'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
