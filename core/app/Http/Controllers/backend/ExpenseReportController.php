<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Expense;
use App\Models\backend\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ExpenseReportController extends Controller
{
    public function index()
    {
        return view('backend.modules.reports.expenses.index');
    }

    public function summary(Request $request)
    {
        $user     = Auth::user();
        $branchId = current_branch_id();

        // 🔒 Check if branch is properly selected
        if (!$branchId || $branchId === 0) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Please select a branch first',
            ], 422);
        }

        // Base query with branch filter
        $baseQuery = Expense::query();
        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        // Apply category filter if provided
        $categoryId = trim((string) $request->input('category_id', ''));
        if ($categoryId && $categoryId !== '') {
            $baseQuery->whereHas('items', function ($q) use ($categoryId) {
                $q->where('expense_category_id', $categoryId);
            });
        }

        // Apply date range filter if provided
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $baseQuery->whereBetween('expense_date', [$request->from_date, $request->to_date]);
        }

        // Clone the base query for different status calculations
        $draft = (float) (clone $baseQuery)->where('status', 'draft')->sum('total_amount');
        $posted = (float) (clone $baseQuery)->where('status', 'posted')->sum('total_amount');
        $total = (float) $baseQuery->sum('total_amount');

        return response()->json([
            'draft' => $draft,
            'posted' => $posted,
            'total' => $total
        ]);
    }

    public function listAjax(Request $request)
    {
        // Guard: don't return results unless date range provided
        if (! $request->filled('from_date') && ! $request->filled('to_date')) {
            return response()->json([
                'draw'                 => (int) $request->input('draw'),
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        $base = Expense::with(['items.category'])
            ->select(['id', 'name', 'reference', 'expense_date', 'total_amount', 'status', 'branch_id']);

        if ($branchId) {
            $base->where('branch_id', $branchId);
        }

        // Category filter
        $categoryId = trim((string) $request->input('category_id', ''));
        if ($categoryId && $categoryId !== '') {
            $base->whereHas('items', function ($q) use ($categoryId) {
                $q->where('expense_category_id', $categoryId);
            });
        }

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $base->whereBetween('expense_date', [$request->from_date, $request->to_date]);
        }

        $total = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $filtered = (clone $base)->count();

        $rows = $base->orderBy('id', $orderDir)->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $categories = $r->items->pluck('category.name')->filter()->unique()->implode(', ');

            $statusBadge = $r->status === 'posted'
                ? '<span class="badge text-sm fw-semibold bg-success-600 px-20 py-9 radius-4 text-white">Posted</span>'
                : '<span class="badge text-sm fw-semibold bg-warning-600 px-20 py-9 radius-4 text-white">Draft</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1">'
                . '<a href="' . route('expenses.show', $r->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-info-focus text-info-main" title="View"><iconify-icon icon="lucide:eye"></iconify-icon></a>'
                . '</div>';

            $data[] = [
                $r->id,
                '<strong>' . e($r->name) . '</strong>',
                e($r->reference ?? '—'),
                e($categories ?: '—'),
                number_format($r->total_amount, 2),
                $statusBadge,
                Carbon::parse($r->expense_date)->format('Y-m-d'),
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
}
