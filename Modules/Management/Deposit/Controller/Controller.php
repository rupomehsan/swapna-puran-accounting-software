<?php

namespace Modules\Management\Deposit\Controller;
use Modules\Management\Deposit\Actions\GetAllData;
use Modules\Management\Deposit\Actions\DestroyData;
use Modules\Management\Deposit\Actions\GetSingleData;
use Modules\Management\Deposit\Actions\StoreData;
use Modules\Management\Deposit\Actions\UpdateData;
use Modules\Management\Deposit\Actions\UpdateStatus;
use Modules\Management\Deposit\Actions\SoftDelete;
use Modules\Management\Deposit\Actions\RestoreData;
use Modules\Management\Deposit\Actions\ImportData;
use Modules\Management\Deposit\Validations\BulkActionsValidation;
use Modules\Management\Deposit\Validations\DataStoreValidation;
use Modules\Management\Deposit\Validations\DataUpdateValidation;
use Modules\Management\Deposit\Actions\BulkActions;
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