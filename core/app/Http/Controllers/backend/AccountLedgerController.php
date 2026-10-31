<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountLedgerController extends Controller
{
    public function index()
    {
        $accounts = Account::where('is_active', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('backend.modules.account.ledger.index', compact('accounts'));
    }

    // public function listAjax(Request $request)
    // {
    //     $request->validate([
    //         'account_id' => 'required|exists:accounts,id',
    //         'from_date'  => 'nullable|date',
    //         'to_date'    => 'nullable|date|after_or_equal:from_date',
    //     ]);

    //     $accountId = $request->account_id;

    //     // Branch context
    //     $branchId = auth()->user()->isSuperAdmin()
    //         ? current_branch_id()
    //         : auth()->user()->branch_id;

    //     abort_if(! $branchId, 422, 'Please select a branch');

    //     $from = $request->from_date;
    //     $to   = $request->to_date;

    //     /*
    // |--------------------------------------------------------------------------
    // | 1️⃣ Opening balance (pure journal-based)
    // |--------------------------------------------------------------------------
    // */
    //     $opening = DB::table('journal_entry_lines')
    //         ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
    //         ->where('journal_entry_lines.account_id', $accountId)
    //         ->where('journal_entry_lines.branch_id', $branchId)
    //         ->where(function ($q) use ($from) {
    //             $q->whereDate('journal_entries.entry_date', '<', $from)
    //                 ->orWhere(function ($q2) use ($from) {
    //                     $q2->whereDate('journal_entries.entry_date', '=', $from)
    //                         ->where('journal_entries.voucher_type_id', VoucherType::idByCode('OPENING'));
    //                 });
    //         })
    //         ->sum(DB::raw('journal_entry_lines.debit - journal_entry_lines.credit'));

    //     $opening = round($opening, 2);

    //     /*
    // |--------------------------------------------------------------------------
    // | 2️⃣ Ledger rows (date range)
    // |--------------------------------------------------------------------------
    // */
    //     $lines = DB::table('journal_entry_lines')
    //         ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
    //         ->where('journal_entry_lines.account_id', $accountId)
    //         ->where('journal_entry_lines.branch_id', $branchId)
    //         ->where('journal_entries.voucher_type_id', '!=', VoucherType::idByCode('OPENING')) // 🔑 KEY FIX
    //         ->when($from, fn($q) => $q->whereDate('journal_entries.entry_date', '>=', $from))
    //         ->when($to, fn($q) => $q->whereDate('journal_entries.entry_date', '<=', $to))
    //         ->orderBy('journal_entries.entry_date')
    //         ->orderBy('journal_entry_lines.id')
    //         ->select(
    //             'journal_entries.id as journal_entry_id',
    //             'journal_entries.entry_date',
    //             'journal_entries.voucher_no',
    //             'journal_entries.narration',
    //             'journal_entry_lines.debit',
    //             'journal_entry_lines.credit'
    //         )
    //         ->get();

    //     /*
    // |--------------------------------------------------------------------------
    // | 3️⃣ Build ledger with running balance
    // |--------------------------------------------------------------------------
    // */
    //     $data    = [];
    //     $balance = $opening;

    //     // Opening row
    //     $data[] = [
    //         'date'        => $from ?? '—',
    //         'description' => '<strong>Opening Balance</strong>',
    //         'debit'       => number_format(0, 2),
    //         'credit'      => number_format(0, 2),
    //         'balance'     => number_format($balance, 2),
    //     ];

    //     foreach ($lines as $row) {
    //         $balance += ($row->debit - $row->credit);

    //         $data[] = [
    //             'date'        => $row->entry_date,
    //             'description' => '
    //             <a href="#"
    //                class="ledger-voucher-link AjaxModal"
    //                data-size="lg"
    //                data-ajax-modal="' . route('accounts.vouchers.view', $row->journal_entry_id) . '">
    //                 ' . e($row->voucher_no) . ' — ' . e($row->narration ?? '') . '
    //             </a>',
    //             'debit'       => number_format($row->debit ?? 0, 2),
    //             'credit'      => number_format($row->credit ?? 0, 2),
    //             'balance'     => number_format($balance, 2),
    //         ];
    //     }

    //     return response()->json([
    //         'data' => $data,
    //     ]);
    // }

    public function listAjax(Request $request)
    {

        $accountId = $request->account_id;

        if (! $accountId) {
            return response()->json([
                'draw'                 => (int) $request->draw,
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        // Branch context
        $branchId = auth()->user()->isSuperAdmin()
            ? current_branch_id()
            : auth()->user()->branch_id;

        abort_if(! $branchId, 422, 'Please select a branch');

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : null;

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : null;

        $rows = collect();

        /* -------------------------------------------------
    | 1️⃣ Opening Balance (journal based)
    ------------------------------------------------- */
        $opening = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entry_lines.branch_id', $branchId)
            ->when($from, function ($q) use ($from) {
                $q->whereDate('journal_entries.entry_date', '<', $from);
            })
            ->sum(DB::raw('journal_entry_lines.debit - journal_entry_lines.credit'));

        $opening = round($opening, 2);

        $rows->push([
            'journal_entry_id' => null,
            'date'             => $from ? $from->format('Y-m-d') : '—',
            'ref'              => '',
            'desc'             => 'Opening Balance',
            'debit'            => 0,
            'credit'           => 0,
            'order'            => 0,
        ]);

        /* -------------------------------------------------
    | 2️⃣ Ledger Lines (date range)
    ------------------------------------------------- */
        $lines = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entry_lines.branch_id', $branchId)
            ->when($from, fn($q) => $q->whereDate('journal_entries.entry_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('journal_entries.entry_date', '<=', $to))
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entry_lines.id')
            ->select(
                'journal_entries.id as journal_entry_id',
                'journal_entries.entry_date',
                'journal_entries.voucher_no',
                'journal_entries.narration',
                'journal_entry_lines.debit',
                'journal_entry_lines.credit'
            )
            ->get();

        foreach ($lines as $l) {
            $rows->push([
                'journal_entry_id' => $l->journal_entry_id,
                'date'             => Carbon::parse($l->entry_date)->format('Y-m-d'),
                'ref'              => $l->voucher_no,
                'desc'             => $l->narration ?? '',
                'debit'            => (float) $l->debit,
                'credit'           => (float) $l->credit,
                'order'            => 1,
            ]);
        }

        /* -------------------------------------------------
    | 3️⃣ Sort + Running Balance
    ------------------------------------------------- */
        $rows = $rows->sortBy(['date', 'order'])->values();

        $balance = $opening;
        $data    = [];
        $sl      = 1;

        foreach ($rows as $r) {
            $balance += $r['debit'];
            $balance -= $r['credit'];

            if (! empty($r['journal_entry_id'])) {
                $voucherHtml = '
                <a href="#"
                class="ledger-voucher-link text-primary-400 fw-semibold AjaxViewModal"
                data-size="lg"
                data-ajax-modal="' . route('accounts.vouchers.view', $r['journal_entry_id']) . '">
                    ' . e($r['ref']) . ($r['desc'] ? ' — ' . e($r['desc']) : '') . '
                </a>';
            } else {
                // Opening balance
                $voucherHtml = '<strong>' . e($r['desc']) . '</strong>';
            }
            $data[] = [
                $sl++,
                $r['date'],
                $voucherHtml,
                e($r['desc']),
                number_format($r['debit'], 2),
                number_format($r['credit'], 2),
                number_format($balance, 2),
            ];
        }

        return response()->json([
            'draw'                 => (int) $request->draw,
            'iTotalRecords'        => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData'               => $data,
        ]);
    }

    public function summary(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
        ]);

        $accountId = $request->account_id;

        $branchId = auth()->user()->isSuperAdmin()
            ? current_branch_id()
            : auth()->user()->branch_id;

        abort_if(! $branchId, 422, 'Please select a branch');

        $from = Carbon::parse($request->from_date)->startOfDay();
        $to   = Carbon::parse($request->to_date)->endOfDay();

        /* -------- Opening Balance -------- */
        $opening = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entry_lines.branch_id', $branchId)
            ->whereDate('journal_entries.entry_date', '<', $from)
            ->sum(DB::raw('journal_entry_lines.debit - journal_entry_lines.credit'));

        /* -------- Period Totals -------- */
        $period = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entry_lines.branch_id', $branchId)
            ->whereBetween('journal_entries.entry_date', [$from, $to])
            ->selectRaw('
            SUM(journal_entry_lines.debit) as total_debit,
            SUM(journal_entry_lines.credit) as total_credit
        ')
            ->first();

        $totalDebit  = (float) ($period->total_debit ?? 0);
        $totalCredit = (float) ($period->total_credit ?? 0);

        $closing = round($opening + $totalDebit - $totalCredit, 2);

        return response()->json([
            'opening'      => round($opening, 2),
            'total_debit'  => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'closing'      => $closing,
        ]);
    }

}
