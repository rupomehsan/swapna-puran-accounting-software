<?php

namespace Modules\Management\Deposit\Actions;

use Modules\Management\Due\Actions\StoreData as DueStoreData;

class SoftDelete
{
    static $model = \Modules\Management\Deposit\Database\Models\Model::class;

    public static function execute()
    {
        try {
            if (!$data = self::$model::where('slug', request()->slug)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }

            $userId      = $data->user_id;
            $depositType = $data->deposit_type;

            $data->delete();

            // Re-reconcile this member's dues — the deleted deposit no longer
            // contributes to the paid pool, so some dues may flip back to unpaid.
            if ($depositType === 'share_deposit') {
                DueStoreData::reconcileMember($userId);
            }

            return messageResponse('Item Successfully soft deleted', [], 200, 'success');
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
