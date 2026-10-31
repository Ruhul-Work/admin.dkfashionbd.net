<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use App\Models\backend\BusinessJournal;
use App\Models\backend\BusinessJournalRecord;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\VoucherType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessJournalController extends Controller
{
    // ──────────────────────────────────────────────
    // Index page
    // ──────────────────────────────────────────────
    public function index()
    {
        return view('backend.modules.account.business_journals.index');
    }

    // ──────────────────────────────────────────────
    // Records Index page
    // ──────────────────────────────────────────────
    public function recordsIndex()
    {
        return view('backend.modules.account.business_journals.records');
    }

    // ──────────────────────────────────────────────
    // AJAX list for Business Journals (DataTable)
    // ──────────────────────────────────────────────
    public function journalListAjax(Request $request)
    {
        $draw   = (int) $request->input('draw');
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim($request->input('search.value', ''));

        $branchId = current_branch_id();

        $base = BusinessJournal::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('creator:id,name');

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $total    = (clone $base)->count();
        $filtered = $total;

        $rows = $base->orderByDesc('date')->orderByDesc('id')
            ->skip($start)->take($length)->get();

        $data = [];
        $sl   = $start + 1;

        foreach ($rows as $j) {
            $data[] = [
                $sl++,
                e($j->name),
                e($j->category ?? '—'),
                $j->date->format('d M Y'),
                e($j->creator?->name ?? '—'),
                // Actions
                '<div class="d-inline-flex gap-1">'
                . '<button class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-delete-journal" '
                . 'data-url="' . route('business-journals.destroy', $j->id) . '" title="Delete">'
                . '<iconify-icon icon="mdi:delete"></iconify-icon></button>'
                . '</div>',
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    // ──────────────────────────────────────────────
    // AJAX list for Journal Records (DataTable)
    // ──────────────────────────────────────────────
    public function recordListAjax(Request $request)
    {
        $draw   = (int) $request->input('draw');
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim($request->input('search.value', ''));

        $branchId = current_branch_id();

        $base = BusinessJournalRecord::query()
            ->with([
                'businessJournal:id,name,category',
                'account:id,name',
            ])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->whereHas('businessJournal', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('account', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhere('narration', 'like', "%{$search}%");
            });
        }

        $total    = (clone $base)->count();
        $filtered = $total;

        $rows = $base->orderByDesc('date')->orderByDesc('id')
            ->skip($start)->take($length)->get();

        $data = [];
        $sl   = $start + 1;

        foreach ($rows as $r) {
            $typeBadge = $r->payment_type === 'Incoming'
                ? '<span class="badge bg-success-subtle text-success px-8 py-6 radius-4">Incoming</span>'
                : '<span class="badge bg-danger-subtle text-danger px-8 py-6 radius-4">Outgoing</span>';

            $data[] = [
                $sl++,
                e($r->businessJournal?->name ?? '—'),
                $typeBadge,
                $r->date->format('d M Y'),
                e($r->account?->name ?? '—'),
                number_format($r->amount, 2),
                e($r->narration ?? '—'),
                // Actions
                '<div class="d-inline-flex gap-1">'
                . '<a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-success-focus text-success-main AjaxModal" '
                . 'data-ajax-modal="' . route('business-journals.records.edit', $r->id) . '" '
                . 'data-size="lg" data-onsuccess="BizJournalIndex.onRecordSaved" title="Edit">'
                . '<iconify-icon icon="lucide:edit"></iconify-icon></a>'
                . '<button class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-delete-record" '
                . 'data-url="' . route('business-journals.records.destroy', $r->id) . '" title="Delete">'
                . '<iconify-icon icon="mdi:delete"></iconify-icon></button>'
                . '</div>',
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    // ──────────────────────────────────────────────
    // Create Journal modal
    // ──────────────────────────────────────────────
    public function createJournalModal()
    {
        return view('backend.modules.account.business_journals.modal.create_journal');
    }

    // ──────────────────────────────────────────────
    // Store Journal
    // ──────────────────────────────────────────────
    public function storeJournal(Request $request)
    {
        $data = $request->validate([
            'date'     => 'required|date',
            'name'     => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $branchId = current_branch_id();

        BusinessJournal::create([
            'name'       => $data['name'],
            'category'   => $data['category'] ?? null,
            'date'       => $data['date'],
            'branch_id'  => $branchId,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'msg' => 'Journal created successfully.']);
    }

    // ──────────────────────────────────────────────
    // Delete Journal (+ cascade records & journal lines)
    // ──────────────────────────────────────────────
    public function destroyJournal(BusinessJournal $businessJournal)
    {
        DB::transaction(function () use ($businessJournal) {
            // Delete all associated journal entries from accounting
            $journalEntryIds = $businessJournal->records()->pluck('journal_entry_id')->filter();
            JournalEntryLine::whereIn('journal_entry_id', $journalEntryIds)->delete();
            JournalEntry::whereIn('id', $journalEntryIds)->delete();

            // Records will cascade delete via DB FK, but we also clean them explicitly
            $businessJournal->records()->delete();
            $businessJournal->delete();
        });

        return response()->json(['success' => true, 'msg' => 'Journal deleted successfully.']);
    }

    // ──────────────────────────────────────────────
    // Create Record modal
    // ──────────────────────────────────────────────
    public function createRecordModal()
    {
        $branchId = current_branch_id();

        $journals = BusinessJournal::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('date')
            ->get(['id', 'name', 'category']);

        $accounts = Account::query()
            ->where('is_active', 1)
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('branchAccounts', fn($q2) => $q2->where('branch_id', $branchId)->where('is_active', 1));
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('backend.modules.account.business_journals.modal.create_record', compact('journals', 'accounts'));
    }

    // ──────────────────────────────────────────────
    // Store Record + Single-Entry accounting
    // ──────────────────────────────────────────────
    public function storeRecord(Request $request)
    {
        $data = $request->validate([
            'business_journal_id' => 'required|exists:business_journals,id',
            'payment_type'        => 'required|in:Incoming,Outgoing',
            'date'                => 'required|date',
            'account_id'          => 'required|exists:accounts,id',
            'amount'              => 'required|numeric|min:0.01',
            'narration'           => 'nullable|string|max:1000',
        ]);

        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch first.');

        $fy = requireFiscalYear();

        return DB::transaction(function () use ($data, $branchId, $fy) {

            // ── Single-entry journal line ──────────────────────────────
            // Incoming → Debit the account (money comes in, balance ↑)
            // Outgoing → Credit the account (money goes out, balance ↓)
            $isIncoming = $data['payment_type'] === 'Incoming';

            $journalEntry = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('B_JOURNAL'),
                'voucher_type_id' => VoucherType::idByCode('B_JOURNAL'),
                'branch_id'       => $branchId,
                'fiscal_year_id'  => $fy->id,
                'entry_date'      => $data['date'],
                'narration'       => $data['narration'] ?? ($isIncoming ? 'Business Journal Incoming' : 'Business Journal Outgoing'),
                'created_by'      => auth()->id(),
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id'       => $data['account_id'],
                'branch_id'        => $branchId,
                'debit'            => $isIncoming ? $data['amount'] : 0,
                'credit'           => $isIncoming ? 0 : $data['amount'],
            ]);
            // ──────────────────────────────────────────────────────────

            $record = BusinessJournalRecord::create([
                'business_journal_id' => $data['business_journal_id'],
                'payment_type'        => $data['payment_type'],
                'date'                => $data['date'],
                'account_id'          => $data['account_id'],
                'amount'              => $data['amount'],
                'narration'           => $data['narration'] ?? null,
                'journal_entry_id'    => $journalEntry->id,
                'branch_id'           => $branchId,
                'created_by'          => auth()->id(),
            ]);

            return response()->json(['success' => true, 'msg' => 'Journal record saved successfully.']);
        });
    }

    // ──────────────────────────────────────────────
    // Edit Record modal
    // ──────────────────────────────────────────────
    public function editRecordModal(BusinessJournalRecord $businessJournalRecord)
    {
        $branchId = current_branch_id();

        $journals = BusinessJournal::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('date')
            ->get(['id', 'name', 'category']);

        $accounts = Account::query()
            ->where('is_active', 1)
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('branchAccounts', fn($q2) => $q2->where('branch_id', $branchId)->where('is_active', 1));
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $record = $businessJournalRecord;

        return view('backend.modules.account.business_journals.modal.edit_record', compact('record', 'journals', 'accounts'));
    }

    // ──────────────────────────────────────────────
    // Update Record + re-sync single-entry accounting
    // ──────────────────────────────────────────────
    public function updateRecord(Request $request, BusinessJournalRecord $businessJournalRecord)
    {
        $data = $request->validate([
            'business_journal_id' => 'required|exists:business_journals,id',
            'payment_type'        => 'required|in:Incoming,Outgoing',
            'date'                => 'required|date',
            'account_id'          => 'required|exists:accounts,id',
            'amount'              => 'required|numeric|min:0.01',
            'narration'           => 'nullable|string|max:1000',
        ]);

        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch first.');

        $fy = requireFiscalYear();

        return DB::transaction(function () use ($data, $branchId, $fy, $businessJournalRecord) {

            $isIncoming = $data['payment_type'] === 'Incoming';

            // ── Delete old journal entry + line ────────────────────────
            if ($businessJournalRecord->journal_entry_id) {
                JournalEntryLine::where('journal_entry_id', $businessJournalRecord->journal_entry_id)->delete();
                JournalEntry::where('id', $businessJournalRecord->journal_entry_id)->delete();
            }

            // ── Create fresh single-entry journal line ─────────────────
            $journalEntry = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('B_JOURNAL'),
                'voucher_type_id' => VoucherType::idByCode('B_JOURNAL'),
                'branch_id'       => $branchId,
                'fiscal_year_id'  => $fy->id,
                'entry_date'      => $data['date'],
                'narration'       => $data['narration'] ?? ($isIncoming ? 'Business Journal Incoming' : 'Business Journal Outgoing'),
                'created_by'      => auth()->id(),
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id'       => $data['account_id'],
                'branch_id'        => $branchId,
                'debit'            => $isIncoming ? $data['amount'] : 0,
                'credit'           => $isIncoming ? 0 : $data['amount'],
            ]);
            // ──────────────────────────────────────────────────────────

            $businessJournalRecord->update([
                'business_journal_id' => $data['business_journal_id'],
                'payment_type'        => $data['payment_type'],
                'date'                => $data['date'],
                'account_id'          => $data['account_id'],
                'amount'              => $data['amount'],
                'narration'           => $data['narration'] ?? null,
                'journal_entry_id'    => $journalEntry->id,
            ]);

            return response()->json(['success' => true, 'msg' => 'Journal record updated successfully.']);
        });
    }

    // ──────────────────────────────────────────────
    // Delete Record + revert accounting
    // ──────────────────────────────────────────────
    public function destroyRecord(BusinessJournalRecord $businessJournalRecord)
    {
        DB::transaction(function () use ($businessJournalRecord) {
            if ($businessJournalRecord->journal_entry_id) {
                JournalEntryLine::where('journal_entry_id', $businessJournalRecord->journal_entry_id)->delete();
                JournalEntry::where('id', $businessJournalRecord->journal_entry_id)->delete();
            }
            $businessJournalRecord->delete();
        });

        return response()->json(['success' => true, 'msg' => 'Record deleted successfully.']);
    }

    // ──────────────────────────────────────────────
    // Ledger page
    // ──────────────────────────────────────────────
    public function ledgerIndex()
    {
        $branchId = current_branch_id();

        $journals = BusinessJournal::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('date')
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'date']);

        return view('backend.modules.account.business_journals.ledger', compact('journals'));
    }

    // ──────────────────────────────────────────────
    // Ledger data
    // ──────────────────────────────────────────────
    public function ledgerListAjax(Request $request)
    {
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $search    = trim($request->input('search.value', ''));
        $journalId = $request->business_journal_id;

        $branchId = current_branch_id();

        if (! $journalId) {
            return response()->json([
                'draw'                 => $draw,
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : null;

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : null;

        $baseQuery = BusinessJournalRecord::query()
            ->with([
                'businessJournal:id,name,category',
                'account:id,name',
                'journalEntry:id,voucher_no',
            ])
            ->where('business_journal_id', $journalId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        if ($from) {
            $openingQuery = (clone $baseQuery)->whereDate('date', '<', $from->toDateString());
            $opening      = 0;

            foreach ($openingQuery->get() as $row) {
                $opening += $row->payment_type === 'Incoming'
                    ? (float) $row->amount
                    : - (float) $row->amount;
            }
        } else {
            $opening = 0;
        }

        $rowsQuery = (clone $baseQuery)
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from->toDateString()))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to->toDateString()))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->whereHas('businessJournal', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('account', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('journalEntry', fn($q2) => $q2->where('voucher_no', 'like', "%{$search}%"))
                        ->orWhere('payment_type', 'like', "%{$search}%")
                        ->orWhere('narration', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%");
                });
            })
            ->orderBy('date')
            ->orderBy('id');

        $records = $rowsQuery->get();

        $rows = collect();

        if ($from) {
            $rows->push([
                'date'             => $from->format('Y-m-d'),
                'journal'          => 'Opening Balance',
                'account'          => '—',
                'type'             => '—',
                'reference'        => '—',
                'amount'           => 0,
                'balance_effect'   => 0,
                'journal_entry_id' => null,
                'narration'        => null,
                'is_opening'       => true,
            ]);
        }

        foreach ($records as $record) {
            $rows->push([
                'date'             => Carbon::parse($record->date)->format('Y-m-d'),
                'journal'          => $record->businessJournal?->name ?? '—',
                'account'          => $record->account?->name ?? '—',
                'type'             => $record->payment_type,
                'reference'        => $record->journalEntry?->voucher_no ?? ('BJ-' . $record->id),
                'amount'           => (float) $record->amount,
                'balance_effect'   => $record->payment_type === 'Incoming'
                    ? (float) $record->amount
                    : - (float) $record->amount,
                'journal_entry_id' => $record->journal_entry_id,
                'narration'        => $record->narration,
                'is_opening'       => false,
            ]);
        }

        $balance = $opening;
        $allData = [];
        $sl      = 1;

        foreach ($rows as $row) {
            if (! empty($row['is_opening'])) {
                $balance = round($balance, 2);
                $allData[] = [
                    $sl++,
                    $row['date'],
                    '<strong>Opening Balance</strong>',
                    '—',
                    '—',
                    '—',
                    '—',
                    number_format(0, 2),
                    number_format($balance, 2),
                ];
                continue;
            }

            $balance += $row['balance_effect'];

            $referenceHtml = $row['journal_entry_id']
                ? '<a href="#" class="ledger-voucher-link text-primary-400 fw-semibold AjaxViewModal" data-size="lg" data-ajax-modal="' . route('accounts.vouchers.view', $row['journal_entry_id']) . '">'
                    . e($row['reference'])
                    . '</a>'
                : e($row['reference']);

            $allData[] = [
                $sl++,
                $row['date'],
                e($row['journal']),
                e($row['account']),
                e($row['type']),
                $referenceHtml,
                e($row['narration'] ?? '—'),
                number_format($row['amount'], 2),
                number_format($balance, 2),
            ];
        }

        $data = array_slice($allData, $start, $length);

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => count($allData),
            'iTotalDisplayRecords' => count($allData),
            'aaData'               => $data,
        ]);
    }

    // ──────────────────────────────────────────────
    // Ledger summary
    // ──────────────────────────────────────────────
    public function ledgerSummary(Request $request)
    {
        $request->validate([
            'business_journal_id' => 'required|exists:business_journals,id',
        ]);

        $journalId = $request->business_journal_id;
        $branchId   = current_branch_id();

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : null;

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : null;

        $baseQuery = BusinessJournalRecord::query()
            ->where('business_journal_id', $journalId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $opening = 0;
        if ($from) {
            $openingQuery = (clone $baseQuery)->whereDate('date', '<', $from->toDateString());
            foreach ($openingQuery->get(['payment_type', 'amount']) as $row) {
                $opening += $row->payment_type === 'Incoming'
                    ? (float) $row->amount
                    : - (float) $row->amount;
            }
        }

        $periodQuery = (clone $baseQuery)
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from->toDateString()))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to->toDateString()));

        $totalIncoming = (float) (clone $periodQuery)
            ->where('payment_type', 'Incoming')
            ->sum('amount');

        $totalOutgoing = (float) (clone $periodQuery)
            ->where('payment_type', 'Outgoing')
            ->sum('amount');

        $closing = round($opening + $totalIncoming - $totalOutgoing, 2);

        return response()->json([
            'opening'        => round($opening, 2),
            'total_incoming' => round($totalIncoming, 2),
            'total_outgoing' => round($totalOutgoing, 2),
            'closing'        => $closing,
        ]);
    }
}
