<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Product;
use App\Models\backend\Sale;
use App\Models\backend\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

use function Laravel\Prompts\select;

class SalesReportController extends Controller
{
    public function index()
    {
        return view('backend.modules.reports.sales.index');
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

        $query = Sale::where('status', 'delivered');

        // Apply branch filter (current user's branch if not super admin)
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Apply warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Apply date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $total = (float) $query->sum('total');
        $paid  = (float) $query->sum('paid_amount');
        $due   = (float) $query->sum('due_amount');

        return response()->json([
            'total' => $total,
            'paid'  => $paid,
            'due'   => $due,
        ]);
    }

    public function listAjax(Request $request)
    {
        // Guard: Return empty data if no date range is provided (prevent auto-load on page load)
        if (! $request->filled('start_date') && ! $request->filled('end_date')) {
            return response()->json([
                'draw'                 => (int) $request->input('draw'),
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        $columns = [
            'id',
            'invoice_no',
            'customer_name',
            'total',
            'paid_amount',
            'due_amount',
            'status',
            'payment_status',
            'created_at',
            'user_id',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $user     = Auth::user();
        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        $base = Sale::with(['customer', 'user'])
            ->where('status', 'delivered')
            ->select([
                'sales.id',
                'sales.invoice_no',
                'sales.customer_id',
                'sales.total',
                'sales.paid_amount',
                'sales.due_amount',
                'sales.status',
                'sales.payment_status',
                'sales.created_at',
                'sales.branch_id',
                'sales.user_id',
                'sales.warehouse_id',
            ]);

        // Apply branch filter (current user's branch if not super admin)
        if ($branchId) {
            $base->where('sales.branch_id', $branchId);
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $base->where('sales.warehouse_id', $request->warehouse_id);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $base->whereBetween('sales.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $total = (clone $base)->count();

        // Search
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('sales.invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];

        foreach ($rows as $r) {
            $statusBadge = match ($r->status) {
                'hold'      => '<span class="badge text-sm fw-semibold bg-warning-600 px-20 py-9 radius-4 text-white">Hold</span>',
                'delivered' => '<span class="badge text-sm fw-semibold bg-success-600 px-20 py-9 radius-4 text-white">Delivered</span>',
                'void'      => '<span class="badge text-sm fw-semibold bg-danger-600 px-20 py-9 radius-4 text-white">Void</span>',
                default     => '<span class="badge text-sm fw-semibold bg-lilac-600 px-20 py-9 radius-4 text-white">' . e($r->status) . '</span>',
            };

            $payBadge = match ($r->payment_status) {
                'paid'  => '<span class="badge text-sm fw-semibold rounded-pill bg-success-600 px-20 py-9 radius-4 text-white">Paid</span>',
                'due'   => '<span class="badge text-sm fw-semibold rounded-pill bg-warning-600 px-20 py-9 radius-4 text-white">Due</span>',
                default => '<span class="badge text-sm fw-semibold rounded-pill bg-lilac-600 px-20 py-9 radius-4 text-white">' . e($r->payment_status) . '</span>',
            };

            $actions  = '<div class="d-inline-flex justify-content-end gap-1">';
            $actions .= '<a href="' . route('pos.sales.invoice', $r->id) . '" target="_blank" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-info-focus text-info-main" title="Invoice"><iconify-icon icon="mdi:printer-outline"></iconify-icon></a>';
            $actions .= '</div>';

            $data[] = [
                $r->id,
                '<strong>' . $r->invoice_no . '</strong>',
                e($r->customer?->name ?? 'Walk In'),
                number_format($r->total, 2),
                number_format($r->paid_amount, 2),
                number_format($r->due_amount, 2),
                $statusBadge,
                $payBadge,
                $r->created_at->format('Y-m-d H:i'),
                e($r->user?->name ?? '-'),
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

    public function sourceBasedIndex()
    {
        return view('backend.modules.reports.sales.source_base');
    }

    public function sourceBasedSummary(Request $request)
    {
        $branchId = current_branch_id();

        // 🔒 Check if branch is properly selected
        if (!$branchId || $branchId === 0) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Please select a branch first',
            ], 422);
        }

        $query = Sale::with('items')->where('status', 'delivered');

        // Apply branch filter (current user's branch if not super admin)
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Apply warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Apply date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        // Apply sale type filter
        if ($request->filled('sale_type')) {
            $query->where('sale_type', $request->sale_type);
        }

        // Apply source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $total = (float) $query->sum('total');
        $paid  = (float) $query->sum('paid_amount');
        $due   = (float) $query->sum('due_amount');
        $quantity = $query->get()->map(function ($sale) {
            return $sale->items->sum('quantity');
        })->sum();

        return response()->json([
            'total' => $total,
            'paid'  => $paid,
            'due'   => $due,
            'quantity' => $quantity,
        ]);
    }

    public function sourceBasedListAjax(Request $request)
    {
        // Guard: Return empty data if no date range is provided (prevent auto-load on page load)
        if (! $request->filled('start_date') && ! $request->filled('end_date')) {
            return response()->json([
                'draw'                 => (int) $request->input('draw'),
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        $columns = [
            'id',
            'invoice_no',
            'customer_name',
            'total',
            'paid_amount',
            'due_amount',
            'status',
            'payment_status',
            'created_at',
            'user_id',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $user     = Auth::user();
        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        $base = Sale::with(['customer', 'user', 'items'])
            ->where('status', 'delivered')
            ->select([
                'sales.id',
                'sales.invoice_no',
                'sales.customer_id',
                'sales.total',
                'sales.paid_amount',
                'sales.due_amount',
                'sales.status',
                'sales.payment_status',
                'sales.created_at',
                'sales.branch_id',
                'sales.user_id',
                'sales.warehouse_id',
            ]);

        // Apply branch filter (current user's branch if not super admin)
        if ($branchId) {
            $base->where('sales.branch_id', $branchId);
        }
        if ($request->sale_type) {
            $base->where('sales.sale_type', $request->sale_type);
        }
        if ($request->source) {
            $base->where('sales.source', $request->source);
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $base->where('sales.warehouse_id', $request->warehouse_id);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $base->whereBetween('sales.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $total = (clone $base)->count();

        // Search
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('sales.invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];

        foreach ($rows as $r) {
            $statusBadge = match ($r->status) {
                'hold'      => '<span class="badge text-sm fw-semibold bg-warning-600 px-20 py-9 radius-4 text-white">Hold</span>',
                'delivered' => '<span class="badge text-sm fw-semibold bg-success-600 px-20 py-9 radius-4 text-white">Delivered</span>',
                'void'      => '<span class="badge text-sm fw-semibold bg-danger-600 px-20 py-9 radius-4 text-white">Void</span>',
                default     => '<span class="badge text-sm fw-semibold bg-lilac-600 px-20 py-9 radius-4 text-white">' . e($r->status) . '</span>',
            };

            $payBadge = match ($r->payment_status) {
                'paid'  => '<span class="badge text-sm fw-semibold rounded-pill bg-success-600 px-20 py-9 radius-4 text-white">Paid</span>',
                'due'   => '<span class="badge text-sm fw-semibold rounded-pill bg-warning-600 px-20 py-9 radius-4 text-white">Due</span>',
                default => '<span class="badge text-sm fw-semibold rounded-pill bg-lilac-600 px-20 py-9 radius-4 text-white">' . e($r->payment_status) . '</span>',
            };

            $actions  = '<div class="d-inline-flex justify-content-end gap-1">';
            $actions .= '<a href="' . route('pos.sales.invoice', $r->id) . '" target="_blank" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-info-focus text-info-main" title="Invoice"><iconify-icon icon="mdi:printer-outline"></iconify-icon></a>';
            $actions .= '</div>';

            $totalQuantity = $r->items->map(fn($item) => $item->quantity)->sum();

            $data[] = [
                $r->id,
                '<strong>' . $r->invoice_no . '</strong>',
                e($r->customer?->name ?? 'Walk In'),
                number_format($r->total, 2),
                number_format($r->paid_amount, 2),
                number_format($r->due_amount, 2),
                $totalQuantity,
                $statusBadge,
                $payBadge,
                $r->created_at->format('Y-m-d H:i'),
                e($r->user?->name ?? '-'),
                // $actions,
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    public function productWiseIndex()
    {
        return view('backend.modules.reports.sales.product_wise');
    }

    public function productWiseSummary(Request $request)
    {
        $branchId = current_branch_id();
        $warehouseId = $request->warehouse_id;
        $productId = $request->product_id;
        $startDate = Carbon::parse($request->from_date)
            ->startOfDay()
            ->format('Y-m-d H:i:s');
        $endDate =  Carbon::parse($request->to_date)
            ->endOfDay()
            ->format('Y-m-d H:i:s');

        // return $endDate;

        // $saleSummary = DB::table('products')
        //     ->leftJoinSub(
        //         DB::table('sale_items')
        //             ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
        //             ->whereBetween('sales.created_at', [$startDate, $endDate])
        //             ->where('sales.branch_id', $branchId)
        //             ->where('sales.warehouse_id', $warehouseId)
        //             ->select(
        //                 'sale_items.product_id',
        //                 DB::raw('SUM(sale_items.quantity) as quantity'),
        //                 DB::raw('SUM(sales.total) as total')
        //             )
        //             ->groupBy('sale_items.product_id'),
        //         'sale_summary',
        //         function ($join) {
        //             $join->on('products.id', '=', 'sale_summary.product_id');
        //         }
        //     )
        //     ->where('products.parent_id', $productId)
        //     ->select(
        //         'products.id',
        //         DB::raw('COALESCE(sale_summary.quantity, 0) as total_quantity'),
        //         DB::raw('COALESCE(sale_summary.total, 0) as product_total_amount')
        //     )
        //     ->get();



        // $saleSummary = DB::table('sale_items')
        //     ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
        //     ->join('products', 'products.id', '=', 'sale_items.product_id')
        //     ->where('products.parent_id', $productId)
        //     ->whereBetween('sales.created_at', [$startDate, $endDate])
        //     ->where('sales.branch_id', $branchId)
        //     ->where('sales.warehouse_id', $warehouseId)
        //     ->select(
        //         'products.id',
        //         DB::raw('SUM(sale_items.quantity) as total_quantity'),
        //         DB::raw('SUM(sale_items.line_total) as product_total_amount')
        //     )
        //     ->groupBy('products.id')
        //     ->get();

        // $itemSummarySubquery = DB::table('sale_items')
        //     ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
        //     ->where('products.parent_id', $productId)
        //     ->whereBetween('sales.created_at', [$startDate, $endDate])
        //     ->where('sales.branch_id', $branchId)
        //     ->where('sales.warehouse_id', $warehouseId)
        //     ->select('sale_items.product_id', DB::raw("SUM(sale_items.quantity) as total_quantity"), DB::raw("SUM(sales.total) as total_sales"))
        //     ->groupBy( 'products.id');

        // $saleSummary = DB::table('products')->joinSub($itemSummarySubquery, 'items', function ($join) {
        //     $join->on('products.id', '=', 'items.product_id');
        // })
        // -select('product.id')
        // ->get(); 
        $saleTotalsSubquery = DB::table('sale_items')
            ->select('sale_id', DB::raw('SUM(line_total) as total_items_subtotal'))
            ->groupBy('sale_id');


        $saleSummary = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->joinSub($saleTotalsSubquery, 'sale_totals', function ($join) {
                $join->on('sales.id', '=', 'sale_totals.sale_id');
            })
            // ->where('has_variants', 0)
            ->where('products.parent_id', $productId)
            // ->orWhere('products.id', $productId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.branch_id', $branchId)
            ->where('sales.warehouse_id', $warehouseId)
            ->select(
                'products.id',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM((sale_items.line_total / sale_totals.total_items_subtotal) * sales.total) as product_total_amount')
            )
            ->groupBy('products.id')
            ->get();



        // return $saleSummary;

        $total_sales_amount = round($saleSummary->sum('product_total_amount'), 2);
        $total_quantity = round($saleSummary->sum('total_quantity'), 2);
        //  dd($total_sales_amount);

        $product = Product::where('id', $productId)->first();

        $discount = ((($product->mrp * $total_quantity) - $total_sales_amount) / ($product->mrp * $total_quantity)) * 100;





        return response()->json([
            'mrp' => round($product->mrp, 2),
            'cost'  => round($product->cost_price, 2),
            'discount'   => round($discount, 2) . '%',
            'profit' => round(($product->mrp - ($product->mrp  * $discount) / 100) - $product->cost_price, 2),
            'profit_percent' => round((($total_sales_amount / $total_quantity - $product->cost_price) / ($total_sales_amount / $total_quantity)) * 100, 2),
            'sale_value' => round($total_sales_amount / $total_quantity, 2),
        ]);
    }


    public function productWiseListAjax(Request $request)
    {
        // Guard: Return empty data if no date range is provided (prevent auto-load on page load)
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
            'invoice_no',
            'customer_name',
            'total',
            'paid_amount',
            'due_amount',
            'status',
            'payment_status',
            'created_at',
            'user_id',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $user     = Auth::user();
        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');
        // return $request->warehouse_id;

        $startDate = Carbon::parse($request->from_date)
            ->startOfDay()
            ->format('Y-m-d H:i:s');
        $endDate =  Carbon::parse($request->to_date)
            ->endOfDay()
            ->format('Y-m-d H:i:s');
        // dd($startDate,$endDate);
        $base = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.cost_price',
                'products.price',
                'sales_distinct.sale_type',
                'sales_distinct.branch_id',
                'sales_distinct.warehouse_id',
                'stock_currents.quantity as current_stock',
                'sales_distinct.total_sold',
                'sales_distinct.total_amount',
                'purchase_orders_distinct.purchased',


            )

            // Sales Summary
            ->leftJoinSub(
                DB::table('sale_items')
                    ->leftJoin('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->select(
                        'sale_items.product_id',
                        'sales.sale_type',
                        'sales.branch_id',
                        'sales.warehouse_id',
                        DB::raw('SUM(sale_items.quantity) as total_sold'),
                        DB::raw('SUM(sale_items.line_total) as total_amount')
                    )
                    ->where('sales.created_at', '>', $startDate)
                    ->where('sales.created_at', '<=', $endDate)
                    ->groupBy(
                        'sale_items.product_id',
                        'sales.sale_type',
                        'sales.branch_id',
                        'sales.warehouse_id',

                    ),
                'sales_distinct',
                function ($join) {
                    $join->on('sales_distinct.product_id', '=', 'products.id');
                }
            )

            // Purchase Summary
            ->leftJoinSub(
                DB::table('purchase_order_items')
                    ->leftJoin(
                        'purchase_orders',
                        'purchase_orders.id',
                        '=',
                        'purchase_order_items.purchase_order_id'
                    )
                    ->select(
                        'purchase_order_items.product_id',
                        'purchase_orders.branch_id',
                        'purchase_orders.warehouse_id',
                        DB::raw('SUM(purchase_order_items.quantity) as purchased')
                    )
                    ->groupBy(
                        'purchase_order_items.product_id',
                        'purchase_orders.branch_id',
                        'purchase_orders.warehouse_id'
                    ),
                'purchase_orders_distinct',
                function ($join) {
                    $join->on(
                        'purchase_orders_distinct.product_id',
                        '=',
                        'products.id'
                    )
                        ->on(
                            'purchase_orders_distinct.branch_id',
                            '=',
                            'sales_distinct.branch_id'
                        )
                        ->on(
                            'purchase_orders_distinct.warehouse_id',
                            '=',
                            'sales_distinct.warehouse_id'
                        );
                }
            )

            // Current Stock
            ->leftJoin('stock_currents', function ($join) {
                $join->on('stock_currents.product_id', '=', 'products.id')
                    ->on('stock_currents.branch_id', '=', 'sales_distinct.branch_id')
                    ->on('stock_currents.warehouse_id', '=', 'sales_distinct.warehouse_id');
            })
            // ->where('has_variants', 0)
            ->where('products.parent_id', $request->product_id);
            // ->orWhere('products.id',$request->product_id);
     

       

        // Apply branch filter (current user's branch if not super admin)
        if ($branchId) {
            $base->where('sales_distinct.branch_id', $branchId);
        }
        if ($request->sale_type) {
            $base->where('sales_distinct.sale_type', $request->sale_type);
        }
        // if ($request->source) {
        //     $base->where('sales.source', $request->source);
        // }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $base->where('sales_distinct.warehouse_id', $request->warehouse_id);
        }


        $total = (clone $base)->count();

        // Search
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('sales.invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();
        // return $rows;

        $data = [];

        foreach ($rows as $r) {

            $actions  = '<div class="d-inline-flex justify-content-end gap-1">';
            $actions .= '<a href="' . route('pos.sales.invoice', $r->id) . '" target="_blank" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-info-focus text-info-main" title="Invoice"><iconify-icon icon="mdi:printer-outline"></iconify-icon></a>';
            $actions .= '</div>';

            // $totalQuantity = $r->items->map(fn($item) => $item->quantity)->sum();

            $data[] = [
                $r->id,
                $r->name,
                // $r->price,
                $r->purchased,
                $r->current_stock,
                $r->cost_price * $r->current_stock,
                $r->total_sold,
                $r->total_amount,
                $r->total_amount - ($r->cost_price * $r->total_sold),

                // e($r->customer?->name ?? 'Walk In'),
                // number_format($r->total, 2),
                // number_format($r->paid_amount, 2),
                // number_format($r->due_amount, 2),
                // $totalQuantity,
                // $statusBadge,
                // $payBadge,
                // $r->created_at->format('Y-m-d H:i'),
                // e($r->user?->name ?? '-'),
                // $actions,
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
