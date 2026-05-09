<?php

namespace Modules\Management\UserManagement\Role\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="\Modules\Management\UserManagement\Role\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\UserManagement\Role\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'name' => "super_admin",
            'serial_no' => 1,
        ]);
        self::$model::create([
            'name' => "member",
            'serial_no' => 2,
        ]);

        // Generate 100 additional roles
      

       
    }
}
