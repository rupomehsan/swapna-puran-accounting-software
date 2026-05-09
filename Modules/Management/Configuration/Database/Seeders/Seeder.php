<?php
namespace Modules\Management\Configuration\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\Configuration\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\Configuration\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        for ($i = 1; $i <= 100; $i++) {
            self::$model::create([                'share_price' => $faker->randomFloat(2, 0, 1000),
                'start_date' => $faker->dateTime()->format('Y-m-d H:i:s'),
                'currency' => $faker->text(10),
            ]);
        }
    }
}