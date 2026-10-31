<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\BranchAccount;
use App\Models\backend\Expense;
use App\Models\backend\ExpenseCategory;
use App\Models\backend\ExpenseItem;
use App\Models\backend\ExpensePayment;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\VoucherType;
use App\Support\BranchScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('backend.modules.expenses.index');
    }

    public function listAjax(Request $request)
    {
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));
        $branchAll = BranchScope::isAll();
        $branchId  = current_branch_id();

        $base = Expense::query()
            ->with(['items.category:id,name'])
            ->select(['id', 'name', 'reference', 'description', 'total_amount', 'status']);

        if (! $branchAll && $branchId) {
            $base->where('branch_id', $branchId);
        } elseif (! $branchAll && ! $branchId) {
            $base->whereRaw('1 = 0');
        }

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('reference', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $rows = $base
            ->orderBy('id', $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($rows as $b) {

            // category names
            $categories = $b->items
                ->pluck('category.name')
                ->filter()
                ->unique()
                ->implode(', ');

            $statusBadge = $b->status === 'posted'
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Posted</span>'
                : '<button class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white btn-toggle-status"
                     data-url="' . route('expenses.toggleStatus', $b->id) . '"
                     data-id="' . $b->id . '"
                     style="border: none; cursor: pointer;">Draft</button>';

            // ---------------- ACTION BUTTONS ----------------
            $actions = '<div class="d-inline-flex align-items-center gap-1">';

            // ✏️ Edit + 🗑 Delete only for draft
            if ($b->status !== 'posted') {
                $actions .= '
            <a href="#"
               class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                      bg-success-focus text-success-main AjaxModal"
               data-ajax-modal="' . route('expenses.editModal', $b->id) . '"
               data-size="lg"
               data-onload="ExpenseIndex.onLoad"
               data-onsuccess="ExpenseIndex.onSaved"
               title="Edit Expense">
                <iconify-icon icon="lucide:edit"></iconify-icon>
            </a>

            <a href="#"
               class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                      bg-danger-focus text-danger-main btn-expense-delete"
               data-url="' . route('expenses.destroy', $b->id) . '"
               data-id="' . $b->id . '"
               title="Delete Expense">
                <iconify-icon icon="mdi:delete"></iconify-icon>
            </a>';
            }

            // 🖨 Print (always available)
            $actions .= '
        <a href="' . route('expenses.invoice', $b->id) . '"
           target="_blank"
           class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                  bg-info-focus text-info-main"
           title="Print Invoice">
            <iconify-icon icon="mdi:printer"></iconify-icon>
        </a>';

            $actions .= '</div>';

            // ---------------- TABLE ROW ----------------
            $data[] = [
                $b->id,
                '<strong>' . e($b->name) . '</strong>',
                e($b->reference ?? '—'),
                e($categories ?: '—'),
                e($b->description ?? '—'),
                number_format($b->total_amount, 2),
                $statusBadge,
                $actions,
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    public function createModal()
    {
        $branchAll = BranchScope::isAll();
        $branchId  = current_branch_id();

        $expense_categories = ExpenseCategory::select('id', 'name')
            ->where('is_active', 1)
            ->when(! $branchAll && $branchId, function ($query) use ($branchId) {
                $query->where(function ($inner) use ($branchId) {
                    $inner->where('branch_id', $branchId)
                        ->orWhereNull('branch_id');
                });
            }, function ($query) use ($branchAll, $branchId) {
                if (! $branchAll && ! $branchId) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->get();

        return view('backend.modules.expenses.create_modal', compact('expense_categories')); // partial only
    }

    public function store(Request $req)
    {
        $branchId = current_branch_id();

        // 🔒 Check if branch is properly selected
        if (!$branchId || $branchId === 0) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Please select a branch first',
            ], 422);
        }

        $data = $req->validate([
            'name'                        => 'required|string|max:255',
            'expense_date'                => 'required|date',
            'reference'                   => 'nullable|string|max:255',
            'description'                 => 'nullable|string|max:255',

            'items'                       => 'required|array|min:1',
            'items.*.expense_category_id' => 'required|integer|exists:expense_categories,id',
            'items.*.description'         => 'nullable|string|max:255',
            'items.*.amount'              => 'required|numeric|min:0.01',

            'payment_type_id'             => 'nullable|integer|exists:payment_types,id',
            'status'                      => 'required|in:draft,posted',
        ]);

        $this->assertExpenseCategoriesAreAccessible(collect($data['items'])->pluck('expense_category_id')->all());

        $userId   = auth()->id();

        // total from items (never trust frontend)
        $totalAmount = collect($data['items'])->sum('amount');

        DB::beginTransaction();

        try {

            /* ================= EXPENSE ================= */
            $expense = Expense::create([
                'branch_id'    => $branchId,
                'invoice_no'   => generateInvoiceNo('EXPENSE'),
                'posted_at'    => now(),
                'name'         => $data['name'],
                'reference'    => $data['reference'],
                'expense_date' => $data['expense_date'],
                'description'  => $data['description'],
                'total_amount' => $totalAmount,
                'status'       => $data['status'],
                'created_by'   => $userId,
            ]);

            /* ================= ITEMS ================= */
            foreach ($data['items'] as $item) {
                ExpenseItem::create([
                    'expense_id'          => $expense->id,
                    'expense_category_id' => $item['expense_category_id'],
                    'description'         => $item['description'] ?? null,
                    'amount'              => $item['amount'],
                ]);
            }

            /* ================= PAYMENT ================= */
            if (! empty($data['payment_type_id'])) {
                ExpensePayment::create([
                    'expense_id'      => $expense->id,
                    'payment_type_id' => $data['payment_type_id'],
                    'amount'          => $totalAmount,
                    'created_by'      => $userId,
                ]);
            }

            /* ================= JOURNAL ENTRY ================= */
            if ($data['status'] === 'posted') {

                // 🔑 resolve branch default account
                $cashAccountId = BranchAccount::where('branch_id', $branchId)
                    ->where('is_default', 1)
                    ->value('account_id');

                if (! $cashAccountId) {
                    throw new \Exception('Default branch account not configured');
                }

                $journal = JournalEntry::create([
                    'voucher_no'      => generateVoucherNo('EXPENSE'),
                    'voucher_type_id' => VoucherType::idByCode('EXPENSE'),
                    'branch_id'       => $branchId,
                    'fiscal_year_id'  => currentFiscalYear()->id,
                    'source_id'       => $expense->id,
                    'source_type'     => Expense::class,
                    'entry_date'      => $data['expense_date'],
                    'narration'       => 'Expense: ' . ($expense->reference ?? $expense->name),
                    'created_by'      => $userId,
                ]);

                // CREDIT → cash/bank
                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id'       => $cashAccountId,
                    'branch_id'        => $branchId,
                    'debit'            => 0,
                    'credit'           => $totalAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'ok'   => true,
                'msg'  => 'Expense saved successfully',
                'data' => [
                    'id'     => $expense->id,
                    'status' => $expense->status,
                ],
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'ok'  => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    public function editModal(Expense $expense)
    {
        // 🔒 Posted expense edit block (UI safety)
        if ($expense->status === 'posted') {
            abort(403, 'Posted expense cannot be edited');
        }

        // eager load relations needed for edit modal
        $expense->load([
            'items.category:id,name',
            'payment.paymentType:id,name',
        ]);

        return view('backend.modules.expenses.edit_modal', compact('expense'));
    }

    public function update(Request $req, Expense $expense)
    {
        // 🔒 Check if branch is properly selected
        $branchId = current_branch_id();
        if (!$branchId || $branchId === 0) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Please select a branch first',
            ], 422);
        }

        // 🔒 Posted expense cannot be modified
        if ($expense->status === 'posted') {
            return response()->json([
                'ok'  => false,
                'msg' => 'Posted expense cannot be modified',
            ], 422);
        }

        $data = $req->validate([
            'name'                        => 'required|string|max:255',
            'expense_date'                => 'required|date',
            'reference'                   => 'nullable|string|max:255',
            'description'                 => 'nullable|string|max:255',

            'items'                       => 'required|array|min:1',
            'items.*.expense_category_id' => 'required|integer|exists:expense_categories,id',
            'items.*.description'         => 'nullable|string|max:255',
            'items.*.amount'              => 'required|numeric|min:0.01',

            'payment_type_id'             => 'nullable|integer|exists:payment_types,id',
            'status'                      => 'required|in:draft,posted',
        ]);

        $this->assertExpenseCategoriesAreAccessible(collect($data['items'])->pluck('expense_category_id')->all());

        $userId   = auth()->id();

        $totalAmount = collect($data['items'])->sum('amount');

        DB::beginTransaction();

        try {

            /* ================= UPDATE EXPENSE ================= */
            $expense->update([
                'name'         => $data['name'],
                'invoice_no'   => $data['invoice_no'] ?? null,
                'posted_at'    => $data['posted_at'] ?? null,
                'expense_date' => $data['expense_date'],
                'reference'    => $data['reference'],
                'description'  => $data['description'],
                'total_amount' => $totalAmount,
                'status'       => $data['status'],
            ]);

            /* ================= RESET ITEMS ================= */
            $expense->items()->delete();

            foreach ($data['items'] as $item) {
                $expense->items()->create([
                    'expense_category_id' => $item['expense_category_id'],
                    'description'         => $item['description'] ?? null,
                    'amount'              => $item['amount'],
                ]);
            }

            /* ================= PAYMENT ================= */
            if (! empty($data['payment_type_id'])) {
                $expense->payment()->updateOrCreate(
                    ['expense_id' => $expense->id],
                    [
                        'payment_type_id' => $data['payment_type_id'],
                        'amount'          => $totalAmount,
                        'created_by'      => $userId,
                    ]
                );
            } else {
                // no payment selected → remove old payment (draft only)
                $expense->payment()->delete();
            }

            /* ================= POSTING (JOURNAL) ================= */
            if ($data['status'] === 'posted') {

                $cashAccountId = BranchAccount::where('branch_id', $branchId)
                    ->where('is_default', 1)
                    ->value('account_id');

                if (! $cashAccountId) {
                    throw new \Exception('Default branch account not configured');
                }

                $journal = JournalEntry::create([
                    'voucher_no'      => generateVoucherNo('EXPENSE'),
                    'voucher_type_id' => VoucherType::idByCode('EXPENSE'),
                    'branch_id'       => $branchId,
                    'fiscal_year_id'  => currentFiscalYear()->id,
                    'source_id'       => $expense->id,
                    'source_type'     => Expense::class,
                    'entry_date'      => $data['expense_date'],
                    'narration'       => 'Expense: ' . ($expense->reference ?? $expense->name),
                    'created_by'      => $userId,
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id'       => $cashAccountId,
                    'branch_id'        => $branchId,
                    'debit'            => 0,
                    'credit'           => $totalAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'ok'  => true,
                'msg' => 'Expense updated successfully',
                'id'  => $expense->id,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'ok'  => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    public function invoice(Expense $expense)
    {
        $expense->load([
            'items.category',
            'payment.paymentType',
            'branch',
            'creator',

        ]);

        return view(
            'backend.modules.expenses.invoice',
            compact('expense')
        );
    }

    public function destroy(Expense $expense)
    {
        if ($expense->status === 'posted') {
            return response()->json(['ok' => false, 'msg' => 'Approved transaction cant be deleted'], 422);

        }
        $expense->delete();

        return response()->json(['ok' => true, 'msg' => 'Expense deleted']);
    }

    public function select2(Request $r)
    {

        $q    = trim($r->input('q', ''));
        $type = $r->type;
        $base = Expense::query()->where('category_type_id', $type)->where('is_active', 1);

        if ($q !== '') {
            $base->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%");
            });
        }

        $items = $base->orderBy('id')->orderBy('name')
            ->limit(20)->get(['id', 'name']);

        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->name,
            ]),
        ]);
    }

    /**
     * Toggle expense status from draft to posted
     */
    public function toggleStatus(Expense $expense)
    {
        try {
            // Check if already posted
            if ($expense->status === 'posted') {
                return response()->json([
                    'ok'  => false,
                    'msg' => 'Expense is already posted',
                ], 422);
            }

            $branchId = current_branch_id();
            $userId   = auth()->id();

            DB::beginTransaction();

            // Update status
            $expense->update([
                'status'    => 'posted',
                'posted_at' => now(),
            ]);

            // Create journal entry for accounting
            $cashAccountId = BranchAccount::where('branch_id', $branchId)
                ->where('is_default', 1)
                ->value('account_id');

            if ($cashAccountId) {
                $journal = JournalEntry::create([
                    'voucher_no'      => generateVoucherNo('EXPENSE'),
                    'voucher_type_id' => VoucherType::idByCode('EXPENSE'),
                    'branch_id'       => $branchId,
                    'fiscal_year_id'  => currentFiscalYear()->id,
                    'source_id'       => $expense->id,
                    'source_type'     => Expense::class,
                    'entry_date'      => $expense->expense_date,
                    'narration'       => 'Expense: ' . ($expense->reference ?? $expense->name),
                    'created_by'      => $userId,
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id'       => $cashAccountId,
                    'branch_id'        => $branchId,
                    'debit'            => 0,
                    'credit'           => $expense->total_amount,
                ]);
            }

            DB::commit();

            return response()->json([
                'ok'   => true,
                'msg'  => 'Expense posted successfully',
                'data' => [
                    'id'     => $expense->id,
                    'status' => $expense->status,
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok'  => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    protected function assertExpenseCategoriesAreAccessible(array $categoryIds): void
    {
        $categoryIds = collect($categoryIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($categoryIds->isEmpty() || BranchScope::isAll()) {
            return;
        }

        $branchId = current_branch_id();

        if (! $branchId) {
            abort(422, 'Please select a branch first');
        }

        $matchedCount = ExpenseCategory::query()
            ->whereIn('id', $categoryIds)
            ->where(function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            })
            ->count();

        abort_if($matchedCount !== $categoryIds->count(), 422, 'Invalid expense category selected for this branch');
    }
}
