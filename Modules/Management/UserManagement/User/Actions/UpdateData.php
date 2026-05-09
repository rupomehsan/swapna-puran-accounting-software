<?php

namespace Modules\Management\UserManagement\User\Actions;

use Illuminate\Support\Facades\DB;



class UpdateData
{


    static $model = \Modules\Management\UserManagement\User\Database\Models\Model::class;

    public static function execute($request, $slug)
    {
        try {
            // throw new \Exception('Forced exception for testing catch block.');

            if (!$data = self::$model::query()->where('slug', $slug)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }

            $requestData = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $requestData['image'] = uploader($image, 'uploads/users');
            }

        
            $data->update($requestData);

         

            DB::commit();

            return messageResponse('Item updated successfully', $data, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
