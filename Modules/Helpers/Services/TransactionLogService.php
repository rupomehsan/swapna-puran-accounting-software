<?php

namespace Modules\Helpers\Services;

use Illuminate\Support\Facades\DB;

class TransactionLogService
{
    static $logModel    = \Modules\Management\TransactionLog\Database\Models\Model::class;
    static $journalModel = \Modules\Management\Journal\Database\Models\Model::class;
    static $journalEntryModel = \Modules\Management\JournalEntry\Database\Models\Model::class;

    /**
     * Record a transaction log entry and create the double-entry journal.
     *
     * @param array $params
     *   - transaction_type  string   share_deposit|extra_savings|withdrawal|income|expense|due_created|due_collection
     *   - related_type      string   class name of source model e.g. 'Deposit'
     *   - related_id        int      PK of source record
     *   - user_id           int|null member's user id (null for org-level income/expense)
     *   - amount            float    always positive
     *   - direction         string   credit|debit
     *   - transaction_date  string   datetime string
     *   - description       string
     *   - voucher_no        string   shared voucher from source
     *   - debit_account_id  int      accounts.id to debit
     *   - credit_account_id int      accounts.id to credit
     */
    public static function record(array $params): void
    {
        $userId = $params['user_id'] ?? null;

        // Compute running balance:
        // - member transactions  → per-member running balance
        // - org-level (user_id null) → org-wide cash running balance
        $prevQuery = self::$logModel::whereNull('deleted_at')->orderBy('id', 'desc');
        $prevBalance = $userId
            ? ($prevQuery->where('user_id', $userId)->value('balance_after') ?? 0)
            : ($prevQuery->whereNull('user_id')->value('balance_after') ?? 0);

        $balanceAfter = $params['direction'] === 'credit'
            ? $prevBalance + $params['amount']
            : $prevBalance - $params['amount'];

        // Insert transaction log
        self::$logModel::create([
            'voucher_no'       => $params['voucher_no'],
            'transaction_type' => $params['transaction_type'],
            'related_type'     => $params['related_type'],
            'related_id'       => $params['related_id'],
            'user_id'          => $userId,
            'amount'           => $params['amount'],
            'direction'        => $params['direction'],
            'balance_after'    => $balanceAfter,
            'transaction_date' => $params['transaction_date'],
            'description'      => $params['description'] ?? '',
        ]);

        // Insert journal + journal entries (double-entry)
        if (!empty($params['debit_account_id']) && !empty($params['credit_account_id'])) {
            $journal = self::$journalModel::create([
                'voucher_no'     => $params['voucher_no'],
                'reference_type' => $params['related_type'],
                'reference_id'   => $params['related_id'],
                'journal_date'   => now()->toDateString(),
                'description'    => $params['description'] ?? '',
                'total_debit'    => $params['amount'],
                'total_credit'   => $params['amount'],
            ]);

            self::$journalEntryModel::create([
                'journal_id'  => $journal->id,
                'account_id'  => $params['debit_account_id'],
                'entry_type'  => 'debit',
                'amount'      => $params['amount'],
                'description' => $params['description'] ?? '',
            ]);

            self::$journalEntryModel::create([
                'journal_id'  => $journal->id,
                'account_id'  => $params['credit_account_id'],
                'entry_type'  => 'credit',
                'amount'      => $params['amount'],
                'description' => $params['description'] ?? '',
            ]);
        }
    }

    /**
     * Generate a unique voucher number.
     * Format: TXN-YYYYMMDD-{random6}
     */
    public static function generateVoucher(string $prefix = 'TXN'): string
    {
        do {
            $voucher = strtoupper($prefix) . '-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::$logModel::where('voucher_no', $voucher)->exists());

        return $voucher;
    }

    /**
     * Get current member balance.
     */
    public static function getMemberBalance(int $userId): float
    {
        return (float) (self::$logModel::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->value('balance_after') ?? 0);
    }

    /**
     * Get default account id by code.
     */
    public static function accountId(string $code): ?int
    {
        return DB::table('accounts')->where('account_code', $code)->value('id');
    }
}
