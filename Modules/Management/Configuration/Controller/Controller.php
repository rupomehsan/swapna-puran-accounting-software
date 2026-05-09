<?php

namespace Modules\Management\Configuration\Controller;
use Modules\Management\Configuration\Actions\GetAllData;
use Modules\Management\Configuration\Actions\DestroyData;
use Modules\Management\Configuration\Actions\GetSingleData;
use Modules\Management\Configuration\Actions\StoreData;
use Modules\Management\Configuration\Actions\UpdateData;
use Modules\Management\Configuration\Actions\UpdateStatus;
use Modules\Management\Configuration\Actions\SoftDelete;
use Modules\Management\Configuration\Actions\RestoreData;
use Modules\Management\Configuration\Actions\ImportData;
use Modules\Management\Configuration\Validations\BulkActionsValidation;
use Modules\Management\Configuration\Validations\DataStoreValidation;
use Modules\Management\Configuration\Actions\BulkActions;
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