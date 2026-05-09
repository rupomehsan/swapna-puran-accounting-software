<?php

namespace Modules\Management\Account\Controller;
use Modules\Management\Account\Actions\GetAllData;
use Modules\Management\Account\Actions\DestroyData;
use Modules\Management\Account\Actions\GetSingleData;
use Modules\Management\Account\Actions\StoreData;
use Modules\Management\Account\Actions\UpdateData;
use Modules\Management\Account\Actions\UpdateStatus;
use Modules\Management\Account\Actions\SoftDelete;
use Modules\Management\Account\Actions\RestoreData;
use Modules\Management\Account\Actions\ImportData;
use Modules\Management\Account\Validations\BulkActionsValidation;
use Modules\Management\Account\Validations\DataStoreValidation;
use Modules\Management\Account\Validations\DataUpdateValidation;
use Modules\Management\Account\Actions\BulkActions;
use App\Http\Controllers\Controller as ControllersController;


class Controller extends ControllersController
{

    public function index( ){

        $data = GetAllData::execute();
        return $data;
    }

    public function store(DataStoreValidation $request)
    {
        $data = StoreData::execute($request);
        return $data;
    }

    public function show($slug)
    {
        $data = GetSingleData::execute($slug);
        return $data;
    }

    public function update(DataUpdateValidation $request, $slug)
    {
        $data = UpdateData::execute($request, $slug);
        return $data;
    }
         public function updateStatus()
    {
        $data = UpdateStatus::execute();
        return $data;
    }

    public function softDelete()
    {
        $data = SoftDelete::execute();
        return $data;
    }
    public function destroy($slug)
    {
        $data = DestroyData::execute($slug);
        return $data;
    }
    public function restore()
    {
        $data = RestoreData::execute();
        return $data;
    }
    public function import()
    {
        $data = ImportData::execute();
        return $data;
    }
    public function bulkAction(BulkActionsValidation $request)
    {
        $data = BulkActions::execute($request);
        return $data;
    }

}