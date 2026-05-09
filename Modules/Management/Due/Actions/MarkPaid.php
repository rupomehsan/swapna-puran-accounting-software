<?php

namespace Modules\Management\Due\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

class MarkPaid
{
    static $model = \Modules\Management\Due\Database\Models\Model::class;

    public static function execute($slug)
    {
        try {
            $due = self::$model::where('slug', $slug)->first();
            if (!$due) {
                return messageResponse('Due not found.', [], 404, 'error');
            }
            if ($due->payment_status === 'paid') {
                return messageResponse('This due is already marked as paid.', $due, 200);
            }

            $paidNow = (float) request()->input('paid_amount', $due->remaining_amount);

            DB::beginTransaction();

            $due->paid_amount      = $due->paid_amount + $paidNow;
            $due->remaining_amount = max(0, $due->due_amount - $due->paid_amount);
            $due->payment_status   = $due->remaining_amount <= 0 ? 'paid' : 'partial';
            $due->save();

            TransactionLogService::record([
                'voucher_no'       => TransactionLogService::generateVoucher('DC'),
                'transaction_type' => 'due_collection',
                'related_type'     => 'Due',
                'related_id'       => $due->id,
                'user_id'          => $due->user_id,
                'amount'           => $paidNow,
                'direction'        => 'credit',
                'transaction_date' => now(),
                'description'      => "Due collection for month " . $due->for_month,
                'debit_account_id' => TransactionLogService::accountId('1000'),
                'credit_account_id'=> TransactionLogService::accountId('2000'),
            ]);

            DB::commit();
            return messageResponse('Due marked as ' . $due->payment_status, $due, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
