<?php
namespace Modules\Management\Withdrawal\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\Withdrawal\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\Withdrawal\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        for ($i = 1; $i <= 10; $i++) {
            self::$model::create([
                'user_id'         => $faker->randomNumber(3),
                'voucher_no'      => 'WTH-' . $faker->unique()->numerify('########'),
                'amount'          => $faker->randomFloat(2, 500, 20000),
                'withdrawal_date' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'payment_method'  => $faker->randomElement(['cash', 'bank', 'mobile_banking']),
                'reason'          => $faker->sentence,
                'note'            => $faker->sentence,
            ]);
        }
    }
}
