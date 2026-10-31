<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Category;
use App\Models\backend\PaymentType;
use App\Models\backend\Product;
use App\Models\backend\PurchaseOrder;
use App\Models\backend\PurchaseReturn;
use App\Services\PurchaseReturnService;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        return view('backend.modules.purchase_returns.index');
    }

    public function listAjax(Request $request)
    {
        // columns must align with returned aaData order below
        $columns = [
            'id',
            'return_number',
            'supplier_name',
            'subtotal',
            'discount',
            'shipping_charge',
            'total_amount',
            'paid_amount',
            'due_amount',
            'created_at',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        // base query: eager load supplier & warehouse if needed
        $base = PurchaseReturn::with(['supplier'])->select([
            'purchase_returns.id',
            'purchase_returns.return_number',
            'purchase_returns.supplier_id',
            'purchase_returns.subtotal',
            'purchase_returns.discount',
            'purchase_returns.shipping_charge',
            'purchase_returns.total_amount',
            'purchase_returns.paid_amount',
            'purchase_returns.due_amount',
            'purchase_returns.created_at',
        ]);

        $total = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('purchase_returns.po_number', 'like', "%{$search}%")
                    ->orWhere('purchase_returns.notes', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        // map virtual column supplier_name sort -> supplier.name
        if ($orderCol === 'supplier_name') {
            $base->join('suppliers', 'suppliers.id', '=', 'purchase_returns.supplier_id')
                ->orderBy('suppliers.name', $orderDir)
                ->select('purchase_returns.*'); // ensure we still select PO columns
        } else {
            $base->orderBy($orderCol, $orderDir);
        }

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $poLink       = route('purchase.return.show', $r->id);
            $supplierName = $r->supplier ? e($r->supplier->name) : '-';

            // status badge
            $statusBadge = '<span class="border px-24 py-4 radius-4 fw-medium text-sm'
                . ($r->status === 'draft' ? 'border-warning-main bg-warning-focus text-warning-600' : ($r->status === 'cancelled' ? 'border-danger-main bg-danger-focus text-danger-600' : 'border-success-main bg-success-focus text-success-600'))
                . '">' . e(ucfirst($r->status)) . '</span>';

            // payment status badge
            $payBadgeClass = ($r->total_amount - $r->paid_amount == 0)
                ? 'badge text-sm fw-semibold rounded-pill bg-success-600 px-20 py-9 radius-4 text-white'
                : 'badge text-sm fw-semibold rounded-pill bg-info-600 px-20 py-9 radius-4 text-white';

            $paymentBadge  = '<span class="badge rounded-pill ' . $payBadgeClass . '">' . e(ucfirst(str_replace('_', ' ', $r->total_amount - $r->paid_amount == 0 ? 'paid' : 'partial'))) . '</span>';

            // invoice link / icon
            // $invoiceHtml = $r->purchase_invoice ? '<a href="' . asset($r->purchase_invoice) . '" target="_blank" title="Invoice" class="text-info-main"><iconify-icon icon="mdi:file-document-outline"></iconify-icon></a>' : '-';

            // actions (view/edit/delete) — update routes to your project
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
            <a href="' . $poLink . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-info-focus text-info-main " title="View"><iconify-icon icon="lucide:eye"></iconify-icon></a>


             <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-warning-focus text-warning-main  AjaxModal"    data-ajax-modal="' . route('purchase.return.payment.modal', $r->id) . '" title="Add Payment" data-onSuccess="purchasePayIndex.onSaved"> <iconify-icon icon="material-symbols:currency-exchange-rounded" class="text-lg"></iconify-icon></a>

            <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-focus text-danger-main btn-order-delete" data-id="' . $r->id . '" data-url="' . route('purchase.return.destroy', $r->id) . '" title="Delete"><iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon></a>

        </div>';

            $data[] = [
                $r->id,
                '<strong><a href="' . $poLink . '">' . e($r->return_number) . '</a></strong>',
                $supplierName,
                number_format((float) $r->subtotal, 2),
                number_format((float) $r->discount, 2),
                number_format((float) $r->shipping_charge, 2),
                number_format((float) $r->total_amount, 2),
                number_format((float) $r->paid_amount, 2),
                number_format((float) $r->due_amount, 2),

                $paymentBadge,
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

    public function show(PurchaseReturn $purchaseReturn)
    {
        $order = $purchaseReturn->load(['supplier', 'purchaseReturnPayment', 'items.product']);
        $paymentMethods = PaymentType::where('is_active', 1)->get();

        return view('backend.modules.purchase_returns.show', compact('order', 'paymentMethods'));
    }

    public function returnOrderDetails($order)
    {
        // Eager load relations
        $order = PurchaseOrder::with(['supplier', 'warehouse', 'branch', 'items.product', 'payments', 'receipts.items'])
            ->where('status', 'received')
            ->where('po_number', $order)->firstOrFail();

        if (!$order) {
            return response()->json(['error' => 'Order not found or not received yet'], 404);
        }
        return response()->json(['order' => $order]);
    }

    public function purchaseReturn()
    {
          $categories = Category::all();
          $paymentMethods = PaymentType::where('is_active', 1)->get();

        return view('backend.modules.purchase_returns.return_order', compact('categories', 'paymentMethods'));
    }

    public function returnPurchaseOrder(Request $request)
    {
        $payload = $request->payload;
        // dd($payload);
        return PurchaseReturnService::returnPurchaseOrder($payload);
    }

    public function paymentModal(PurchaseReturn $purchaseReturn)
    {   $paymentMethods = PaymentType::where('is_active', 1)->get();
        return view('backend.modules.purchase_returns.return_payment_modal', compact('purchaseReturn', 'paymentMethods'));
    }
    
    public function storePayment(Request $request, PurchaseReturn $purchaseReturn)
    {
        $payload = $request->validate([
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'method' => 'required|string|max:255',
            'reference'    => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        // Use a service to handle the payment logic
        return PurchaseReturnService::storePayment($purchaseReturn, $payload);
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        try {
            $purchaseReturn->delete();
            return response()->json(['success' => 'Purchase return deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete purchase return.'], 500);
        }
    }
}
