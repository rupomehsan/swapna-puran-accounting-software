<?php

namespace Modules\Management\ShareAdjustment\Controller;

use App\Http\Controllers\Controller as ControllersController;
use Modules\Management\ShareAdjustment\Actions\GetAllData;
use Modules\Management\ShareAdjustment\Actions\PreviewAdjustment;
use Modules\Management\ShareAdjustment\Actions\StoreAdjustment;

class Controller extends ControllersController
{
    public function index()
    {
        return GetAllData::execute();
    }

    public function preview()
    {
        return PreviewAdjustment::execute();
    }

    public function store()
    {
        return StoreAdjustment::execute();
    }
}
