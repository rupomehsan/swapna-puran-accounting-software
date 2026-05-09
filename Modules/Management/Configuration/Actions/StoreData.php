<?php

namespace Modules\Management\Configuration\Actions;

class StoreData
{
    static $model = \Modules\Management\Configuration\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            $existing = self::$model::first();

            if ($existing) {
                $existing->update($requestData);
                return messageResponse('Configuration updated successfully', $existing, 200);
            }

            $data = self::$model::create($requestData);
            return messageResponse('Configuration saved successfully', $data, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
