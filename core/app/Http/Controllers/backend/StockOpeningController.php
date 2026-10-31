<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\backend\StockLedger;
use App\Services\StockService;


class StockOpeningController extends Controller
{
    public function index()
    {
        return view('backend.modules.inventory.openingStock.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['txn_date','warehouse_name','product_name','quantity','unit_cost','created_by_name','note'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        // Base query: শুধুমাত্র Opening lines
        $base = StockLedger::query()
            ->from('stock_ledgers as sl')
            ->where('sl.ref_type', 'opening')
            ->leftJoin('products as p', 'p.id', '=', 'sl.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'sl.warehouse_id')
            ->leftJoin('users as u', 'u.id', '=', 'sl.created_by')
            ->selectRaw("
                sl.id,
                sl.txn_date,
                sl.quantity,
                sl.unit_cost,
                sl.note,
                p.id as product_id,
                p.name as product_name,
                p.sku  as product_sku,
                w.name as warehouse_name,
                u.name as created_by_name
            ");

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('p.name', 'like', "%{$searchVal}%")
                ->orWhere('p.sku', 'like', "%{$searchVal}%")
                ->orWhere('w.name', 'like', "%{$searchVal}%")
                ->orWhere('u.name', 'like', "%{$searchVal}%")
                ->orWhere('sl.note', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'txn_date';

        // map orderCol to actual column
        $orderMap = [
            'txn_date'         => 'sl.txn_date',
            'warehouse_name'   => 'w.name',
            'product_name'     => 'p.name',
            'quantity'         => 'sl.quantity',
            'unit_cost'        => 'sl.unit_cost',
            'created_by_name'  => 'u.name',
            'note'             => 'sl.note',
        ];
        $base->orderBy($orderMap[$orderCol] ?? 'sl.txn_date', $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $prodCol = '<div><strong>'.e($r->product_name ?? '—').'</strong><br><code>'.e($r->product_sku ?? '').'</code></div>';
            $whCol   = e($r->warehouse_name ?? '—');
            $qtyCol  = number_format((float)$r->quantity, 3);
            $costCol = is_null($r->unit_cost) ? '—' : number_format((float)$r->unit_cost, 2);
            $byCol   = e($r->created_by_name ?? '—');
            $dateCol = optional($r->txn_date)->format('Y-m-d H:i');

            // action (edit/delete future implementation)
                $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                  
                    data-size="md"
                    data-onload="OpeningIndex.onLoad"
                    data-onsuccess="OpeningIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-focus text-danger-main btn-stock-delete"
                   
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';
           

            $data[] = [
                '',
                $dateCol,
                $whCol,
                $prodCol,
                $qtyCol,
                $costCol,
                $byCol,
                e($r->note ?? '—'),
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
        // AjaxModal form: warehouse select2, product select2 (sellable only), qty, unit_cost
        return view('backend.modules.inventory.openingStock.modal');
    }

    public function store(Request $r, StockService $stock)
    {
        $data = $r->validate([
            'warehouse_id'      => 'nullable|exists:warehouses,id',
            'rows'              => 'required|array|min:1',
            'rows.*.product_id' => 'required|exists:products,id',
            'rows.*.qty'        => 'required|numeric|min:0.001',
            'rows.*.unit_cost'  => 'nullable|numeric|min:0',
        ]);

        foreach ($data['rows'] as $row) {
            $stock->apply(
                productId: (int) $row['product_id'],
                warehouseId: $data['warehouse_id'] ?? null,
                direction: 'IN',
                qty: (float) $row['qty'],
                unitCost: isset($row['unit_cost']) ? (float) $row['unit_cost'] : null,
                ref: ['type' => 'OPENING_STOCK', 'id' => null],
                userId: auth()->id(),
                note: 'Opening stock',
            );
        }

        return response()->json(['success' => true, 'msg' => 'Opening stock applied']);
    }
}