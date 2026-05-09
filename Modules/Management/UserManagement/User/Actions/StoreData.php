<?php

namespace Modules\Management\UserManagement\User\Actions;

use Illuminate\Support\Facades\DB;
use App\Events\UserActivityEvent;


class StoreData
{


    static $model = \Modules\Management\UserManagement\User\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $requestData['image'] = uploader($image, 'uploads/users');
            }

            $requestData['password'] = bcrypt($requestData['password']);
            
            if ($data = self::$model::query()->create($requestData)) {
                return messageResponse('Item added successfully', $data, 201);
            }
          


        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}