<?php
namespace Modules\Management\TransactionLog\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\TransactionLog\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\TransactionLog\Database\Models\Model::class;

    public function run(): void
    {
        // Transaction logs are auto-created by TransactionLogService — no fake seed data needed
    }
}
