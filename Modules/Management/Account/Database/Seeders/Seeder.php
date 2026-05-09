<?php
namespace Modules\Management\Account\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\Account\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\Account\Database\Models\Model::class;

    public function run(): void
    {
        self::$model::truncate();

        $accounts = [
            ['account_code' => '1000', 'account_name' => 'Cash Account',     'account_type' => 'asset',     'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '1001', 'account_name' => 'Bank Account',      'account_type' => 'asset',     'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '2000', 'account_name' => 'Member Capital',    'account_type' => 'equity',    'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '2001', 'account_name' => 'Retained Earnings', 'account_type' => 'equity',    'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '3000', 'account_name' => 'Service Income',    'account_type' => 'income',    'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '3001', 'account_name' => 'Membership Fee',    'account_type' => 'income',    'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '4000', 'account_name' => 'Office Rent',       'account_type' => 'expense',   'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '4001', 'account_name' => 'Staff Salary',      'account_type' => 'expense',   'parent_id' => null, 'opening_balance' => 0],
            ['account_code' => '4002', 'account_name' => 'Utility Bill',      'account_type' => 'expense',   'parent_id' => null, 'opening_balance' => 0],
        ];

        foreach ($accounts as $account) {
            self::$model::create($account);
        }
    }
}
