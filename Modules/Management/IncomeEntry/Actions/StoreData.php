<?php

namespace Modules\Management\IncomeEntry\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

class StoreData
{
    static $model = \Modules\Management\IncomeEntry\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            $requestData['voucher_no'] = TransactionLogService::generateVoucher('INC');

            DB::beginTransaction();

            $income = self::$model::create($requestData);

            $cashAccountId   = TransactionLogService::accountId('1000');
            $incomeAccountId = $income->account_id ?? TransactionLogService::accountId('3000');

            TransactionLogService::record([
                'voucher_no'       => $income->voucher_no,
                'transaction_type' => 'income',
                'related_type'     => 'IncomeEntry',
                'related_id'       => $income->id,
                'user_id'          => null,
                'amount'           => $income->amount,
                'direction'        => 'credit',
                'transaction_date' => $income->entry_date ?? now(),
                'description'      => $income->description ?? $income->income_source,
                'debit_account_id' => $cashAccountId,
                'credit_account_id'=> $incomeAccountId,
            ]);

            DB::commit();
            return messageResponse('Income entry recorded successfully', $income, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}