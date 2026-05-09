<?php

namespace Modules\Management\Withdrawal\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

class StoreData
{
    static $model = \Modules\Management\Withdrawal\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            $amount      = (float) $requestData['amount'];
            $requestData['voucher_no'] = TransactionLogService::generateVoucher('WTH');

            DB::beginTransaction();

            $withdrawal = self::$model::create($requestData);

            $cashAccountId    = TransactionLogService::accountId('1000');
            $capitalAccountId = TransactionLogService::accountId('2000');

            TransactionLogService::record([
                'voucher_no'       => $withdrawal->voucher_no,
                'transaction_type' => 'withdrawal',
                'related_type'     => 'Withdrawal',
                'related_id'       => $withdrawal->id,
                'user_id'          => $withdrawal->user_id ?? null,
                'amount'           => $amount,
                'direction'        => 'debit',
                'transaction_date' => now(),
                'description'      => $withdrawal->reason ?? 'Member withdrawal',
                'debit_account_id' => $capitalAccountId,
                'credit_account_id'=> $cashAccountId,
            ]);

            DB::commit();
            return messageResponse('Withdrawal recorded successfully', $withdrawal, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}