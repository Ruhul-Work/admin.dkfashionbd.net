<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\PurchaseOrder;
use App\Models\backend\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseReportController extends Controller
{
    public function index()
    {
        return view('backend.modules.reports.purchase.index');
    }

    public function summary(Request $request)
    {
        $branchId = current_branch_id();

        // 🔒 Check if branch is properly selected
        if (!$branchId || $branchId === 0) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Please select a branch first',
            ], 422);
        }

        $query = PurchaseOrder::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $total = (float) $query->sum('total_amount');
        $paid  = (float) $query->sum('paid_amount');
        $due   = (float) $query->sum('outstanding_amount');

        return response()->json([
            'total' => $total,
            'paid'  => $paid,
            'due'   => $due,
        ]);
    }

    public function listAjax(Request $request)
    {
        // Guard: don't return results unless date range provided (use from_date/to_date)
        if (! $request->filled('from_date') && ! $request->filled('to_date')) {
            return response()->json([
                'draw'                 => (int) $request->input('draw'),
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        $columns = [
            'id',
            'po_number',
            'supplier_name',
            'total_amount',
            'paid_amount',
            'outstanding_amount',
            'status',
            'payment_status',
            'created_at',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        $base = PurchaseOrder::with(['supplier'])->select([
            'purchase_orders.id',
            'purchase_orders.po_number',
            'purchase_orders.supplier_id',
            'purchase_orders.total_amount',
            'purchase_orders.paid_amount',
            'purchase_orders.outstanding_amount',
            'purchase_orders.status',
            'purchase_orders.payment_status',
            'purchase_orders.created_at',
        ]);

        // Branch restriction
        if ($branchId) {
            $base->where('purchase_orders.branch_id', $branchId);
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $base->where('purchase_orders.warehouse_id', $request->warehouse_id);
        }

        // Date range filter (from_date/to_date)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $base->whereBetween('purchase_orders.created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $total = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('purchase_orders.po_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $poLink       = route('purchase.orders.show', $r->id);
            $supplierName = $r->supplier ? e($r->supplier->name) : '-';

            $actions = '<div class="d-inline-flex justify-content-end gap-1">'
                . '<a href="' . $poLink . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-info-focus text-info-main" title="View"><iconify-icon icon="lucide:eye"></iconify-icon></a>'
                . '</div>';

            $data[] = [
                $r->id,
                '<strong><a href="' . $poLink . '">' . e($r->po_number) . '</a></strong>',
                $supplierName,
                number_format((float) $r->total_amount, 2),
                number_format((float) $r->paid_amount, 2),
                number_format((float) $r->outstanding_amount, 2),
                '<span>' . e(ucfirst($r->status)) . '</span>',
                '<span>' . e(ucfirst(str_replace('_', ' ', $r->payment_status))) . '</span>',
                $r->created_at->format('Y-m-d'),
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
