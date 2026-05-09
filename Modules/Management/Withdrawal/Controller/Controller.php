<?php

namespace Modules\Management\Withdrawal\Controller;
use Modules\Management\Withdrawal\Actions\GetAllData;
use Modules\Management\Withdrawal\Actions\DestroyData;
use Modules\Management\Withdrawal\Actions\GetSingleData;
use Modules\Management\Withdrawal\Actions\StoreData;
use Modules\Management\Withdrawal\Actions\UpdateData;
use Modules\Management\Withdrawal\Actions\UpdateStatus;
use Modules\Management\Withdrawal\Actions\SoftDelete;
use Modules\Management\Withdrawal\Actions\RestoreData;
use Modules\Management\Withdrawal\Actions\ImportData;
use Modules\Management\Withdrawal\Validations\BulkActionsValidation;
use Modules\Management\Withdrawal\Validations\DataStoreValidation;
use Modules\Management\Withdrawal\Actions\BulkActions;
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

    public function update(DataStoreValidation $request, $slug)
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