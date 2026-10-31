<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\BranchAccount;
use App\Models\backend\Category;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\Product;
use App\Models\backend\PurchaseOrder;
use App\Models\backend\PurchaseOrderItem;
use App\Models\backend\PurchasePayment;
use App\Models\backend\PurchaseReturn;
use App\Models\backend\Supplier;
use App\Models\backend\SupplierLedger;
use App\Models\backend\VoucherType;
use App\Services\PurchaseReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as Validator;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('backend.modules.purchase.list');
    }

    public function listAjax(Request $request)
    {
        // columns must align with returned aaData order below
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

        // base query: eager load supplier & warehouse if needed
        $base = PurchaseOrder::with(['supplier'])->select([
            'purchase_orders.id',
            'purchase_orders.po_number',
            'purchase_orders.supplier_id',
            'purchase_orders.total_amount',
            'purchase_orders.paid_amount',
            'purchase_orders.outstanding_amount',
            'purchase_orders.status',
            'purchase_orders.payment_status',
            'purchase_orders.purchase_invoice',
            'purchase_orders.created_at',
        ]);

        $total = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('purchase_orders.po_number', 'like', "%{$search}%")
                    ->orWhere('purchase_orders.notes', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        // map virtual column supplier_name sort -> supplier.name
        if ($orderCol === 'supplier_name') {
            $base->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                ->orderBy('suppliers.name', $orderDir)
                ->select('purchase_orders.*'); // ensure we still select PO columns
        } else {
            $base->orderBy($orderCol, $orderDir);
        }

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $poLink       = route('purchase.orders.show', $r->id);
            $supplierName = $r->supplier ? e($r->supplier->name) : '-';

            // status badge
            $statusBadge = '<span class="border px-24 py-4 radius-4 fw-medium text-sm'
                . ($r->status === 'draft' ? 'border-warning-main bg-warning-focus text-warning-600' : ($r->status === 'cancelled' ? 'border-danger-main bg-danger-focus text-danger-600' : 'border-success-main bg-success-focus text-success-600'))
                . '">' . e(ucfirst($r->status)) . '</span>';

            // payment status badge
            $payBadgeClass = $r->payment_status === 'paid' ? 'badge text-sm fw-semibold rounded-pill bg-success-600 px-20 py-9 radius-4 text-white' : ($r->payment_status === 'partially_paid' ? 'badge text-sm fw-semibold rounded-pill bg-warning-600 px-20 py-9 radius-4 text-white' : 'badge text-sm fw-semibold rounded-pill bg-lilac-600 px-20 py-9 radius-4 text-white');
            $paymentBadge  = '<span class="badge rounded-pill ' . $payBadgeClass . '">' . e(ucfirst(str_replace('_', ' ', $r->payment_status))) . '</span>';

            // invoice link / icon
            $invoiceHtml = $r->purchase_invoice ? '<a href="' . asset($r->purchase_invoice) . '" target="_blank" title="Invoice" class="text-info-main"><iconify-icon icon="mdi:file-document-outline"></iconify-icon></a>' : '-';

            // actions (view/edit/delete) — update routes to your project
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
            <a href="' . $poLink . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-info-focus text-info-main " title="View"><iconify-icon icon="lucide:eye"></iconify-icon></a>
            <a href="' . route('purchase.orders.edit', $r->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main " title="Edit"><iconify-icon icon="lucide:edit"></iconify-icon></a>

             <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-warning-focus text-warning-main  AjaxModal"    data-ajax-modal="' . route('purchase.orders.payment.modal', $r->id) . '" title="Add Payment" data-onSuccess="purchasePayIndex.onSaved"> <iconify-icon icon="material-symbols:currency-exchange-rounded" class="text-lg"></iconify-icon></a>

            <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-focus text-danger-main btn-order-delete" data-id="' . $r->id . '" data-url="' . route('purchase.orders.destroy', $r->id) . '" title="Delete"><iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon></a>

        </div>';

            $data[] = [
                $r->id,
                '<strong><a href="' . $poLink . '">' . e($r->po_number) . '</a></strong>',
                $supplierName,
                number_format((float) $r->total_amount, 2),
                number_format((float) $r->paid_amount, 2),
                number_format((float) $r->outstanding_amount, 2),
                $statusBadge,
                $paymentBadge,
                $r->created_at->format('Y-m-d'),
                $invoiceHtml,
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

    public function create()
    {
        $categories = Category::all();
        $products   = Product::with(['category'])
            ->where('parent_id', null)
            ->paginate(6);


        return view('backend.modules.purchase.index', compact('categories', 'products'));
    }

    public function paymentModal(PurchaseOrder $order)
    {
        return view('backend.modules.purchase.payment_modal', compact('order'));
    }

    public function store(Request $request, PurchaseOrder $purchaseorder)
    {
        // Basic guard: payload must exist
        $payloadJson = $request->input('payload');
        if (! $payloadJson) {
            return response()->json(['success' => false, 'message' => 'Missing payload'], 422);
        }

        $payload = json_decode($payloadJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid payload JSON'], 422);
        }

        // Validate top-level fields
        $validator = Validator::make($payload, [
            'supplier_id'        => 'required|exists:suppliers,id',
            'warehouse_id'       => 'nullable|exists:warehouses,id',
            'order_date'         => 'nullable|date',
            'reference'          => 'nullable|string|max:150',
            'shipping_amount'    => 'nullable|numeric|min:0',
            'discount'           => 'nullable|array',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_cost'  => 'required|numeric|min:0',
            'items.*.quantity'   => 'required|numeric|min:0.001',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // extra validation/normalization
        $supplier = Supplier::find($payload['supplier_id']);
        if (! $supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found'], 422);
        }

        $cashAccountId = BranchAccount::where('branch_id', $payload['branch_id'])
            ->where('is_active', 1)
            ->value('account_id');

        if (! $cashAccountId) {
            return response()->json(['success' => false, 'message' => 'Cash Account not found'], 422);
        }

        // Compute totals server-side
        $subtotal = 0.0;
        foreach ($payload['items'] as $i) {
            $line = (float) $i['unit_cost'] * (float) $i['quantity'];
            $subtotal += $line;
        }

        $shipping       = isset($payload['shipping_amount']) ? (float) $payload['shipping_amount'] : 0.0;
        $discountAmount = 0.0;
        if (! empty($payload['discount']) && isset($payload['discount']['value'])) {
            $d = (float) $payload['discount']['value'];
            if (($payload['discount']['type'] ?? 'flat') === 'percentage') {
                $discountAmount = ($subtotal * $d) / 100.0;
            } else {
                $discountAmount = $d;
            }
        }

        $total = $subtotal + $shipping - $discountAmount;
        if ($total < 0) {
            $total = 0;
        }

        // normalize discount user input
        $discountType   = $payload['discount']['type'] ?? 'flat';
        $discountValue  = isset($payload['discount']['value']) ? (float) $payload['discount']['value'] : 0.0;
        $discountAmount = ($discountType === 'percentage') ? ($subtotal * $discountValue / 100.0) : $discountValue;
        $discountAmount = round($discountAmount, 2);

        // recompute total using discountAmount (safer)
        $total = round(max(0, $subtotal + $shipping - $discountAmount), 2);

        // file separately handled
        $invoicePath = null;

        if ($request->hasFile('purchase_invoice')) {
            $invoicePath = uploadImage($request->file('purchase_invoice'), 'purchase/images');
        }

        // Transaction: create order + items
        DB::beginTransaction();
        try {
            // create unique PO number (simple example: PO-YYYYmmdd-UNIX)
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . substr(uniqid(), -6);

            $order = PurchaseOrder::create([
                'supplier_id'        => $payload['supplier_id'],
                'branch_id'          => $payload['branch_id'] ?? null,
                'warehouse_id'       => $payload['warehouse_id'] ?? null,
                'po_number'          => $poNumber,
                'status'             => $payload['status'] ?? 'draft',
                'order_date'         => $payload['order_date'] ?? now()->toDateString(),
                'expected_date'      => $payload['expected_date'] ?? null,
                'currency'           => $payload['currency'] ?? 'BDT',
                'subtotal'           => round($subtotal, 2),
                'tax_amount'         => 0.00,
                'shipping_amount'    => round($shipping, 2),
                // NEW discount fields
                'discount_type'      => $discountType,
                'discount_value'     => round($discountValue, 2),
                'discount_amount'    => $discountAmount,
                'total_amount'       => $total,
                // payment summary initial values
                'paid_amount'        => 0.00,
                'outstanding_amount' => $total,
                'payment_status'     => 'unpaid',
                'notes'              => $payload['reference'] ?? null,
                'purchase_invoice'   => $invoicePath ?? null,
                'created_by'         => auth()->id(),
            ]);

            // create items (unchanged)
            foreach ($payload['items'] as $i) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $i['product_id'],
                    'sku'               => $i['sku'] ?? null,
                    'description'       => $i['description'] ?? null,
                    'unit_cost'         => round((float) $i['unit_cost'], 2),
                    'quantity'          => (int) $i['quantity'],
                    'received_quantity' => 0,
                    'line_total'        => round((float) $i['unit_cost'] * (int) $i['quantity'], 2),
                ]);
            }



            // handle initial payment if provided
            if (! empty($payload['payment']) && ! empty($payload['payment']['amount']) && (float) $payload['payment']['amount'] > 0) {
                $payData   = $payload['payment'];
                $payAmount = round((float) $payData['amount'], 2);

                // validate not paying more than outstanding (recommended)
                if ($payAmount > $order->outstanding_amount) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Payment exceeds order outstanding amount'], 422);
                }

                $payment = PurchasePayment::create([
                    'supplier_id'         => $order->supplier_id,
                    'branch_id'          => $payload['branch_id'] ?? null,
                    'purchase_order_id'   => $order->id,
                    'purchase_receipt_id' => null,
                    'payment_date'        => $payData['payment_date'] ?? now(),
                    'amount'              => $payAmount,
                    'method'              => $payData['method'] ?? null,
                    'reference'           => $payData['reference'] ?? null,
                    'notes'               => $payData['notes'] ?? null,
                    'created_by'          => auth()->id(),
                ]);


                $journal = JournalEntry::create([
                    'voucher_no'      => generateVoucherNo('PURCHASE'),
                    'voucher_type_id' => VoucherType::idByCode('PURCHASE'),
                    'branch_id'       => $payload['branch_id'] ?? null,
                    'fiscal_year_id'  => currentFiscalYear()->id,
                    'source_id'       => $order->id,
                    'entry_date'      => now()->toDateString(),
                    'narration'       => 'Purchase#' . $order->po_number,
                    'created_by'      => auth()->id(),
                ]);

                // JournalEntryLine::create([
                //     'journal_entry_id' => $journal->id,
                //     'account_id'       => $cashAccountId,
                //     'branch_id'        => $payload['branch_id'] ?? null,
                //     'debit'            => $payAmount,
                //     'credit'           => 0,
                // ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id'       => $cashAccountId,
                    'branch_id'        => $payload['branch_id'] ?? null,
                    'debit'            => 0,
                    'credit'           => $payAmount,
                ]);



                // ledger entry (example)
                $last  =  SupplierLedger::where('supplier_id', $order->supplier_id)
                    ->where('branch_id', $payload['branch_id'] ?? $order->branch_id)
                    ->orderBy('id', 'desc')
                    ->first();

                $prev_balance = $last ? (float) $last->balance_after : 0.00;
                SupplierLedger::create([
                    'supplier_id'    => $order->supplier_id,
                    'branch_id'  => $payload['branch_id'],
                    'reference_type' => 'purchase_payment',
                    'reference_id'   => $payment->id,
                    'txn_date'       => now()->toDateString(),
                    'description'    => 'Payment for PO ' . $order->po_number,
                    'debit'          => 0,
                    'credit'         => $payment->amount,
                    'balance_after'  => $prev_balance - $payment->amount,
                ]);
            }

            // recompute payments summary AFTER any payment created
            $paid                      = $order->payments()->sum('amount'); // sum from DB (safe)
            $order->paid_amount        = round($paid, 2);
            $order->outstanding_amount = max(0, round($order->total_amount - $order->paid_amount, 2));
            $order->payment_status     = $order->paid_amount <= 0 ? 'unpaid' : ($order->outstanding_amount <= 0 ? 'paid' : 'partially_paid');
            $order->save();

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Purchase order created',
                'order_id'     => $order->id,
                'redirect_url' => route('purchase.orders.show', $order->id),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($invoicePath) {
                Storage::disk('public')->delete($invoicePath);
            }

            \Log::error('PurchaseOrder store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create purchase order', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(PurchaseOrder $order)
    {
        // eager load relations
        $order->load(['supplier', 'warehouse', 'branch', 'items.product', 'payments', 'receipts.items']);

        // compute payments sum / outstanding
        $paid        = $order->payments()->sum('amount');
        $outstanding = round($order->total_amount - $paid, 2);

        return view('backend.modules.purchase.show', compact('order', 'paid', 'outstanding'));
    }

    public function edit(PurchaseOrder $purchase)
    {
        // eager load relations required by the view
        $purchase->load([
            'items.product',
            'supplier',
            'warehouse',
            'branch',
            'payments',

        ]);

        // If you want to disallow full edit on received orders, pass a flag
        $isEditable = $purchase->status === 'draft';

        $categories = Category::all(); // example, if your create uses categories

        return view('backend.modules.purchase.index', [
            'purchase'   => $purchase,
            'isEditable' => $isEditable,
            'categories' => $categories,
            // add other data needed by create view
        ]);
    }

    //     public function update(Request $request, PurchaseOrder $purchase)
    // {
    //     // Authorization check (optional)
    //     // $this->authorize('update', $purchase);

    //     // If purchase already received, disallow full edit (rule you described)
    //     if ($purchase->status === 'received') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Received orders cannot be edited.'
    //         ], 403);
    //     }

    //     // Expect payload either as JSON in 'payload' or direct form fields (same as store)
    //     $payloadJson = $request->input('payload');
    //     $payload = null;
    //     if ($payloadJson) {
    //         $payload = json_decode($payloadJson, true);
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             return response()->json(['success' => false, 'message' => 'Invalid payload JSON'], 422);
    //         }
    //     } else {
    //         // Normalize direct fields (compatible with your create form)
    //         $payload = [
    //             'supplier_id'     => $request->input('supplier_id') ?? $purchase->supplier_id,
    //             'warehouse_id'    => $request->input('warehouse_id') ?? $purchase->warehouse_id,
    //             'branch_id'       => $request->input('branch_id') ?? $purchase->branch_id,
    //             'status'          => $request->input('status') ?? $purchase->status,
    //             'order_date'      => $request->input('order_date') ?? $purchase->order_date,
    //             'expected_date'   => $request->input('expected_date') ?? $purchase->expected_date,
    //             'reference'       => $request->input('reference') ?? $purchase->reference,
    //             'currency'        => $request->input('currency') ?? $purchase->currency,
    //             'shipping_amount' => $request->input('shipping_amount') ?? $purchase->shipping_amount,
    //             'discount'        => $request->input('discount') ?? null,
    //             'items'           => $request->input('items') ?? [],
    //         ];
    //     }

    //     // Validate top-level (adjust rules per your app)
    //     $validator = Validator::make($payload, [
    //         'supplier_id' => 'required|exists:suppliers,id',
    //         'warehouse_id' => 'nullable|exists:warehouses,id',
    //         'branch_id' => 'nullable|exists:branches,id',
    //         'order_date' => 'nullable|date',
    //         'shipping_amount' => 'nullable|numeric|min:0',
    //         'items' => 'required|array|min:1',
    //         'items.*.product_id' => 'required|exists:products,id',
    //         'items.*.unit_cost' => 'required|numeric|min:0',
    //         'items.*.quantity' => 'required|numeric|min:0.001',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     // Recompute totals server-side (same as store)
    //     $subtotal = 0.0;
    //     foreach ($payload['items'] as $i) {
    //         $line = (float)$i['unit_cost'] * (float)$i['quantity'];
    //         $subtotal += $line;
    //     }

    //     $shipping = isset($payload['shipping_amount']) ? (float)$payload['shipping_amount'] : 0.0;
    //     $discountAmount = 0.0;
    //     if (! empty($payload['discount']) && isset($payload['discount']['value'])) {
    //         $d = (float) $payload['discount']['value'];
    //         if (($payload['discount']['type'] ?? 'flat') === 'percentage') {
    //             $discountAmount = ($subtotal * $d) / 100.0;
    //         } else {
    //             $discountAmount = $d;
    //         }
    //     }

    //     $total = $subtotal + $shipping - $discountAmount;
    //     if ($total < 0) $total = 0;

    //     // File handling: if new invoice uploaded, store and replace old
    //     $invoicePath = $purchase->purchase_invoice;
    //     if ($request->hasFile('purchase_invoice')) {
    //         $newPath = uploadImage($request->file('purchase_invoice'), 'purchase/images');
    //         if ($newPath) {
    //             // delete previous file (if any)
    //             if ($purchase->purchase_invoice) {
    //                 Storage::disk('public')->delete($purchase->purchase_invoice);
    //             }
    //             $invoicePath = $newPath;
    //         }
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // Update purchase order main fields
    //         $purchase->supplier_id = $payload['supplier_id'];
    //         $purchase->warehouse_id = $payload['warehouse_id'] ?? null;
    //         $purchase->branch_id = $payload['branch_id'] ?? null;
    //         $purchase->status = $payload['status'] ?? $purchase->status;
    //         $purchase->order_date = $payload['order_date'] ?? $purchase->order_date;
    //         $purchase->expected_date = $payload['expected_date'] ?? $purchase->expected_date;
    //         $purchase->currency = $payload['currency'] ?? $purchase->currency;
    //         $purchase->subtotal = round($subtotal, 2);
    //         $purchase->shipping_amount = round($shipping, 2);
    //         $purchase->discount_amount = round($discountAmount, 2); // if you have this column
    //         $purchase->total_amount = round($total, 2);
    //         $purchase->notes = $payload['reference'] ?? $purchase->notes;
    //         $purchase->purchase_invoice = $invoicePath;
    //         $purchase->updated_by = auth()->id(); // optional
    //         $purchase->save();

    //         // Sync items:
    //         // Approach: map incoming items by id(product_id) and existing items by id
    //         $incoming = $payload['items'];
    //         $existingItems = $purchase->items()->get()->keyBy(function($it){ return $it->product_id; });

    //         $seenProductIds = [];

    //         foreach ($incoming as $i) {
    //             $pid = (int)$i['product_id'];
    //             $seenProductIds[] = $pid;
    //             $unitCost = round((float)$i['unit_cost'], 2);
    //             $qty = (float)$i['quantity'];
    //             $lineTotal = round($unitCost * $qty, 2);

    //             if ($existingItems->has($pid)) {
    //                 // update existing row
    //                 $row = $existingItems->get($pid);
    //                 $row->unit_cost = $unitCost;
    //                 $row->quantity = $qty;
    //                 $row->line_total = $lineTotal;
    //                 $row->sku = $i['sku'] ?? $row->sku;
    //                 $row->description = $i['description'] ?? $row->description;
    //                 $row->save();
    //             } else {
    //                 // create new item
    //                 PurchaseOrderItem::create([
    //                     'purchase_order_id' => $purchase->id,
    //                     'product_id' => $pid,
    //                     'sku' => $i['sku'] ?? null,
    //                     'description' => $i['description'] ?? null,
    //                     'unit_cost' => $unitCost,
    //                     'quantity' => $qty,
    //                     'received_quantity' => 0,
    //                     'line_total' => $lineTotal,
    //                 ]);
    //             }
    //         }

    //         // delete removed items (those existing but not in incoming payload)
    //         foreach ($existingItems as $prodId => $row) {
    //             if (! in_array($prodId, $seenProductIds)) {
    //                 // you may want to prevent deleting if received_quantity > 0
    //                 if ($row->received_quantity > 0) {
    //                     // either block or set quantity to received_quantity
    //                     // here we block delete and adjust quantity to received
    //                     $row->quantity = max($row->received_quantity, 0);
    //                     $row->line_total = round($row->unit_cost * $row->quantity, 2);
    //                     $row->save();
    //                 } else {
    //                     $row->delete();
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Purchase order updated',
    //             'order_id' => $purchase->id,
    //             'redirect_url' => route('purchase.orders.show', $purchase->id),
    //         ], 200);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         \Log::error('Purchase update error: ' . $e->getMessage());
    //         // if we uploaded new file but transaction failed, consider deleting uploaded file
    //         // Storage::disk('public')->delete($newPath ?? '');
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update purchase order',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function update(Request $request, PurchaseOrder $purchase)
    {
        // Authorization optional
        // $this->authorize('update', $purchase);

        // If purchase already received, disallow full edit
        if ($purchase->status === 'received') {
            return response()->json([
                'success' => false,
                'message' => 'Received orders cannot be edited.',
            ], 403);
        }

        // Accept payload JSON or direct form fields
        $payloadJson = $request->input('payload');
        $payload     = null;
        if ($payloadJson) {
            $payload = json_decode($payloadJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['success' => false, 'message' => 'Invalid payload JSON'], 422);
            }
        } else {
            $payload = [
                'supplier_id'     => $request->input('supplier_id') ?? $purchase->supplier_id,
                'warehouse_id'    => $request->input('warehouse_id') ?? $purchase->warehouse_id,
                'branch_id'       => $request->input('branch_id') ?? $purchase->branch_id,
                'status'          => $request->input('status') ?? $purchase->status,
                'order_date'      => $request->input('order_date') ?? $purchase->order_date,
                'expected_date'   => $request->input('expected_date') ?? $purchase->expected_date,
                'reference'       => $request->input('reference') ?? $purchase->reference,
                'currency'        => $request->input('currency') ?? $purchase->currency,
                'shipping_amount' => $request->input('shipping_amount') ?? $purchase->shipping_amount,
                'discount'        => $request->input('discount') ?? null,
                'items'           => $request->input('items') ?? [],
            ];
        }

        // Validate top-level payload (adjust rules as needed)
        $validator = Validator::make($payload, [
            'supplier_id'        => 'required|exists:suppliers,id',
            'warehouse_id'       => 'nullable|exists:warehouses,id',
            'branch_id'          => 'nullable|exists:branches,id',
            'order_date'         => 'nullable|date',
            'shipping_amount'    => 'nullable|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_cost'  => 'required|numeric|min:0',
            'items.*.quantity'   => 'required|numeric|min:0.001',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Recompute subtotal from incoming items
        $subtotal = 0.0;
        foreach ($payload['items'] as $i) {
            $line = (float) $i['unit_cost'] * (float) $i['quantity'];
            $subtotal += $line;
        }

        $shipping = isset($payload['shipping_amount']) ? (float) $payload['shipping_amount'] : 0.0;

        // discount normalization: expecting {type, value}
        $discountAmount = 0.0;
        if (! empty($payload['discount']) && isset($payload['discount']['value'])) {
            $d = (float) $payload['discount']['value'];
            if (($payload['discount']['type'] ?? 'flat') === 'percentage') {
                $discountAmount = ($subtotal * $d) / 100.0;
            } else {
                $discountAmount = $d;
            }
        }

        $total = round(max(0, $subtotal + $shipping - $discountAmount), 2);

        // handle invoice file (store path if new)
        $invoicePath     = $purchase->purchase_invoice;
        $newUploadedPath = null;
        if ($request->hasFile('purchase_invoice')) {
            // use your helper - adjust target folder as needed
            $newUploadedPath = uploadImage($request->file('purchase_invoice'), 'purchase/images');
            if ($newUploadedPath) {
                $invoicePath = $newUploadedPath;
            }
        }

        DB::beginTransaction();
        try {
            // Lock the purchase row to avoid race conditions (if supported by DB)
            $purchase = PurchaseOrder::where('id', $purchase->id)->lockForUpdate()->first();

            // Recalculate existing paid (source of truth: payments table)
            $paid = (float) $purchase->payments()->sum('amount');

            // If new computed total is less than already-paid amount -> block (to avoid negative outstanding)
            if ($total < $paid) {
                // if we uploaded a new file earlier, consider deleting it to avoid orphan file
                if (! empty($newUploadedPath)) {
                    Storage::disk('public')->delete($newUploadedPath);
                }
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Total cannot be less than already paid amount (' . number_format($paid, 2) . ').',
                ], 422);
            }

            // Update purchase order main fields
            $purchase->supplier_id     = $payload['supplier_id'];
            $purchase->warehouse_id    = $payload['warehouse_id'] ?? null;
            $purchase->branch_id       = $payload['branch_id'] ?? null;
            $purchase->status          = $payload['status'] ?? $purchase->status;
            $purchase->order_date      = $payload['order_date'] ?? $purchase->order_date;
            $purchase->expected_date   = $payload['expected_date'] ?? $purchase->expected_date;
            $purchase->currency        = $payload['currency'] ?? $purchase->currency;
            $purchase->subtotal        = round($subtotal, 2);
            $purchase->shipping_amount = round($shipping, 2);
            // optionally map to your DB columns for discount
            $purchase->discount_amount  = round($discountAmount, 2);
            $purchase->total_amount     = round($total, 2);
            $purchase->notes            = $payload['reference'] ?? $purchase->notes;
            $purchase->purchase_invoice = $invoicePath;
            $purchase->updated_by       = auth()->id() ?? $purchase->updated_by;
            $purchase->save();

            // Sync items: match by product_id (update existing, create new, remove missing)
            $incoming      = $payload['items'];
            $existingItems = $purchase->items()->get()->keyBy(function ($it) {
                return $it->product_id;
            });

            $seenProductIds = [];
            foreach ($incoming as $i) {
                $pid              = (int) $i['product_id'];
                $seenProductIds[] = $pid;
                $unitCost         = round((float) $i['unit_cost'], 2);
                $qty              = (float) $i['quantity'];
                $lineTotal        = round($unitCost * $qty, 2);

                if ($existingItems->has($pid)) {
                    $row = $existingItems->get($pid);
                    // if row has received_quantity > 0, you might want to ensure quantity >= received_quantity
                    if ($row->received_quantity > 0 && $qty < $row->received_quantity) {
                        // preserve received quantity
                        $qty = $row->received_quantity;
                    }
                    $row->unit_cost   = $unitCost;
                    $row->quantity    = $qty;
                    $row->line_total  = $lineTotal;
                    $row->sku         = $i['sku'] ?? $row->sku;
                    $row->description = $i['description'] ?? $row->description;
                    $row->save();
                } else {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchase->id,
                        'product_id'        => $pid,
                        'sku'               => $i['sku'] ?? null,
                        'description'       => $i['description'] ?? null,
                        'unit_cost'         => $unitCost,
                        'quantity'          => $qty,
                        'received_quantity' => 0,
                        'line_total'        => $lineTotal,
                    ]);
                }
            }

            // delete removed items (only if they have no received_quantity)
            foreach ($existingItems as $prodId => $row) {
                if (! in_array($prodId, $seenProductIds)) {
                    if ($row->received_quantity > 0) {
                        // keep row but clamp quantity to received_quantity
                        $row->quantity   = max($row->received_quantity, 0);
                        $row->line_total = round($row->unit_cost * $row->quantity, 2);
                        $row->save();
                    } else {
                        $row->delete();
                    }
                }
            }

            // Recompute paid & outstanding & payment_status after items change
            $paid                         = (float) $purchase->payments()->sum('amount');
            $purchase->paid_amount        = round($paid, 2);
            $purchase->outstanding_amount = max(0, round($purchase->total_amount - $purchase->paid_amount, 2));
            $purchase->payment_status     = $purchase->paid_amount <= 0
                ? 'unpaid'
                : ($purchase->outstanding_amount <= 0 ? 'paid' : 'partially_paid');
            $purchase->save();

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Purchase order updated',
                'order_id'     => $purchase->id,
                'redirect_url' => route('purchase.orders.show', $purchase->id),
                'new_summary'  => [
                    'paid_amount'    => (float) $purchase->paid_amount,
                    'outstanding'    => (float) $purchase->outstanding_amount,
                    'payment_status' => $purchase->payment_status,
                ],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Purchase update error: ' . $e->getMessage());
            // cleanup uploaded file if created
            if (! empty($newUploadedPath)) {
                Storage::disk('public')->delete($newUploadedPath);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update purchase order',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }




    public function destroy(PurchaseOrder $purchase)
    {
        // CASE 1: Draft → allow delete
        if ($purchase->status === 'draft') {

            DB::beginTransaction();
            try {
                // Delete invoice file
                if ($purchase->purchase_invoice) {
                    Storage::disk('public')->delete($purchase->purchase_invoice);
                }

                // Delete items
                $purchase->items()->delete();

                // Delete payments (optional; depends on your business rule)
                $purchase->payments()->delete();

                // Finally delete order
                $purchase->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Purchase deleted successfully'
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Delete failed',
                    'error'   => $e->getMessage()
                ], 500);
            }
        }

        // CASE 2 & 3: Received OR Partially received → deny
        return response()->json([
            'success' => false,
            'message' => 'Cannot delete purchase because items are already received.'
        ], 403);
    }
}
