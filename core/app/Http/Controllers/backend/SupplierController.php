<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\PurchaseOrder;
use App\Models\backend\PurchasePayment;
use App\Models\backend\PurchaseReturn;
use App\Models\backend\PurchaseReturnPayment;
use App\Models\backend\Supplier;
use App\Models\backend\SupplierLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SupplierController extends Controller
{
    public function index()
    {
        return view('backend.modules.suppliers.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'slug', 'email', 'phone', 'postal_code', 'address', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Supplier::query()
            ->select(['id', 'name', 'slug', 'email', 'phone', 'postal_code', 'address', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('slug', 'like', "%{$searchVal}%")
                    ->orWhere('email', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->name) . '</strong>';

            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="' . route('supplier.edit', $b->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main "
                    data-size="lg"
                    data-onload="supplierIndex.onLoad"
                    data-onsuccess="supplierIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('supplier.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $icon = '<div  style="width:70px"><img src="' . image($b->image) . '" alt="img"></div>';

            $data[] = [
                $b->id,
                $nameCol,
                e($b->slug),
                $b->email,
                $b->phone,
                $b->address,
                $b->postal_code,
                $active,
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
        return view('backend.modules.suppliers.create'); // partial only
    }
    public function createModal()
    {
        return view('backend.modules.suppliers.createModal'); // partial only
    }

    public function store(Request $req)
    {
        // dd($req->all());
        $data = $req->validate([
            'name'        => ['required', 'string', 'max:150', 'unique:suppliers,name'],
            'slug'        => ['nullable', 'string', 'max:150', 'unique:suppliers,slug'],
            'email'       => ['required', 'string', 'max:150', 'unique:suppliers,email'],
            'phone'       => ['required', 'string', 'max:50', 'unique:suppliers,phone'],
            'postal_code' => ['nullable', 'string'],
            'address'     => ['required', 'string', 'max:255'],
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active'   => ['nullable', 'integer'],

        ]);

        $imagePath = null;
        if ($req->hasFile('image')) {
            $imagePath = uploadImage($req->file('image'), 'supplier/images');
        }

        $supplier = Supplier::create([
            'name'        => ucwords($data['name']),
            'slug'        => $data['slug'] ?? null,
            'email'       => $data['email'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'address'     => $data['address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'image'       => $imagePath,
            'is_active'   => $data['is_active'] ?? 1,

        ]);

        return response()->json(['ok' => true, 'msg' => 'supplier created', 'id' => $supplier->id]);
    }

    public function editModal(Supplier $supplier)
    {
        return view('backend.modules.suppliers.edit', compact('supplier'));
    }

    public function show(Supplier $supplier)
    {

        return response()->json([
            'id'   => $supplier->id,
            'name' => $supplier->name,
            'slug' => $supplier->slug,

        ]);
    }

    public function update(Request $req, Supplier $supplier)
    {

        $data = $req->validate([
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['required', 'string', 'max:150'],
            'email'       => ['required', 'string', 'max:150'],
            'phone'       => ['required', 'string', 'max:50'],
            'postal_code' => ['required', 'string'],
            'address'     => ['required', 'string', 'max:255'],
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active'   => ['required', 'integer'],

        ]);

        $previousImage = $supplier->image;

        if ($req->hasFile('image')) {
            $imagePath       = uploadImage($req->file('image'), 'supplier/images');
            $supplier->image = $imagePath;

            if ($previousImage && file_exists($previousImage)) {
                unlink($previousImage);
            }
        }

        $supplier->name        = ucwords($data['name']);
        $supplier->slug        = $data['slug'];
        $supplier->email       = $data['email'];
        $supplier->phone       = $data['phone'];
        $supplier->address     = $data['address'];
        $supplier->postal_code = $data['postal_code'];
        $supplier->is_active   = $data['is_active'];

        $supplier->save();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier updated successfully!');
    }
    public function importCsvModal()
    {
        return view('backend.modules.suppliers.import_csv');
    }

    public function importCsv(Request $req)
    {

        $sheets[] = $req->all();
        if (empty($sheets) || empty($sheets[0])) {
            return back()->with('error', 'Uploaded file is empty or unreadable.');
        }

        $rows = $sheets[0];

        // If first row is header, normalize it and map rows to assoc arrays
        $header   = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $allowed = ['name', 'slug', 'email', 'phone', 'address', 'postal_code', 'image', 'is_active']; // allowed DB columns

        $insertRows = [];

        foreach ($dataRows as $r) {
            // protect against ragged rows
            $assoc = [];
            foreach ($header as $i => $col) {
                $assoc[$col] = $r[$i] ?? null;
            }
            // whitelist and normalize
            $row = array_intersect_key($assoc, array_flip($allowed));
            $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

            if (empty($row['phone'])) {
                continue; // skip if no unique identifier
            }
            // prepare for upsert; ensure email key exists even if null
            $insertRows[] = [
                'name'        => ucwords($row['name']) ?? null,
                'slug'        => $row['slug'] ?? null,
                'email'       => $row['email'] ?? null,
                'phone'       => $row['phone'] ?? null,
                'address'     => ucwords($row['address']) ?? null,
                'postal_code' => $row['postal_code'] ?? null,
                'image'       => $row['image'] ?? null,
                'is_active'   => (int) ($row['is_active']) ?? 1,
            ];
        }

        if (! empty($insertRows)) {
            // Upsert in bulk (Laravel 8+). Unique by email (or phone)
            Supplier::upsert($insertRows, ['name', 'slug', 'phone', 'address', 'postal_code', 'image', 'is_active']);
        }

        return response()->json(['ok' => true, 'msg' => 'Suppliers imported successfully']);
    }

    public function ledger()
    {
        return view('backend.modules.suppliers.ledger');
    }

    // public function ledgerList(Request $req)
    // {
    //     // dd($req->all());

    //     $supplier_id = $req->supplier_id;
    //     $branch_id   = current_branch_id();

    //     // dd($branch_id);

    //     if (! $supplier_id) {
    //         return response()->json([
    //             'draw'                 => (int) $req->draw,
    //             'iTotalRecords'        => 0,
    //             'iTotalDisplayRecords' => 0,
    //             'aaData'               => [],
    //         ]);
    //     }

    //     $start = $req->from_date
    //         ? Carbon::parse($req->from_date)->startOfDay()
    //         : null;

    //     $end = $req->to_date
    //         ? Carbon::parse($req->to_date)->endOfDay()
    //         : null;

    //     $search = trim($req->input('search.value'));

    //     $rows = collect();

    //     $start = $start ? Carbon::parse($start)->startOfDay() : null; // 2026-01-26 00:00:00
    //     $end   = $end ? Carbon::parse($end)->endOfDay() : null;

    //     $purchaseOrders = PurchaseOrder::with(['supplier', 'branch'])
    //         ->where('supplier_id', $supplier_id)
    //         ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
    //         ->when($start && $end, function ($q) use ($start, $end) {
    //             $q->whereBetween('created_at', [$start, $end]);
    //         })
    //         ->get()
    //         ->map(function ($po) {
    //             return [
    //                 'supplier' => $po->supplier?->name ?? 'N/A',
    //                 'type'     => 'Purchase',
    //                 'branch'   => $po->branch?->name ?? 'N/A',
    //                 'txn_id'   => $po->id,
    //                 'debit'    => 0,
    //                 'credit'   => $po->total_amount,
    //                 'date'     => $po->created_at->format('d-m-Y'),

    //             ];
    //         });

    //     $purchasePayments = PurchasePayment::with(['supplier', 'branch'])
    //         ->where('supplier_id', $supplier_id)
    //         ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
    //         ->when($start && $end, function ($q) use ($start, $end) {
    //             $q->whereBetween('created_at', [$start, $end]);
    //         })
    //         ->get()
    //         ->map(function ($pp) {
    //             return [
    //                 'supplier' => $pp->supplier?->name ?? 'N/A',
    //                 'type'     => 'Purchase Payment',
    //                 'branch'   => $pp->branch?->name ?? 'N/A',
    //                 'txn_id'   => $pp->id,
    //                 'debit'    => $pp->amount,
    //                 'credit'   => 0,
    //                 'date'     => $pp->created_at->format('d-m-Y'),
    //             ];
    //         });

    //     /**
    //      * Purchase Return Payments (money received back)
    //      */
    //     $purchaseReturnPayments = PurchaseReturnPayment::with(['supplier', 'branch'])
    //         ->where('supplier_id', $supplier_id)
    //         ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
    //         ->when($start && $end, function ($q) use ($start, $end) {
    //             $q->whereBetween('created_at', [$start, $end]);
    //         })
    //         ->get()
    //         ->map(function ($pr) {
    //             return [
    //                 'supplier' => $pr->supplier?->name ?? 'N/A',
    //                 'type'     => 'Purchase Return Payment',
    //                 'branch'   => $pr->branch?->name ?? 'N/A',
    //                 'txn_id'   => $pr->id,
    //                 'debit'    => $pr->amount,
    //                 'credit'   => 0,
    //                 'date'     => $pr->created_at->format('d-m-Y'),
    //             ];
    //         });

    //     $ledger = $purchaseOrders
    //         ->merge($purchasePayments)
    //         ->merge($purchaseReturnPayments)
    //         ->sortBy('date')
    //         ->values();

    //     $data    = [];
    //     $balance = 0;

    //     foreach ($ledger as $a) {

    //         $balance += $a['debit'];
    //         $balance -= $a['credit'];
    //         $data[]   = [
    //             1 => $a['supplier'],
    //             2 => $a['type'],
    //             3 => $a['txn_id'],
    //             4 => $a['debit'],
    //             5 => $a['credit'],
    //             6 => $balance,
    //             7 => $a['date'],
    //         ];
    //     }

    //     // $totalDebit = $ledger->sum('debit');
    //     // $totalCredit = $ledger->sum('credit');
    //     $totalRecords  = $ledger->count();

    //     // return response()->json(['success' => true, 'ledger' => $ledger, 'total_debit' => $totalDebit, 'total_credit' => $totalCredit]);
    //     return response()->json([
    //         'draw'                 => (int) $req->draw,
    //         'iTotalRecords'        => $totalRecords,
    //         'iTotalDisplayRecords' => $totalRecords,
    //         'aaData'               => $data,
    //     ]);
    // }

    public function ledgerList(Request $req)
    {
        $supplierId = $req->supplier_id;
        $branchId   = current_branch_id();

        if (! $supplierId) {
            return response()->json([
                'draw'                 => (int) $req->draw,
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        $from = $req->from_date
            ? Carbon::parse($req->from_date)->startOfDay()
            : null;

        $to = $req->to_date
            ? Carbon::parse($req->to_date)->endOfDay()
            : null;

        $search = trim($req->input('search.value'));

        $rows = collect();

        /* =====================================================
     | 1️⃣ Purchase Orders (Credit – we owe supplier)
     ===================================================== */
        $purchases = PurchaseOrder::with(['supplier', 'branch'])
            ->where('supplier_id', $supplierId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();

        foreach ($purchases as $po) {
            $rows->push([
                'date'     => $po->created_at,
                'supplier' => $po->supplier?->name ?? '—',
                'type'     => 'Purchase',
                'ref'      => $po->po_number ?? 'PO-' . $po->id,
                'ref_type' => 'purchase',
                'ref_id'   => $po->id,
                'debit'    => 0,
                'credit'   => (float) $po->total_amount,
            ]);
        }

        /* =====================================================
     | 2️⃣ Purchase Payments (Debit – we paid supplier)
     ===================================================== */
        $payments = PurchasePayment::with(['supplier', 'branch'])
            ->where('supplier_id', $supplierId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();

        foreach ($payments as $pp) {
            $rows->push([
                'date'     => $pp->created_at,
                'supplier' => $pp->supplier?->name ?? '—',
                'type'     => 'Purchase Payment',
                'ref'      => 'PAY-' . $pp->id,
                'ref_type' => 'purchase_payment',
                'ref_id'   => $pp->id,
                'debit'    => (float) $pp->amount,
                'credit'   => 0,
            ]);
        }


        $purchaseReturns = PurchaseReturn::with(['supplier', 'items'])
            ->where('supplier_id', $supplierId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();
            foreach ($purchaseReturns as $pr) {
                $rows->push([
                    'date'     => $pr->created_at,
                    'supplier' => $pr->supplier?->name ?? '—',
                    'type'     => 'Purchase Return',
                    'ref'      => $pr->return_number ?? 'PR-' . $pr->id,
                    'ref_type' => 'purchase_return',
                    'ref_id'   => $pr->id,
                    'debit'    => 0,
                    'credit'   => (float) $pr->total_amount,
                ]);
            }

        /* =====================================================
     | 3️⃣ Purchase Return Payments (Debit – money received back)
     ===================================================== */
        $returnPayments = PurchaseReturnPayment::with(['supplier', 'branch'])
            ->where('supplier_id', $supplierId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();

        foreach ($returnPayments as $rp) {
            $rows->push([
                'date'     => $rp->created_at,
                'supplier' => $rp->supplier?->name ?? '—',
                'type'     => 'Purchase Return Payment',
                'ref'      => 'PRP-' . $rp->id,
                'ref_type' => 'purchase_return_payment',
                'ref_id'   => $rp->id,
                'debit'    => (float) $rp->amount,
                'credit'   => 0,
            ]);
        }

        /* =====================================================
     | 3b. Purchase Return Cancel (credit reversals)
     ===================================================== */
        $returnCancels = SupplierLedger::with(['supplier'])
            ->where('supplier_id', $supplierId)
            ->where('reference_type', 'purchase_return_cancel')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('txn_date', [$from, $to]))
            ->get();

        foreach ($returnCancels as $rc) {
            $rows->push([
                'date'     => $rc->txn_date,
                'supplier' => $rc->supplier?->name ?? '—',
                'type'     => 'Purchase Return Cancel',
                'ref'      => 'PRC-' . $rc->reference_id,
                'ref_type' => 'purchase_return',
                'ref_id'   => $rc->reference_id,
                'debit'    => (float) $rc->debit,
                'credit'   => (float) $rc->credit,
            ]);
        }

        /* =====================================================
     | 4️⃣ Sort by date
     ===================================================== */
        $rows = $rows->sortBy('date')->values();

        /* =====================================================
     | 5️⃣ Search filter (client-side)
     ===================================================== */
        if ($search !== '') {
            $rows = $rows->filter(function ($r) use ($search) {
                return;
                str_contains(strtolower($r['ref']), strtolower($search)) ||
                str_contains(strtolower($r['type']), strtolower($search)) ||
                str_contains((string) $r['debit'], $search) ||
                str_contains((string) $r['credit'], $search);
            })->values();
        }

        /* =====================================================
     | 6️⃣ Running Balance + DataTable rows
     ===================================================== */
        $balance = 0;
        $data    = [];
        $sl      = 1;

        foreach ($rows as $r) {

            // Supplier ledger rule:
            // Debit  -> reduces payable
            // Credit -> increases payable
            $balance += $r['credit'];
            $balance -= $r['debit'];

            $refHtml = $r['ref'];

            if (! empty($r['ref_type']) && ! empty($r['ref_id'])) {
                $refHtml = '
                <a href="#"
                   class="AjaxViewModal text-primary-400 fw-semibold"
                   data-size="lg"
                   data-ajax-modal="' . route('supplier.ledger.reference.view', [
                    'type' => $r['ref_type'],
                    'id'   => $r['ref_id'],
                ]) . '">
                   ' . e($r['ref']) . '
                </a>';
            }

            $data[] = [
                $sl++,
                Carbon::parse($r['date'])->format('Y-m-d'),
                $r['supplier'],
                $refHtml,
                $r['type'],
                number_format($r['debit'], 2),
                number_format($r['credit'], 2),
                number_format($balance, 2),
            ];
        }

        return response()->json([
            'draw'                 => (int) $req->draw,
            'iTotalRecords'        => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData'               => $data,
        ]);
    }

    public function supplierLedgerReference(string $type, int $id)
    {
        $type = strtolower(trim($type));

        return match ($type) {

            'purchase'                => (function () use ($id) {

                $order = PurchaseOrder::with([
                    'items.product',
                    'supplier',
                    'payments',
                ])->findOrFail($id);

                $paid        = $order->payments->sum('amount');
                $outstanding = round($order->total_amount - $paid, 2);

                return view(
                    'backend.modules.purchase.show_modal',
                    compact('order', 'paid', 'outstanding')
                );

            })(),

            'purchase_payment'        => view(
                'backend.modules.purchase.payment_details_modal',
                ['payment' => PurchasePayment::with('supplier')->findOrFail($id)]
            ),

            'purchase_return_payment' => view(
                'backend.modules.purchase_returns.payment_details_modal',
                ['payment' => PurchaseReturnPayment::with('supplier')->findOrFail($id)]
            ),

            'purchase_return'         => $this->purchaseReturnModal($id),

            'purchase_return_cancel'  => $this->purchaseReturnModal($id),

            default                   => abort(404, 'Reference not supported: ' . $type),
        };
    }

    protected function purchaseReturnModal(int $id)
    {
        $order = PurchaseReturn::with([
            'supplier',
            'items.product',
            'purchaseReturnPayment',
        ])->findOrFail($id);

        return view('backend.modules.purchase_returns.show_modal', compact('order'));
    }

    public function ledgerSummary(Request $req)
    {
        $supplier_id = $req->supplier_id;
        $branch_id   = current_branch_id();

        if (! $supplier_id) {
            return response()->json([
                'total_purchase' => 0,
                'total_paid'     => 0,
                'total_return'   => 0,
                'balance'        => 0,
            ]);
        }

        $start = $req->from_date
            ? Carbon::parse($req->from_date)->startOfDay()
            : null;

        $end = $req->to_date
            ? Carbon::parse($req->to_date)->endOfDay()
            : null;

        $applyDateFilter = function ($query, string $column) use ($start, $end) {
            if ($start) {
                $query->where($column, '>=', $start);
            }

            if ($end) {
                $query->where($column, '<=', $end);
            }

            return $query;
        };

        /* ---------------- Total Purchase ---------------- */
        $purchaseQuery = PurchaseOrder::where('supplier_id', $supplier_id)
            ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
            ;
        $applyDateFilter($purchaseQuery, 'created_at');
        $totalPurchase = $purchaseQuery->sum('total_amount');

        /* ---------------- Total Paid ---------------- */
        $paidQuery = PurchasePayment::where('supplier_id', $supplier_id)
            ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
            ;
        $applyDateFilter($paidQuery, 'created_at');
        $totalPaid = $paidQuery->sum('amount');

        /* ---------------- Total Return Received ---------------- */
        $returnQuery = PurchaseReturnPayment::where('supplier_id', $supplier_id)
            ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
            ;
        $applyDateFilter($returnQuery, 'created_at');
        $totalReturn = $returnQuery->sum('amount');

        /* ---------------- Purchase Returns ---------------- */
        $purchaseReturnQuery = PurchaseReturn::where('supplier_id', $supplier_id)
            ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
            ;
        $applyDateFilter($purchaseReturnQuery, 'created_at');
        $purchaseReturnTotal = $purchaseReturnQuery->sum('total_amount');

        /* ---------------- Return Cancellation ---------------- */
        $cancelledReturn = SupplierLedger::where('supplier_id', $supplier_id)
            ->where('reference_type', 'purchase_return_cancel')
            ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
            ;
        $applyDateFilter($cancelledReturn, 'txn_date');
        $cancelledReturnTotal = $cancelledReturn->sum('credit');

        $balance = round(
            $totalPurchase
            - $totalPaid
            + $purchaseReturnTotal
            - $totalReturn
            + (float) $cancelledReturnTotal,
            2
        );

        return response()->json([
            'total_purchase' => round($totalPurchase, 2),
            'total_paid'     => round($totalPaid, 2),
            'total_return'   => round($totalReturn, 2),
            'balance'        => $balance,
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        // $inUse = DB::table('suppliers')->where('supplier_id', $supplier->id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This supplier has {$inUse} subcategorie(s). Reassign them first.",
        //     ], 422);
        // }

        $image = $supplier->image;

        $supplier->delete();

        if (isset($image) && file_exists($image)) {
            unlink($image);
        }

        return response()->json(['ok' => true, 'msg' => 'supplier deleted']);
    }

    public function recent_record(Request $req)
    {
        $supplier = Supplier::select('id', 'name')->latest('id')->first();
        return response()->json(['supplier' => $supplier]);
    }

    public function select2(Request $r)
    {

        $q    = trim($r->input('q', ''));
        $type = $r->type;
        $base = Supplier::query()->where('is_active', 1);

        if ($q !== '') {
            $base->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%");
            });
        }

        $items = $base->orderBy('id', 'desc')
            ->limit(20)->get(['id', 'name']);

        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'       => $t->id,
                'text'     => $t->name,
                'selected' => true,

            ]),
        ]);
    }
}
