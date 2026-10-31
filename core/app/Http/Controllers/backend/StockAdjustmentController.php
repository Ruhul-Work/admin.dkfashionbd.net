<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\backend\StockAdjustment;
use App\Models\backend\StockAdjustmentItem;
use App\Models\backend\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        return view('backend.modules.inventory.stockAdjustment.index');
    }

    
    public function listAjax(Request $request)
    {
        $draw        = (int) $request->input('draw', 1);
        $start       = (int) $request->input('start', 0);
        $length      = (int) $request->input('length', 10);
        $orderColIdx = (int) $request->input('order.0.column', 2); // default order on date
        $orderDir    = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search      = trim($request->input('search.value', ''));

        $base = StockAdjustment::query()
            ->select(
                'stock_adjustments.*',
                DB::raw('COALESCE(agg.items_count, 0) as items_count'),
                DB::raw('COALESCE(agg.sum_in, 0) as sum_in'),
                DB::raw('COALESCE(agg.sum_out, 0) as sum_out')
            )
            ->leftJoin(DB::raw('(
        SELECT adjustment_id,
               COUNT(*) AS items_count,
               SUM(CASE WHEN direction = "IN"  THEN quantity ELSE 0 END) AS sum_in,
               SUM(CASE WHEN direction = "OUT" THEN quantity ELSE 0 END) AS sum_out
        FROM stock_adjustment_items
        GROUP BY adjustment_id
    ) agg'), 'agg.adjustment_id', '=', 'stock_adjustments.id');

        // filters (optional request params)
        if ($request->filled('warehouse_id')) {
            $base->where('stock_adjustments.warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('branch_id')) {
            $base->where('stock_adjustments.branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $base->where('stock_adjustments.status', $request->status);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->from . ' 00:00:00';
            $to   = $request->to . ' 23:59:59';
            $base->whereBetween('stock_adjustments.adjust_date', [$from, $to]);
        }
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('stock_adjustments.reference_no', 'like', "%{$search}%")
                    ->orWhere('stock_adjustments.note', 'like', "%{$search}%");
            });
        }

        // total counts before pagination
        $total = (clone $base)->count();

        // ordering map: column index (DataTables) -> DB column/subselect
        // we will match this map with the JS columns order below
        $orderMap = [
            0 => 'stock_adjustments.id', // SL (we'll order by id or date)
            1 => 'stock_adjustments.reference_no',
            2 => 'stock_adjustments.adjust_date',
            3 => 'stock_adjustments.warehouse_id',
            4 => 'stock_adjustments.branch_id',
            5 => 'agg.items_count',
            6 => 'agg.sum_in', // could be sum_in or net
            7 => 'stock_adjustments.status',
            8 => 'stock_adjustments.created_by',
            9 => 'stock_adjustments.note',
        ];

        $orderCol = $orderMap[$orderColIdx] ?? 'stock_adjustments.adjust_date';
        $rows     = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)
            ->get();

        // eager load related names for warehouse/branch/creator to avoid N+1
        $rows->loadMissing(['warehouse:id,name', 'branch:id,name', 'creator:id,name']);

        // build aaData
        $data = [];
        $sl   = $start + 1;
        foreach ($rows as $r) {
            $ref        = e($r->reference_no ?: ('#' . $r->id));
            $date       = optional($r->adjust_date)->format('Y-m-d H:i') ?? optional($r->created_at)->format('Y-m-d H:i');
            $wh         = optional($r->warehouse)->name ?? '—';
            $br         = optional($r->branch)->name ?? '—';
            $itemsCount = (int) ($r->items_count ?? 0);
            $sumIn      = number_format((float) ($r->sum_in ?? 0), 3);
            $sumOut     = number_format((float) ($r->sum_out ?? 0), 3);
            $qtyDisplay = '+' . $sumIn . ' / -' . $sumOut;

            // status badge
            $status      = strtoupper($r->status ?? 'DRAFT');
            $statusBadge = '<span class=" border px-24 py-4 radius-4 fw-medium text-sm ' .
            ($status === 'POSTED' ? 'border-success-main bg-success-focus text-success-600' : ($status === 'CANCELLED' ? 'border-danger-main bg-danger-focus text-danger-600' : 'border-warning-main bg-warning-focus text-warning-600')) .
            '">' . e($status) . '</span>';

            // created by
            $createdBy = optional($r->creator)->name ?? ($r->created_by ?? '—');

            // truncate note
            $noteShort = $r->note ? e(\Illuminate\Support\Str::limit($r->note, 50)) : '—';

            // actions — adjust routes/perm checks as you need
            $viewUrl   = route('inventory.adjustments.show', $r->id);
            $editUrl   = route('inventory.adjustments.edit', $r->id); // or dedicated edit route for header
            $postUrl   = route('inventory.adjustments.post', $r->id); // ensure route exists
            $deleteUrl = route('inventory.adjustments.destroy', $r->id);

            $actions = '<div class="d-inline-flex gap-1">';
            $actions .= ' <a href="' . $viewUrl . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                                bg-info-focus text-info-main" title="view">
                                <iconify-icon icon="material-symbols:undereye-outline" class="text-lg"></iconify-icon>
                            </a>';
            // only show edit if draft
            if ($status === 'DRAFT') {
                $actions .= ' <a href="' . $editUrl . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                                bg-success-focus text-success-main" title="Edit">
                                <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                            </a>';
                $actions .= ' <a href="#" data-url="' . $postUrl . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                                bg-success-focus text-success-main btn-adjust-post" title="post">
                                <iconify-icon icon="solar:check-circle-outline" class="text-xl"></iconify-icon>
                            </a>';
                $actions .= ' <a href="#" data-url="' . $deleteUrl . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                             bg-danger-focus text-danger-main btn-adjust-delete" title="delete">
                                 <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon>
                            </a>';

            } else {
                // if posted show cancel
                if ($status === 'POSTED') {
                    $cancelUrl = route('inventory.adjustments.cancel', $r->id); // create route if needed
                    $actions .= ' <a href="#" data-url="' . $cancelUrl . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                                bg-warning-focus text-warning-main btn-adjust-cancel" title="cancel">
                                <iconify-icon icon="material-symbols:cancel" class="text-lg"></iconify-icon>
                            </a>';
                }
            }
            $actions .= '</div>';

            $data[] = [
                $sl++,
                $ref,
                $date,
                e($wh),
                e($br),
                $itemsCount,
                $qtyDisplay,
                $statusBadge,
                e($createdBy),
                $noteShort,
                $actions,
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $total,
            'aaData'               => $data,
        ]);
    }

    public function create()
    {
        return view('backend.modules.inventory.stockAdjustment.create');
    }

    // Store as DRAFT (or optionally POST immediately based on input)
    public function store(StockAdjustmentRequest $request)
    {
        $data   = $request->validated();
        $userId = Auth::id();

        // Build header and items from validated payload
        $header = [
            'reference_no' => $data['reference_no'] ?? null,
            'branch_id'    => $data['branch_id'] ?? 0,
            'warehouse_id' => $data['warehouse_id'],
            'adjust_date'  => $data['when'] ?? now(),
            'reason_code'  => $data['global_reason'] ?? null,
            'note'         => $data['note'] ?? null,
        ];

        $items = [];
        foreach ($data['rows'] as $row) {
            // server derive direction from qty sign if not passed explicitly
            $qty       = (float) $row['qty'];
            $direction = $row['direction'] ?? ($qty >= 0 ? 'IN' : 'OUT');
            $items[]   = [
                'product_id'   => (int) $row['product_id'],
                'warehouse_id' => $row['warehouse_id'] ?? $header['warehouse_id'],
                'branch_id'    => $row['branch_id'] ?? $header['branch_id'],
                'direction'    => $direction,
                'quantity'     => abs($qty),
                'unit_cost'    => isset($row['unit_cost']) && $row['unit_cost'] !== '' ? (float) $row['unit_cost'] : null,
                'note'         => $row['reason'] ?? null,
            ];
        }

        try {
            $adjustId = $this->stockService->createDraft($header, $items, $userId);

            // If front wants immediate post, we can check a flag (post_now)
            if (! empty($data['post_now']) && $data['post_now']) {
                $this->stockService->postAdjustment($adjustId, $userId);
                $status = 'posted';
            } else {
                $status = 'draft';
            }

            return response()->json([
                'ok'            => true,
                'msg'           => 'Adjustment saved',
                'status'        => $status,
                'adjustment_id' => $adjustId,
            ]);
        } catch (\Exception $e) {
            // log error server-side for debugging
            \Log::error('StockAdjustment::store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function show(StockAdjustment $adjustment)
    {
        $adjustment->loadMissing(['items.product', 'warehouse', 'branch', 'creator', 'ledgerEntries.product']);
        return view('backend.modules.inventory.stockAdjustment.show', compact('adjustment'));
    }

// POST /adjustments/{id}/post
    public function post(Request $request, $id)
    {
        $userId = auth()->id();
        try {
            // call service (should perform transaction & update ledgers & currents)
            $res = $this->stockService->postAdjustment((int) $id, $userId);

            return response()->json([
                'ok'   => true,
                'msg'  => 'Adjustment posted',
                'data' => $res,
            ]);
        } catch (\Exception $e) {
            Log::error("postAdjustment failed: {$e->getMessage()}", ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'msg' => 'Post failed: ' . $e->getMessage()], 400);
        }
    }

// POST /adjustments/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $userId = auth()->id();
        try {
            $res = $this->stockService->cancelAdjustment((int) $id, $userId); // implement in service
            return response()->json(['ok' => true, 'msg' => 'Adjustment cancelled', 'data' => $res]);
        } catch (\Exception $e) {
            Log::error("cancelAdjustment failed: {$e->getMessage()}", ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'msg' => 'Cancel failed: ' . $e->getMessage()], 400);
        }
    }

    //   // POST /adjustments/{id}/post
    // public function post(Request $request, $id)
    // {
    //     $userId = Auth::id();
    //     try {
    //         $res = $this->stockService->postAdjustment((int)$id, $userId);
    //         return response()->json(['ok'=>true,'msg'=>'Posted','data'=>$res]);
    //     } catch (\Exception $e) {
    //         \Log::error('StockAdjustment::post error: '.$e->getMessage());
    //         return response()->json(['ok'=>false,'msg'=>$e->getMessage()], 400);
    //     }
    // }

    // // POST /adjustments/{id}/cancel
    // public function cancel(Request $request, $id)
    // {
    //     $userId = Auth::id();
    //     try {
    //         $res = $this->stockService->cancelAdjustment((int)$id, $userId);
    //         return response()->json(['ok'=>true,'msg'=>'Cancelled','data'=>$res]);
    //     } catch (\Exception $e) {
    //         \Log::error('StockAdjustment::cancel error: '.$e->getMessage());
    //         return response()->json(['ok'=>false,'msg'=>$e->getMessage()], 400);
    //     }
    // }

    // AJAX: variants for parent (used in existing JS fetchVariants)
    // public function ajaxParentVariants($parentId, Request $request)
    // {
    //     // returns variants with system_qty for selected warehouse
    //     $warehouseId = $request->query('warehouse_id') ?? $request->input('warehouse_id') ?? null;
    //     $branchId = $request->query('branch_id') ?? $request->input('branch_id') ?? 0;

    //     // fetch variants (implement according to your product model, sample below)
    //     $variants = \DB::table('products')->where('parent_id', $parentId)->get(); // adapt to your schema

    //     $results = $variants->map(function($v) use ($warehouseId, $branchId) {
    //         $systemQty = 0;
    //         if ($warehouseId) {
    //             $systemQty = $this->stockService->getSystemQuantity($v->id, (int)$warehouseId, (int)$branchId);
    //         }
    //         return [
    //             'id' => $v->id,
    //             'name' => $v->english_name ?? $v->name ?? 'Variant',
    //             'sku' => $v->sku ?? '',
    //             'image' => $v->image ?? null,
    //             'default_unit_cost' => $v->cost_price ?? 0,
    //             'system_qty' => $systemQty,
    //         ];
    //     });

    //     return response()->json(['data' => $results]);
    // }

    // Bulk system qty endpoint (POST) - accepts array of product_ids
    public function systemQtyBulk(Request $request)
    {
        $productIds  = $request->input('product_ids', []);
        $warehouseId = $request->input('warehouse_id');
        $branchId    = $request->input('branch_id', 0);

        $result = [];
        foreach ($productIds as $pid) {
            $result[$pid] = $this->stockService->getSystemQuantity((int) $pid, (int) $warehouseId, (int) $branchId);
        }

        return response()->json(['data' => $result]);
    }

    public function edit($id)
    {
        $adjustment = StockAdjustment::with(['items.product', 'warehouse', 'branch'])->findOrFail($id);

        // only drafts editable
        if (strtoupper($adjustment->status) !== 'DRAFT') {
            return redirect()->route('inventory.adjustments.index')
                ->with('error', 'Only DRAFT adjustments can be edited.');
        }

        // prepare data similar to create page: variants rows are in $adjustment->items
        // load warehouses & user branch etc if needed
        $warehouses = Warehouse::select('id', 'name')->get();

        return view('backend.modules.inventory.stockAdjustment.edit', compact('adjustment', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        $adjustment = StockAdjustment::with('items')->findOrFail($id);

        if (strtoupper($adjustment->status) !== 'DRAFT') {
            return response()->json(['ok' => false, 'msg' => 'Only DRAFT adjustments can be updated.'], 400);
        }

        $data = $request->validate([
            'warehouse_id'      => ['required', 'integer'],
            'branch_id'         => ['nullable', 'integer'],
            'when'              => ['nullable', 'date'],
            'global_reason'     => ['nullable', 'string'],
            'rows'              => ['required', 'array', 'min:1'],
            'rows.*.product_id' => ['required', 'integer'],
            'rows.*.qty'        => ['required', 'numeric'],
            'rows.*.unit_cost'  => ['nullable', 'numeric'],
            'post_now'          => ['nullable', 'in:0,1'],
        ]);

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            // update header
            $adjustment->update([
                'warehouse_id' => $data['warehouse_id'],
                'branch_id'    => $data['branch_id'] ?? $adjustment->branch_id,
                'adjust_date'  => $data['when'] ?? $adjustment->adjust_date,
                'reason_code'  => $data['global_reason'] ?? $adjustment->reason_code,
                'note'         => $data['global_reason'] ?? $adjustment->note,
                'created_by'   => $adjustment->created_by ?? $userId,
            ]);

            // Simplest safe approach: delete previous items and re-insert new lines
            StockAdjustmentItem::where('adjustment_id', $adjustment->id)->delete();

            $items = [];
            foreach ($data['rows'] as $row) {
                $qty       = (float) $row['qty'];
                $direction = $qty >= 0 ? 'IN' : 'OUT';
                $items[]   = [
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => (int) $row['product_id'],
                    'warehouse_id'  => $data['warehouse_id'],
                    'branch_id'     => $data['branch_id'] ?? $adjustment->branch_id,
                    'direction'     => $direction,
                    'quantity'      => abs($qty),
                    'unit_cost'     => isset($row['unit_cost']) && $row['unit_cost'] !== '' ? (float) $row['unit_cost'] : null,
                    'note'          => $row['reason'] ?? null,
                    'created_by'    => $userId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            // bulk insert
            StockAdjustmentItem::insert($items);

            DB::commit();

            // if post requested now call service
            if (! empty($data['post_now']) && $data['post_now'] == 1) {
                $this->stockService->postAdjustment($adjustment->id, $userId);
                return response()->json(['ok' => true, 'msg' => 'Updated and posted', 'status' => 'posted']);
            }

            return response()->json(['ok' => true, 'msg' => 'Adjustment updated', 'status' => 'draft']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('updateAdjustment error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'msg' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        if (strtoupper($adjustment->status) !== 'DRAFT') {
            return response()->json(['ok' => false, 'msg' => 'Only DRAFT adjustments can be deleted.'], 400);
        }

        DB::beginTransaction();
        try {
            // delete items then header
            \DB::table('stock_adjustment_items')->where('adjustment_id', $adjustment->id)->delete();
            $adjustment->delete();
            DB::commit();
            return response()->json(['ok' => true, 'msg' => 'Deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('destroyAdjustment error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'msg' => 'Delete failed: ' . $e->getMessage()], 500);
        }
    }

}
