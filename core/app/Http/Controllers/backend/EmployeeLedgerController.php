<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Employee;
use App\Models\backend\EmployeeCurrentBalance;
use App\Models\backend\EmployeeSalary;
use App\Models\backend\EmployeeSalaryPayment;
use Illuminate\Http\Request;

class EmployeeLedgerController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('backend.modules.employee_ledger.index', compact('employees'));
    }

    public function listAjax(Request $req)
    {
        $draw = (int) $req->input('draw', 0);
        $start = (int) $req->input('start', 0);
        $length = (int) $req->input('length', 10);
        $employeeId = $req->input('employee_id');
        $startDate = $req->input('start_date');
        $endDate = $req->input('end_date');

        $rows = collect();

        if ($employeeId) {
            $salaries = EmployeeSalary::where('employee_id', $employeeId);
            if ($startDate) $salaries->whereDate('date', '>=', $startDate);
            if ($endDate) $salaries->whereDate('date', '<=', $endDate);
            foreach ($salaries->get() as $s) {
                $rows->push([
                    'date' => optional($s->date)->format('Y-m-d'),
                    'type' => 'Salary Assigned',
                    'category' => ucfirst($s->salary_type),
                    'method' => '-',
                    'amount' => (float) $s->amount,
                    'direction' => 'In',
                ]);
            }

            $payments = EmployeeSalaryPayment::where('employee_id', $employeeId);
            if ($startDate) $payments->whereDate('date', '>=', $startDate);
            if ($endDate) $payments->whereDate('date', '<=', $endDate);
            foreach ($payments->get() as $p) {
                $rows->push([
                    'date' => optional($p->date)->format('Y-m-d'),
                    'type' => 'Salary Payment',
                    'category' => ucfirst($p->salary_type),
                    'method' => ucfirst(str_replace('_', ' ', $p->payment_method)),
                    'amount' => (float) $p->amount,
                    'direction' => 'Out',
                ]);
            }
        }

        $rows = $rows->sortByDesc('date')->values();
        $total = $rows->count();
        $filtered = $total;
        $paged = $rows->slice($start, $length)->values();

        $assigned = $rows->where('type', 'Salary Assigned')->sum('amount');
        $paid = $rows->where('type', 'Salary Payment')->sum('amount');
        $balance = $employeeId ? (float) (EmployeeCurrentBalance::where('employee_id', $employeeId)->value('current_balance') ?? 0) : 0;

        $data = [];
        foreach ($paged as $r) {
            $data[] = [
                $r['date'],
                $r['type'],
                $r['category'],
                $r['method'],
                number_format($r['amount'], 2),
                $r['direction'],
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
            'summary' => [
                'assigned' => number_format($assigned, 2),
                'paid' => number_format($paid, 2),
                'balance' => number_format($balance, 2),
            ],
        ]);
    }
}
