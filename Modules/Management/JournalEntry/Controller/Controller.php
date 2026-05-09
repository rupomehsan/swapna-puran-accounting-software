<?php

namespace Modules\Management\JournalEntry\Controller;
use Modules\Management\JournalEntry\Actions\GetAllData;
use Modules\Management\JournalEntry\Actions\DestroyData;
use Modules\Management\JournalEntry\Actions\GetSingleData;
use Modules\Management\JournalEntry\Actions\StoreData;
use Modules\Management\JournalEntry\Actions\UpdateData;
use Modules\Management\JournalEntry\Actions\UpdateStatus;
use Modules\Management\JournalEntry\Actions\SoftDelete;
use Modules\Management\JournalEntry\Actions\RestoreData;
use Modules\Management\JournalEntry\Actions\ImportData;
use Modules\Management\JournalEntry\Validations\BulkActionsValidation;
use Modules\Management\JournalEntry\Validations\DataStoreValidation;
use Modules\Management\JournalEntry\Actions\BulkActions;
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