<?php

namespace Modules\Management\Deposit\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Helpers\Services\TransactionLogService;

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

            // Auto-find the due for this member + month and mark it paid
            $due = self::findDue($deposit->user_id, $deposit->for_month);
            if ($due) {
                $deposit->due_id = $due->id;
                $deposit->save();
                self::updateDue($due->id, $deposit->amount, 'add');
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

    /**
     * Find unpaid/partial due for this member in the same year-month as for_month.
     */
    public static function findDue(int $userId, $forMonth): ?object
    {
        if (!$forMonth) return null;

        return self::$dueModel::where('user_id', $userId)
            ->whereYear('for_month', date('Y', strtotime($forMonth)))
            ->whereMonth('for_month', date('m', strtotime($forMonth)))
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->first();
    }

    /**
     * Adjust due paid_amount and payment_status.
     * $mode = 'add' (new payment) | 'reverse' (undo old amount)
     */
    public static function updateDue(int $dueId, float $amount, string $mode): void
    {
        $due = self::$dueModel::find($dueId);
        if (!$due) return;

        if ($mode === 'add') {
            $due->paid_amount = $due->paid_amount + $amount;
        } else {
            $due->paid_amount = max(0, $due->paid_amount - $amount);
        }

        $due->remaining_amount = max(0, $due->due_amount - $due->paid_amount);
        $due->payment_status   = match (true) {
            $due->remaining_amount <= 0 => 'paid',
            $due->paid_amount > 0       => 'partial',
            default                     => 'unpaid',
        };
        $due->save();
    }
}
