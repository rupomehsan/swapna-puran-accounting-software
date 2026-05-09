<?php
namespace Modules\Management\IncomeEntry\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\IncomeEntry\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\IncomeEntry\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        $sources = ['Service Charge', 'Membership Fee', 'Investment Return', 'Interest Income'];

        for ($i = 1; $i <= 10; $i++) {
            self::$model::create([
                'voucher_no'    => 'INC-' . $faker->unique()->numerify('########'),
                'income_source' => $faker->randomElement($sources),
                'account_id'    => null,
                'amount'        => $faker->randomFloat(2, 500, 10000),
                'entry_date'    => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'description'   => $faker->sentence,
            ]);
        }
    }
}
