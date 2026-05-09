<?php
namespace Modules\Management\Deposit\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\Deposit\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\Deposit\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        for ($i = 1; $i <= 10; $i++) {
            self::$model::create([
                'user_id'        => $faker->randomNumber(3),
                'voucher_no'     => 'DEP-' . $faker->unique()->numerify('########'),
                'deposit_type'   => $faker->randomElement(['share_deposit', 'extra_savings']),
                'amount'         => $faker->randomFloat(2, 1000, 50000),
                'for_month'      => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'payment_date'   => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'payment_method' => $faker->randomElement(['cash', 'bank', 'mobile_banking']),
                'due_id'         => null,
                'note'           => $faker->sentence,
                'received_by'    => 1,
            ]);
        }
    }
}
