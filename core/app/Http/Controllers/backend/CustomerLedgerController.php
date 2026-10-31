<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Sale;
use App\Models\backend\SalePayment;
use App\Models\backend\SaleReturn;
use App\Models\backend\SaleReturnPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerLedgerController extends Controller
{
    public function index()
    {
        return view('backend.modules.customer_ledger.index');
    }

    // public function listAjax(Request $request)
    // {
    //     $customerId = $request->customer_id;

    //     if (! $customerId) {
    //         return response()->json([
    //             'draw'                 => (int) $request->draw,
    //             'iTotalRecords'        => 0,
    //             'iTotalDisplayRecords' => 0,
    //             'aaData'               => [],
    //         ]);
    //     }

    //     $from = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
    //     $to   = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

    //     $rows = collect();

    //     /* ---------------- Sales ---------------- */
    //     $sales = Sale::where('customer_id', $customerId)
    //         ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
    //         ->get();

    //     foreach ($sales as $s) {
    //         $rows->push([
    //             'date'   => $s->created_at,
    //             'ref'    => $s->invoice_no,
    //             'desc'   => 'Sale',
    //             'debit'  => $s->total,
    //             'credit' => 0,
    //         ]);
    //     }

    //     /* ---------------- Sale Payments ---------------- */
    //     $payments = SalePayment::whereHas('sale', fn($q) =>
    //         $q->where('customer_id', $customerId)
    //     )->when($from, fn($q) => $q->whereBetween('paid_at', [$from, $to]))
    //         ->get();

    //     foreach ($payments as $p) {
    //         $rows->push([
    //             'date'   => $p->paid_at,
    //             'ref'    => $p->payment_type,
    //             'desc'   => 'Sale Payment',
    //             'debit'  => 0,
    //             'credit' => $p->amount,
    //         ]);
    //     }

    //     /* ---------------- Sale Returns ---------------- */
    //     $returns = SaleReturn::where('customer_id', $customerId)
    //         ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
    //         ->get();

    //     foreach ($returns as $r) {
    //         $rows->push([
    //             'date'   => $r->created_at,
    //             'ref'    => 'SR-' . $r->id,
    //             'desc'   => 'Sale Return',
    //             'debit'  => 0,
    //             'credit' => $r->total_refund,
    //         ]);
    //     }

    //     /* ---------------- Refund Payments ---------------- */
    //     $refunds = SaleReturnPayment::whereHas('saleReturn', fn($q) =>
    //         $q->where('customer_id', $customerId)
    //     )->when($from, fn($q) => $q->whereBetween('payment_date', [$from, $to]))
    //         ->get();

    //     foreach ($refunds as $rp) {
    //         $rows->push([
    //             'date'   => $rp->payment_date,
    //             'ref'    => 'Refund',
    //             'desc'   => 'Refund Paid',
    //             'debit'  => $rp->amount,
    //             'credit' => 0,
    //         ]);
    //     }

    //     /* ---------------- Sort + Balance ---------------- */
    //     $rows = $rows->sortBy('date')->values();

    //     $balance = 0;
    //     $data    = [];

    //     foreach ($rows as $r) {
    //         $balance += $r['debit'];
    //         $balance -= $r['credit'];

    //         $data[] = [
    //             Carbon::parse($r['date'])->format('Y-m-d'),
    //             $r['ref'],
    //             $r['desc'],
    //             number_format($r['debit'], 2),
    //             number_format($r['credit'], 2),
    //             number_format($balance, 2),
    //         ];
    //     }

    //     return response()->json([
    //         'draw'                 => (int) $request->draw,
    //         'iTotalRecords'        => count($data),
    //         'iTotalDisplayRecords' => count($data),
    //         'aaData'               => $data,
    //     ]);
    // }

    public function listAjax(Request $request)
    {
        $customerId = $request->customer_id;
        $branchId   = current_branch_id();

        if (! $customerId) {
            return response()->json([
                'draw'                 => (int) $request->draw,
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

        $search = trim($request->input('search.value'));

        $rows = collect();

        /* ---------------- Sales ---------------- */
        $sales = Sale::where('customer_id', $customerId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();

        foreach ($sales as $s) {
            $rows->push([
                'date'     => $s->created_at,
                'ref'      => $s->invoice_no,
                'desc'     => 'Sale',
                'debit'    => $s->total,
                'credit'   => 0,

                // 🔥 virtual reference
                'ref_type' => 'sale',
                'ref_id'   => $s->id,
            ]);
        }

        /* ---------------- Sale Payments ---------------- */
        $payments = SalePayment::whereHas('sale', function ($q) use ($customerId, $branchId) {
            $q->where('customer_id', $customerId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        })
            ->when($from && $to, fn($q) => $q->whereBetween('paid_at', [$from, $to]))
            ->get();

        foreach ($payments as $p) {
            // $rows->push([
            //     'date'   => $p->paid_at,
            //     'ref'    => strtoupper($p->payment_type),
            //     'desc'   => 'Sale Payment',
            //     'debit'  => 0,
            //     'credit' => $p->amount,
            // ]);
            $rows->push([
                'date'     => $p->paid_at,
                'ref'      => 'PAY-' . $p->id,
                'desc'     => 'Sale Payment',
                'debit'    => 0,
                'credit'   => $p->amount,

                'ref_type' => 'sale_payment',
                'ref_id'   => $p->id,
            ]);
        }

        /* ---------------- Sale Returns ---------------- */
        $returns = SaleReturn::where('customer_id', $customerId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();

        foreach ($returns as $r) {
            // $rows->push([
            //     'date'   => $r->created_at,
            //     'ref'    => 'SR-' . $r->id,
            //     'desc'   => 'Sale Return',
            //     'debit'  => 0,
            //     'credit' => $r->total_refund,
            // ]);
            $rows->push([
                'date'     => $r->created_at,
                'ref'      => 'SALE-RETURN-' . $r->id,
                'desc'     => 'Sale Return',
                'debit'    => 0,
                'credit'   => $r->total_refund,

                'ref_type' => 'sale_return',
                'ref_id'   => $r->id,
            ]);
        }

        /* ---------------- Refund Payments ---------------- */
        $refunds = SaleReturnPayment::whereHas('saleReturn', function ($q) use ($customerId, $branchId) {
            $q->where('customer_id', $customerId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        })
            ->when($from && $to, fn($q) => $q->whereBetween('payment_date', [$from, $to]))
            ->get();

        foreach ($refunds as $rp) {
            $rows->push([
                'date'     => $rp->payment_date,
                'ref'      => 'REFUND',
                'desc'     => 'Refund Paid',
                'debit'    => $rp->amount,
                'credit'   => 0,

                'ref_type' => 'sale_return_payment',
                'ref_id'   => $rp->sale_return_id,
            ]);
        }

        /* ---------------- Sort ---------------- */
        $rows = $rows->sortBy('date')->values();

        /* ---------------- Search filter ---------------- */
        if ($search !== '') {
            $rows = $rows->filter(function ($r) use ($search) {
                return;
                str_contains(strtolower($r['ref']), strtolower($search)) ||
                str_contains(strtolower($r['desc']), strtolower($search)) ||
                str_contains((string) $r['debit'], $search) ||
                str_contains((string) $r['credit'], $search);
            })->values();
        }

        /* ---------------- Running Balance ---------------- */
        $balance = 0;
        $data    = [];

        $sl = 1;

        foreach ($rows as $r) {
            $balance += $r['debit'];
            $balance -= $r['credit'];

            $refHtml = $r['ref'];

            if (! empty($r['ref_type']) && ! empty($r['ref_id'])) {
                $refHtml = '
                <a href="#"
                class="AjaxViewModal text-primary-400 fw-semibold"
                data-size="lg"
                data-ajax-modal="' . route('customerLedger.reference.view', [
                    'type' => $r['ref_type'],
                    'id'   => $r['ref_id'],
                ]) . '">
                ' . e($r['ref']) . '
                </a>';
            }

            $data[] = [
                $sl++,
                Carbon::parse($r['date'])->format('Y-m-d'),
                $refHtml,
                $r['desc'],
                number_format($r['debit'], 2),
                number_format($r['credit'], 2),
                number_format($balance, 2),
            ];
        }

        // dd($data);

        return response()->json([
            'draw'                 => (int) $request->draw,
            'iTotalRecords'        => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData'               => $data,
        ]);
    }

    public function referenceView(string $type, int $id)
    {
        return match ($type) {

            'sale'           => view(
                'backend.modules.pos.saleDetailsModal',
                ['sale' => Sale::with('items.product')->findOrFail($id)]
            ),

            'sale_payment'   => view(
                'backend.modules.pos.payment_details_modal',
                [
                    'payment' => SalePayment::with('sale')->findOrFail($id),
                ]
            ),

            'sale_return'    => view(
                'backend.modules.sale_returns.show_modal',
                ['saleReturn' => SaleReturn::with('items.product')->findOrFail($id)]
            ),

            'sale_return_payment' => view(
                'backend.modules.sale_returns.refund_payment_details_modal',
                [
                    'payment' => SaleReturnPayment::with([
                        'saleReturn',
                        'createdBy',
                    ])->findOrFail($id),
                ]
            ),

            default          => abort(404),
        };
    }

    public function summary(Request $request)
    {
        $customerId = $request->customer_id;
        $branchId   = current_branch_id();

        if (! $customerId) {
            return response()->json([
                'total_sale'   => 0,
                'total_paid'   => 0,
                'total_return' => 0,
                'balance'      => 0,
            ]);
        }

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : null;

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : null;

        /* ---------------- Total Sale ---------------- */
        $totalSale = Sale::where('customer_id', $customerId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->sum('total');

        /* ---------------- Total Paid ---------------- */
        $totalPaid = SalePayment::whereHas('sale', function ($q) use ($customerId, $branchId) {
            $q->where('customer_id', $customerId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        })
            ->when($from, fn($q) => $q->whereBetween('paid_at', [$from, $to]))
            ->sum('amount');

        /* ---------------- Total Return ---------------- */
        $totalReturn = SaleReturn::where('customer_id', $customerId)
            ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_refund');

        $balance = round($totalSale - $totalPaid - $totalReturn, 2);

        return response()->json([
            'total_sale'   => round($totalSale, 2),
            'total_paid'   => round($totalPaid, 2),
            'total_return' => round($totalReturn, 2),
            'balance'      => $balance,
        ]);
    }

}
