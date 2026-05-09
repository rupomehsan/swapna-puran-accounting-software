<?php

namespace Modules\Management\Configuration\Actions;

class GetAllData
{
    static $model = \Modules\Management\Configuration\Database\Models\Model::class;

    public static function execute()
    {
        try {
            $config = self::$model::first();

            return entityResponse([
                'data' => $config ? [$config] : [],
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
