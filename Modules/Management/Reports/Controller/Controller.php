<?php

namespace Modules\Management\Reports\Controller;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    public function ledger()
    {
        try {
            $accountId = request()->input('account_id');
            $startDate = request()->input('start_date');
            $endDate   = request()->input('end_date');

            if (!$accountId) {
                return response()->json(['message' => 'account_id is required', 'status' => 'error'], 422);
            }

            $account = DB::table('accounts')->where('id', $accountId)->whereNull('deleted_at')->first();
            if (!$account) {
                return response()->json(['message' => 'Account not found'], 404);
            }

            $isDebitNormal = in_array($account->account_type, ['asset', 'expense']);

            // Opening balance = account opening_balance + all entries before start_date
            $openingQ = DB::table('journal_entries as je')
                ->join('journals as j', 'j.id', '=', 'je.journal_id')
                ->where('je.account_id', $accountId)
                ->whereNull('je.deleted_at')
                ->whereNull('j.deleted_at');
            if ($startDate) {
                $openingQ->where('j.journal_date', '<', $startDate);
            }
            $ot = $openingQ->selectRaw(
                'COALESCE(SUM(CASE WHEN je.entry_type="debit"  THEN je.amount ELSE 0 END),0) as debit,
                 COALESCE(SUM(CASE WHEN je.entry_type="credit" THEN je.amount ELSE 0 END),0) as credit'
            )->first();

            $openingBalance  = (float) $account->opening_balance;
            $openingBalance += $isDebitNormal
                ? ((float)$ot->debit  - (float)$ot->credit)
                : ((float)$ot->credit - (float)$ot->debit);

            // Period entries
            $entryQ = DB::table('journal_entries as je')
                ->join('journals as j', 'j.id', '=', 'je.journal_id')
                ->where('je.account_id', $accountId)
                ->whereNull('je.deleted_at')
                ->whereNull('j.deleted_at');
            if ($startDate) $entryQ->where('j.journal_date', '>=', $startDate);
            if ($endDate)   $entryQ->where('j.journal_date', '<=', $endDate);

            $entries = $entryQ
                ->select('je.entry_type', 'je.amount', 'je.description as e_desc',
                         'j.journal_date', 'j.voucher_no', 'j.description as j_desc')
                ->orderBy('j.journal_date')
                ->orderBy('j.id')
                ->get();

            $running = $openingBalance;
            $totalDr = 0;
            $totalCr = 0;

            $rows = $entries->map(function ($e) use (&$running, &$totalDr, &$totalCr, $isDebitNormal) {
                $dr = $e->entry_type === 'debit'  ? (float)$e->amount : 0;
                $cr = $e->entry_type === 'credit' ? (float)$e->amount : 0;
                $totalDr += $dr;
                $totalCr += $cr;
                $running += $isDebitNormal ? ($dr - $cr) : ($cr - $dr);
                return [
                    'date'        => $e->journal_date,
                    'voucher_no'  => $e->voucher_no,
                    'description' => $e->e_desc ?: $e->j_desc,
                    'debit'       => $dr,
                    'credit'      => $cr,
                    'balance'     => $running,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'account' => [
                        'id'           => $account->id,
                        'account_code' => $account->account_code,
                        'account_name' => $account->account_name,
                        'account_type' => $account->account_type,
                    ],
                    'opening_balance' => $openingBalance,
                    'entries'         => $rows,
                    'totals'          => [
                        'debit'           => $totalDr,
                        'credit'          => $totalCr,
                        'closing_balance' => $running,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'server_error'], 500);
        }
    }

    public function trialBalance()
    {
        try {
            $startDate = request()->input('start_date');
            $endDate   = request()->input('end_date');

            $sub = DB::table('journal_entries as je')
                ->join('journals as j', 'j.id', '=', 'je.journal_id')
                ->whereNull('je.deleted_at')
                ->whereNull('j.deleted_at');
            if ($startDate) $sub->where('j.journal_date', '>=', $startDate);
            if ($endDate)   $sub->where('j.journal_date', '<=', $endDate);
            $sub->groupBy('je.account_id')->select(
                'je.account_id',
                DB::raw('SUM(CASE WHEN je.entry_type="debit"  THEN je.amount ELSE 0 END) as total_debit'),
                DB::raw('SUM(CASE WHEN je.entry_type="credit" THEN je.amount ELSE 0 END) as total_credit')
            );

            $accounts = DB::table('accounts as a')
                ->leftJoinSub($sub, 'es', 'es.account_id', '=', 'a.id')
                ->whereNull('a.deleted_at')
                ->where('a.status', 'active')
                ->select(
                    'a.account_code', 'a.account_name', 'a.account_type', 'a.opening_balance',
                    DB::raw('COALESCE(es.total_debit,  0) as total_debit'),
                    DB::raw('COALESCE(es.total_credit, 0) as total_credit')
                )
                ->orderBy('a.account_code')
                ->get();

            $grandDr = 0;
            $grandCr = 0;

            $rows = $accounts->map(function ($a) use (&$grandDr, &$grandCr) {
                $isDebitNormal = in_array($a->account_type, ['asset', 'expense']);
                $balance = $isDebitNormal
                    ? ((float)$a->opening_balance + (float)$a->total_debit  - (float)$a->total_credit)
                    : ((float)$a->opening_balance + (float)$a->total_credit - (float)$a->total_debit);

                $dr = $isDebitNormal  ? max(0, $balance)  : max(0, -$balance);
                $cr = !$isDebitNormal ? max(0, $balance)  : max(0, -$balance);

                $grandDr += $dr;
                $grandCr += $cr;

                return [
                    'account_code'   => $a->account_code,
                    'account_name'   => $a->account_name,
                    'account_type'   => $a->account_type,
                    'debit_balance'  => $dr,
                    'credit_balance' => $cr,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'accounts'     => $rows,
                    'grand_totals' => ['debit' => $grandDr, 'credit' => $grandCr],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'server_error'], 500);
        }
    }

    public function profitLoss()
    {
        try {
            $startDate = request()->input('start_date');
            $endDate   = request()->input('end_date');

            $income   = $this->entryTotalsForTypes(['income'],  'credit', $startDate, $endDate);
            $expenses = $this->entryTotalsForTypes(['expense'], 'debit',  $startDate, $endDate);

            $totalIncome  = (float)$income->sum('amount');
            $totalExpense = (float)$expenses->sum('amount');
            $net          = $totalIncome - $totalExpense;

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'income'        => $income,
                    'expenses'      => $expenses,
                    'total_income'  => $totalIncome,
                    'total_expense' => $totalExpense,
                    'net_profit'    => $net,
                    'is_profit'     => $net >= 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'server_error'], 500);
        }
    }

    public function balanceSheet()
    {
        try {
            $asOf = request()->input('as_of_date') ?? now()->toDateString();

            $assets      = $this->accountBalances(['asset'],     $asOf);
            $liabilities = $this->accountBalances(['liability'], $asOf);
            $equity      = $this->accountBalances(['equity'],    $asOf);

            $income   = $this->entryTotalsForTypes(['income'],  'credit', null, $asOf);
            $expense  = $this->entryTotalsForTypes(['expense'], 'debit',  null, $asOf);
            $retained = (float)$income->sum('amount') - (float)$expense->sum('amount');

            $totalAssets = (float)$assets->sum('balance');
            $totalLiab   = (float)$liabilities->sum('balance');
            $totalEquity = (float)$equity->sum('balance') + $retained;

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'as_of_date'        => $asOf,
                    'assets'            => $assets,
                    'liabilities'       => $liabilities,
                    'equity'            => $equity,
                    'retained_earnings' => $retained,
                    'total_assets'      => $totalAssets,
                    'total_liabilities' => $totalLiab,
                    'total_equity'      => $totalEquity,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'server_error'], 500);
        }
    }

    private function entryTotalsForTypes(array $types, string $entryType, ?string $startDate, ?string $endDate)
    {
        $q = DB::table('accounts as a')
            ->join('journal_entries as je', 'je.account_id', '=', 'a.id')
            ->join('journals as j', 'j.id', '=', 'je.journal_id')
            ->whereIn('a.account_type', $types)
            ->where('je.entry_type', $entryType)
            ->where('a.status', 'active')
            ->whereNull('a.deleted_at')
            ->whereNull('je.deleted_at')
            ->whereNull('j.deleted_at');
        if ($startDate) $q->where('j.journal_date', '>=', $startDate);
        if ($endDate)   $q->where('j.journal_date', '<=', $endDate);

        return $q->groupBy('a.id', 'a.account_code', 'a.account_name')
            ->select('a.account_code', 'a.account_name', DB::raw('COALESCE(SUM(je.amount),0) as amount'))
            ->orderBy('a.account_code')
            ->get();
    }

    private function accountBalances(array $types, ?string $endDate)
    {
        $sub = DB::table('journal_entries as je')
            ->join('journals as j', 'j.id', '=', 'je.journal_id')
            ->whereNull('je.deleted_at')
            ->whereNull('j.deleted_at');
        if ($endDate) $sub->where('j.journal_date', '<=', $endDate);
        $sub->groupBy('je.account_id')->select(
            'je.account_id',
            DB::raw('SUM(CASE WHEN je.entry_type="debit"  THEN je.amount ELSE 0 END) as total_debit'),
            DB::raw('SUM(CASE WHEN je.entry_type="credit" THEN je.amount ELSE 0 END) as total_credit')
        );

        return DB::table('accounts as a')
            ->leftJoinSub($sub, 'es', 'es.account_id', '=', 'a.id')
            ->whereIn('a.account_type', $types)
            ->whereNull('a.deleted_at')
            ->where('a.status', 'active')
            ->select(
                'a.account_code', 'a.account_name', 'a.account_type', 'a.opening_balance',
                DB::raw('COALESCE(es.total_debit,  0) as total_debit'),
                DB::raw('COALESCE(es.total_credit, 0) as total_credit')
            )
            ->orderBy('a.account_code')
            ->get()
            ->map(function ($a) {
                $isDebitNormal = in_array($a->account_type, ['asset', 'expense']);
                $balance = $isDebitNormal
                    ? ((float)$a->opening_balance + (float)$a->total_debit  - (float)$a->total_credit)
                    : ((float)$a->opening_balance + (float)$a->total_credit - (float)$a->total_debit);
                return [
                    'account_code' => $a->account_code,
                    'account_name' => $a->account_name,
                    'balance'      => $balance,
                ];
            });
    }
}
