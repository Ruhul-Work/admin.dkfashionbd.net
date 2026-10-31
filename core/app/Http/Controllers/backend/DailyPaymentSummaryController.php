<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyPaymentSummaryController extends Controller
{
    public function index()
    {
        return view('backend.modules.pos.reports.daily_payment_summary');
    }

    public function listAjax(Request $request)
    {
        $draw   = (int) $request->input('draw');
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $branchId = current_branch_id();

        /*
    |--------------------------------------------------------------------------
    | 1️⃣ RAW aggregation (DB only does SUM)
    |--------------------------------------------------------------------------
    | sale_payments is source of truth
    | paid_at date is used
    | payment_types joined for slug
    */
        $rows = SalePayment::query()
            ->join('payment_types', 'payment_types.id', '=', 'sale_payments.payment_type_id')
            ->join('sales', 'sales.id', '=', 'sale_payments.sale_id')
            ->where('sales.branch_id', $branchId)
            ->where('payment_types.is_active', 1)
            ->select(
                DB::raw('DATE(sale_payments.paid_at) as pay_date'),
                'payment_types.slug as payment_type',
                DB::raw('SUM(sale_payments.amount) as amount')
            )
            ->groupBy(
                DB::raw('DATE(sale_payments.paid_at)'),
                'payment_types.slug'
            )
            ->orderBy(DB::raw('DATE(sale_payments.paid_at)'), 'desc')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 2️⃣ PHP-side reshape (date wise)
    |--------------------------------------------------------------------------
    */
        $grouped = [];

        foreach ($rows as $r) {
            $date = $r->pay_date;

            if (! isset($grouped[$date])) {
                $grouped[$date] = [
                    'cash'  => 0,
                    'bkash' => 0,
                    'card'  => 0,
                    'total' => 0,
                ];
            }

            $grouped[$date][$r->payment_type] += (float) $r->amount;
            $grouped[$date]['total']          += (float) $r->amount;
        }

        /*
    |--------------------------------------------------------------------------
    | 3️⃣ Pagination (PHP-level, safe)
    |--------------------------------------------------------------------------
    */
        $dates        = array_keys($grouped);
        $totalRecords = count($dates);

        $dates = array_slice($dates, $start, $length, true);

        /*
    |--------------------------------------------------------------------------
    | 4️⃣ Build DataTable rows
    |--------------------------------------------------------------------------
    */
        $data = [];
        $sl   = $start + 1;

        foreach ($dates as $date) {
            $row = $grouped[$date];

            $data[] = [
                $sl++,
                $date,
                number_format($row['cash'], 2),
                number_format($row['bkash'], 2),
                number_format($row['card'], 2),
                number_format($row['total'], 2),
                '<a href="#"
               class="btn btn-sm btn-success AjaxModal"
               data-ajax-modal="' . route('account-transfers.create', ['date' => $date]) . '" title="Fund Transfer">
                 <iconify-icon icon="mdi:bank-transfer" class="menu-icon text-xl"></iconify-icon> 
             </a>',
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | 5️⃣ DataTable response
    |--------------------------------------------------------------------------
    */
        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'aaData'               => $data,
        ]);
    }

}
