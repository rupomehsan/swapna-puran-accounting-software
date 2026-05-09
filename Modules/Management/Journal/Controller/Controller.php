<?php

namespace Modules\Management\Journal\Controller;
use Modules\Management\Journal\Actions\GetAllData;
use Modules\Management\Journal\Actions\DestroyData;
use Modules\Management\Journal\Actions\GetSingleData;
use Modules\Management\Journal\Actions\StoreData;
use Modules\Management\Journal\Actions\UpdateData;
use Modules\Management\Journal\Actions\UpdateStatus;
use Modules\Management\Journal\Actions\SoftDelete;
use Modules\Management\Journal\Actions\RestoreData;
use Modules\Management\Journal\Actions\ImportData;
use Modules\Management\Journal\Validations\BulkActionsValidation;
use Modules\Management\Journal\Validations\DataStoreValidation;
use Modules\Management\Journal\Actions\BulkActions;
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