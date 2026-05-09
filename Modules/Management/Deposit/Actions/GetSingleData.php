<?php

namespace Modules\Management\Deposit\Actions;

class GetSingleData
{
    static $model = \Modules\Management\Deposit\Database\Models\Model::class;

    public static function execute($slug)
    {
        try {
            $deposit = self::$model::query()
                ->with(['member:id,name,email', 'due:id,due_amount,paid_amount,remaining_amount,payment_status,for_month'])
                ->where('slug', $slug)
                ->first();

            if (!$deposit) {
                return messageResponse('Deposit not found', [], 404, 'error');
            }

            // Members can only view their own deposits
            if (auth()->user()?->role_id === 2 && $deposit->user_id !== auth()->id()) {
                return messageResponse('Unauthorized', [], 403, 'error');
            }

            return entityResponse($deposit);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
