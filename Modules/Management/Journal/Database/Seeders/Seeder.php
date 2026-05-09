<?php
namespace Modules\Management\Journal\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\Journal\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\Journal\Database\Models\Model::class;

    public function run(): void
    {
        // Journals are auto-created by TransactionLogService — no fake seed data needed
    }
}
