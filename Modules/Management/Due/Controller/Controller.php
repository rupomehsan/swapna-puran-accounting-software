<?php

namespace Modules\Management\Due\Controller;
use Modules\Management\Due\Actions\GetAllData;
use Modules\Management\Due\Actions\DestroyData;
use Modules\Management\Due\Actions\GetSingleData;
use Modules\Management\Due\Actions\StoreData;
use Modules\Management\Due\Actions\UpdateData;
use Modules\Management\Due\Actions\UpdateStatus;
use Modules\Management\Due\Actions\SoftDelete;
use Modules\Management\Due\Actions\RestoreData;
use Modules\Management\Due\Actions\ImportData;
use Modules\Management\Due\Validations\BulkActionsValidation;
use Modules\Management\Due\Validations\DataStoreValidation;
use Modules\Management\Due\Actions\BulkActions;
use Modules\Management\Due\Actions\GenerateDue;
use Modules\Management\Due\Actions\MarkPaid;
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

    public function generateDue()
    {
        $data = GenerateDue::execute();
        return $data;
    }

    public function markPaid($slug)
    {
        $data = MarkPaid::execute($slug);
        return $data;
    }

}