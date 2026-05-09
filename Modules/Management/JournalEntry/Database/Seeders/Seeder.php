<?php
namespace Modules\Management\JournalEntry\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\JournalEntry\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\JournalEntry\Database\Models\Model::class;

    public function run(): void
    {
        // Journal entries are auto-created by TransactionLogService — no fake seed data needed
    }
}
