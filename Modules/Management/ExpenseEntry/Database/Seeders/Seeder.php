<?php
namespace Modules\Management\ExpenseEntry\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\ExpenseEntry\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\ExpenseEntry\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        $categories = ['Office Rent', 'Staff Salary', 'Internet Bill', 'Utility Bill', 'Stationery'];

        for ($i = 1; $i <= 10; $i++) {
            self::$model::create([
                'voucher_no'       => 'EXP-' . $faker->unique()->numerify('########'),
                'expense_category' => $faker->randomElement($categories),
                'account_id'       => null,
                'amount'           => $faker->randomFloat(2, 200, 15000),
                'entry_date'       => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'description'      => $faker->sentence,
            ]);
        }
    }
}
