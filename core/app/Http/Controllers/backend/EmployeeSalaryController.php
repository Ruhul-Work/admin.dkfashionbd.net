<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Employee;
use App\Models\backend\EmployeeAccountLedger;
use App\Models\backend\EmployeeCurrentBalance;
use App\Models\backend\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeSalaryController extends Controller
{
    public function index()
    {
        return view('backend.modules.employee_salaries.index');
    }

    public function listAjax(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = EmployeeSalary::query()->with('employee:id,name');

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('salary_type', 'like', "%{$searchVal}%")
                    ->orWhereHas('employee', function ($e) use ($searchVal) {
                        $e->where('name', 'like', "%{$searchVal}%");
                    });
            });
        }

        $filtered = (clone $base)->count();

        $rows = $base->orderBy('id', $orderDir)->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $s) {
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-salary-delete" data-url="' . route('employee.salaries.destroy', $s->id) . '" data-id="' . $s->id . '" title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $s->id,
                e($s->employee?->name ?? 'N/A'),
                ucfirst($s->salary_type),
                number_format($s->amount, 2),
                optional($s->date)->format('F j Y'),
                optional($s->created_at)->format('F j Y'),
                $actions,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'iTotalRecords' => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData' => $data,
        ]);
    }

    public function createModal()
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('backend.modules.employee_salaries.create_modal', compact('employees'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'salary_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.salary_type' => 'required|in:salary,incentive,bonus,overtime,due,opening',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $salary = EmployeeSalary::create([
                    'employee_id' => $data['employee_id'],
                    'salary_type' => $item['salary_type'],
                    'amount' => $item['amount'],
                    'date' => $data['salary_date'],
                ]);

                EmployeeAccountLedger::create([
                    'ref_type' => 'salary_assign',
                    'ref_id' => $salary->id,
                    'direction' => 'in',
                    'amount' => $item['amount'],
                ]);

                $balance = EmployeeCurrentBalance::firstOrCreate(
                    ['employee_id' => $data['employee_id']],
                    ['current_balance' => 0, 'version' => 0]
                );
                $balance->current_balance = $balance->current_balance + $item['amount'];
                $balance->version = ($balance->version ?? 0) + 1;
                $balance->save();
            }

            DB::commit();
            return response()->json(['ok' => true, 'msg' => 'Employee salary assigned successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $salary = EmployeeSalary::findOrFail($id);
        DB::beginTransaction();
        try {
            EmployeeAccountLedger::where('ref_type', 'salary_assign')
                ->where('ref_id', $salary->id)
                ->delete();

            $balance = EmployeeCurrentBalance::where('employee_id', $salary->employee_id)->first();
            if ($balance) {
                $balance->current_balance = $balance->current_balance - $salary->amount;
                if ($balance->current_balance < 0) {
                    $balance->current_balance = 0;
                }
                $balance->version = ($balance->version ?? 0) + 1;
                $balance->save();
            }

            $salary->delete();
            DB::commit();
            return response()->json(['ok' => true, 'msg' => 'Salary entry deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}