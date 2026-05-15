<?php

namespace Modules\Management\Due\Actions;

class UpdateData
{
    static $model = \Modules\Management\Due\Database\Models\Model::class;

    public static function execute($request, $slug)
    {
        try {
            if (!$due = self::$model::query()->where('slug', $slug)->first()) {
                return messageResponse('Data not found...', $due, 404, 'error');
            }

            $due->update($request->validated());
            $due->refresh();

            // Re-reconcile after update (due_amount or for_month may have changed)
            StoreData::reconcile($due);

            return messageResponse('Item updated successfully', $due, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
