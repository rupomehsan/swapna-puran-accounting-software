<?php
namespace Modules\Management\Due\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\Due\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\Due\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        for ($i = 1; $i <= 10; $i++) {
            $dueAmount  = $faker->randomFloat(2, 5000, 25000);
            $paidAmount = $faker->randomFloat(2, 0, $dueAmount);
            self::$model::create([
                'user_id'          => $faker->randomNumber(3),
                'due_amount'       => $dueAmount,
                'paid_amount'      => $paidAmount,
                'remaining_amount' => round($dueAmount - $paidAmount, 2),
                'for_month'        => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'due_date'         => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
                'payment_status'   => $faker->randomElement(['unpaid', 'partial', 'paid']),
                'note'             => $faker->sentence,
            ]);
        }
    }
}
