<?php

namespace Modules\Management\TransactionLog\Controller;
use Modules\Management\TransactionLog\Actions\GetAllData;
use Modules\Management\TransactionLog\Actions\DestroyData;
use Modules\Management\TransactionLog\Actions\GetSingleData;
use Modules\Management\TransactionLog\Actions\StoreData;
use Modules\Management\TransactionLog\Actions\UpdateData;
use Modules\Management\TransactionLog\Actions\UpdateStatus;
use Modules\Management\TransactionLog\Actions\SoftDelete;
use Modules\Management\TransactionLog\Actions\RestoreData;
use Modules\Management\TransactionLog\Actions\ImportData;
use Modules\Management\TransactionLog\Validations\BulkActionsValidation;
use Modules\Management\TransactionLog\Validations\DataStoreValidation;
use Modules\Management\TransactionLog\Actions\BulkActions;
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