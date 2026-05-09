<?php

namespace Modules\Management\ExpenseEntry\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

class StoreData
{
    static $model = \Modules\Management\ExpenseEntry\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            $requestData['voucher_no'] = TransactionLogService::generateVoucher('EXP');

            DB::beginTransaction();

            $expense = self::$model::create($requestData);

            $cashAccountId    = TransactionLogService::accountId('1000');
            $expenseAccountId = $expense->account_id ?? TransactionLogService::accountId('4000');

            TransactionLogService::record([
                'voucher_no'       => $expense->voucher_no,
                'transaction_type' => 'expense',
                'related_type'     => 'ExpenseEntry',
                'related_id'       => $expense->id,
                'user_id'          => null,
                'amount'           => $expense->amount,
                'direction'        => 'debit',
                'transaction_date' => $expense->entry_date ?? now(),
                'description'      => $expense->description ?? $expense->expense_category,
                'debit_account_id' => $expenseAccountId,
                'credit_account_id'=> $cashAccountId,
            ]);

            DB::commit();
            return messageResponse('Expense entry recorded successfully', $expense, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}