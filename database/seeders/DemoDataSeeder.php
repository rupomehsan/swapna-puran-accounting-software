<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Run: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Clean slate for member/financial data ──────────────
        DB::table('deposits')->whereIn('user_id', DB::table('users')->where('role_id', 2)->pluck('id'))->delete();
        DB::table('users')->where('role_id', 2)->delete();
        DB::table('income_entries')->truncate();
        DB::table('expense_entries')->truncate();

        // ── Members ────────────────────────────────────────────
        $members = [
            ['name' => 'মো. রফিকুল ইসলাম',   'phone' => '01711223344', 'email' => 'rafiqul@example.com',   'shares' => 10, 'join' => '2022-01-15'],
            ['name' => 'তাসলিমা বেগম',         'phone' => '01722334455', 'email' => 'taslima@example.com',   'shares' => 5,  'join' => '2022-03-01'],
            ['name' => 'আব্দুল করিম',           'phone' => '01733445566', 'email' => 'karim@example.com',     'shares' => 8,  'join' => '2022-06-20'],
            ['name' => 'নাসরিন আক্তার',         'phone' => '01744556677', 'email' => 'nasrin@example.com',    'shares' => 6,  'join' => '2022-09-10'],
            ['name' => 'মো. সালাহউদ্দিন',      'phone' => '01755667788', 'email' => 'salah@example.com',     'shares' => 12, 'join' => '2023-01-05'],
            ['name' => 'রেহেনা পারভীন',         'phone' => '01766778899', 'email' => 'rehena@example.com',    'shares' => 4,  'join' => '2023-04-18'],
            ['name' => 'মো. জাকির হোসেন',      'phone' => '01777889900', 'email' => 'zakir@example.com',     'shares' => 7,  'join' => '2023-07-22'],
            ['name' => 'সানজিদা ইসলাম',         'phone' => '01788990011', 'email' => 'sanjida@example.com',   'shares' => 3,  'join' => '2023-11-30'],
            ['name' => 'মো. আবু তাহের',         'phone' => '01799001122', 'email' => 'taher@example.com',     'shares' => 9,  'join' => '2024-02-14'],
            ['name' => 'শাহনাজ পারভীন',         'phone' => '01700112233', 'email' => 'shahnaz@example.com',   'shares' => 6,  'join' => '2024-05-08'],
        ];

        $memberIds = [];
        foreach ($members as $m) {
            $id = DB::table('users')->insertGetId([
                'role_id'        => 2,
                'name'           => $m['name'],
                'email'          => $m['email'],
                'phone'          => $m['phone'],
                'password'       => Hash::make('password'),
                'number_of_share'=> $m['shares'],
                'join_date'      => $m['join'],
                'slug'           => Str::slug($m['name']) . '-' . rand(100, 999),
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $memberIds[] = ['id' => $id, 'shares' => $m['shares'], 'join' => $m['join']];
        }

        // ── Deposits ───────────────────────────────────────────
        $methods  = ['cash', 'bank', 'mobile_banking', 'cheque'];
        $counter  = 1;
        $months   = ['2024-01-01','2024-02-01','2024-03-01','2024-04-01','2024-05-01',
                     '2024-06-01','2024-07-01','2024-08-01','2024-09-01','2024-10-01',
                     '2024-11-01','2024-12-01','2025-01-01','2025-02-01','2025-03-01',
                     '2025-04-01','2025-05-01'];

        foreach ($memberIds as $member) {
            // Share deposits — roughly monthly since join
            $shareAmount = $member['shares'] * 500;
            foreach (array_slice($months, rand(0, 4)) as $month) {
                DB::table('deposits')->insert([
                    'user_id'        => $member['id'],
                    'voucher_no'     => 'DEP-' . str_pad($counter++, 6, '0', STR_PAD_LEFT),
                    'deposit_type'   => 'share_deposit',
                    'amount'         => $shareAmount,
                    'for_month'      => $month,
                    'payment_date'   => date('Y-m-d', strtotime($month . ' +' . rand(0, 10) . ' days')),
                    'payment_method' => $methods[array_rand($methods)],
                    'note'           => 'মাসিক শেয়ার জমা',
                    'slug'           => 'dep-' . Str::random(8),
                    'status'         => 'active',
                    'creator'        => 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // Extra savings — 2 to 4 irregular deposits
            $extraCount = rand(2, 4);
            $extraMonths = array_rand(array_flip($months), $extraCount);
            if (!is_array($extraMonths)) $extraMonths = [$extraMonths];
            foreach ($extraMonths as $month) {
                DB::table('deposits')->insert([
                    'user_id'        => $member['id'],
                    'voucher_no'     => 'DEP-' . str_pad($counter++, 6, '0', STR_PAD_LEFT),
                    'deposit_type'   => 'extra_savings',
                    'amount'         => rand(2, 20) * 1000,
                    'for_month'      => $month,
                    'payment_date'   => date('Y-m-d', strtotime($month . ' +' . rand(0, 15) . ' days')),
                    'payment_method' => $methods[array_rand($methods)],
                    'note'           => 'অতিরিক্ত সঞ্চয়',
                    'slug'           => 'dep-' . Str::random(8),
                    'status'         => 'active',
                    'creator'        => 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // ── Income entries ─────────────────────────────────────
        $incomes = [
            ['source' => 'বিনিয়োগ আয়',      'amount' => 15000.00, 'date' => '2024-03-15', 'desc' => 'শেয়ার বিনিয়োগ থেকে প্রাপ্ত আয়'],
            ['source' => 'ভাড়া আয়',          'amount' => 8500.00,  'date' => '2024-04-01', 'desc' => 'অফিস স্পেস ভাড়া'],
            ['source' => 'বিনিয়োগ আয়',      'amount' => 22000.00, 'date' => '2024-05-20', 'desc' => 'মিউচুয়াল ফান্ড রিটার্ন'],
            ['source' => 'সুদ আয়',            'amount' => 4200.00,  'date' => '2024-06-30', 'desc' => 'ব্যাংক সুদ'],
            ['source' => 'সার্ভিস চার্জ',     'amount' => 3000.00,  'date' => '2024-07-10', 'desc' => 'প্রসেসিং ফি'],
            ['source' => 'বিনিয়োগ আয়',      'amount' => 18500.00, 'date' => '2024-08-25', 'desc' => 'রিয়েল এস্টেট বিনিয়োগ'],
            ['source' => 'ভাড়া আয়',          'amount' => 8500.00,  'date' => '2024-09-01', 'desc' => 'অফিস স্পেস ভাড়া'],
            ['source' => 'সুদ আয়',            'amount' => 5100.00,  'date' => '2024-10-31', 'desc' => 'এফডিআর সুদ'],
            ['source' => 'বিনিয়োগ আয়',      'amount' => 31000.00, 'date' => '2024-11-15', 'desc' => 'বার্ষিক বিনিয়োগ রিটার্ন'],
            ['source' => 'ভাড়া আয়',          'amount' => 8500.00,  'date' => '2024-12-01', 'desc' => 'অফিস স্পেস ভাড়া'],
            ['source' => 'সুদ আয়',            'amount' => 6300.00,  'date' => '2025-01-31', 'desc' => 'ব্যাংক সুদ Q4'],
            ['source' => 'বিনিয়োগ আয়',      'amount' => 24000.00, 'date' => '2025-03-10', 'desc' => 'পোর্টফোলিও রিটার্ন'],
            ['source' => 'ভাড়া আয়',          'amount' => 9000.00,  'date' => '2025-04-01', 'desc' => 'অফিস স্পেস ভাড়া নবায়ন'],
            ['source' => 'সার্ভিস চার্জ',     'amount' => 4500.00,  'date' => '2025-05-05', 'desc' => 'প্রশাসনিক ফি'],
        ];

        foreach ($incomes as $i => $inc) {
            DB::table('income_entries')->insert([
                'voucher_no'    => 'INC-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'income_source' => $inc['source'],
                'amount'        => $inc['amount'],
                'entry_date'    => $inc['date'],
                'description'   => $inc['desc'],
                'slug'          => 'inc-' . Str::random(8),
                'status'        => 'active',
                'creator'       => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // ── Expense entries ────────────────────────────────────
        $expenses = [
            ['category' => 'অফিস ভাড়া',        'amount' => 5000.00, 'date' => '2024-03-05', 'desc' => 'মাসিক অফিস ভাড়া'],
            ['category' => 'ইউটিলিটি বিল',     'amount' => 1200.00, 'date' => '2024-04-10', 'desc' => 'বিদ্যুৎ ও পানি বিল'],
            ['category' => 'অফিস সাপ্লাই',      'amount' => 3500.00, 'date' => '2024-05-12', 'desc' => 'স্টেশনারি ও প্রিন্টিং'],
            ['category' => 'অফিস ভাড়া',        'amount' => 5000.00, 'date' => '2024-06-05', 'desc' => 'মাসিক অফিস ভাড়া'],
            ['category' => 'যোগাযোগ খরচ',      'amount' => 800.00,  'date' => '2024-07-15', 'desc' => 'ইন্টারনেট ও ফোন বিল'],
            ['category' => 'অফিস ভাড়া',        'amount' => 5000.00, 'date' => '2024-08-05', 'desc' => 'মাসিক অফিস ভাড়া'],
            ['category' => 'রক্ষণাবেক্ষণ',     'amount' => 4200.00, 'date' => '2024-09-20', 'desc' => 'অফিস মেরামত'],
            ['category' => 'ইউটিলিটি বিল',     'amount' => 1400.00, 'date' => '2024-10-10', 'desc' => 'বিদ্যুৎ বিল'],
            ['category' => 'অফিস ভাড়া',        'amount' => 5000.00, 'date' => '2024-11-05', 'desc' => 'মাসিক অফিস ভাড়া'],
            ['category' => 'বার্ষিক অনুষ্ঠান',  'amount' => 12000.00,'date' => '2024-12-20', 'desc' => 'বার্ষিক সদস্য সভা'],
            ['category' => 'অফিস ভাড়া',        'amount' => 5500.00, 'date' => '2025-01-05', 'desc' => 'মাসিক অফিস ভাড়া (বর্ধিত)'],
            ['category' => 'যোগাযোগ খরচ',      'amount' => 950.00,  'date' => '2025-02-15', 'desc' => 'ইন্টারনেট বিল'],
            ['category' => 'অফিস সাপ্লাই',      'amount' => 2800.00, 'date' => '2025-03-22', 'desc' => 'অফিস সরঞ্জাম'],
            ['category' => 'অফিস ভাড়া',        'amount' => 5500.00, 'date' => '2025-04-05', 'desc' => 'মাসিক অফিস ভাড়া'],
        ];

        foreach ($expenses as $i => $exp) {
            DB::table('expense_entries')->insert([
                'voucher_no'       => 'EXP-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'expense_category' => $exp['category'],
                'amount'           => $exp['amount'],
                'entry_date'       => $exp['date'],
                'description'      => $exp['desc'],
                'slug'             => 'exp-' . Str::random(8),
                'status'           => 'active',
                'creator'          => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $this->command->info('✓ Demo data seeded: 10 members, deposits, 14 income entries, 14 expense entries.');
    }
}
