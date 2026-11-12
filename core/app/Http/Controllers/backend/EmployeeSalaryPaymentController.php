<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use App\Models\backend\BranchAccount;
use App\Models\backend\Employee;
use App\Models\backend\EmployeeAccountLedger;
use App\Models\backend\EmployeeCurrentBalance;
use App\Models\backend\EmployeeSalaryPayment;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryPaymentController extends Controller
{
    public function index()
    {
        return view('backend.modules.employee_salary_payments.index');
    }

    public function listAjax(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = EmployeeSalaryPayment::query()->with('employee:id,name');

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
        foreach ($rows as $p) {
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-payment-delete" data-url="' . route('employee.salary-payments.destroy', $p->id) . '" data-id="' . $p->id . '" title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $p->id,
                e($p->employee?->name ?? 'N/A'),
                ucfirst($p->salary_type),
                ucfirst(str_replace('_', ' ', $p->payment_method)),
                number_format($p->amount, 2),
                optional($p->date)->format('F j Y'),
                optional($p->created_at)->format('F j Y'),
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
        $branchId = current_branch_id();
        $employees = Employee::query()
            ->leftJoin('employee_current_balances as b', 'b.employee_id', '=', 'employees.id')
            ->select('employees.id', 'employees.name', DB::raw('COALESCE(b.current_balance, 0) as current_balance'))
            ->orderBy('employees.name')
            ->get();

        $balances = $employees->mapWithKeys(function ($e) {
            return [(string) $e->id => (float) $e->current_balance];
        });

        $accounts = BranchAccount::with('account:id,name,bank_name')
            ->where('branch_id', $branchId)
            ->where('is_active', 1)
            ->get();

        return view('backend.modules.employee_salary_payments.create_modal', compact('employees', 'balances', 'accounts'));
    }

public function store(Request $req)
    {
        $branchId = current_branch_id();
        if (! $branchId) {
            return response()->json(['ok' => false, 'msg' => 'Please select a branch first'], 422);
        }

        $data = $req->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'account_id' => 'required|integer|exists:accounts,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.salary_type' => 'required|in:salary,bonus,incentive,due,overtime,opening',
            'items.*.payment_method' => 'required|in:cash,bank,mobile_banking',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        $branchAccount = BranchAccount::where('branch_id', $branchId)
            ->where('account_id', $data['account_id'])
            ->where('is_active', 1)
            ->first();

        if (! $branchAccount) {
            return response()->json(['ok' => false, 'msg' => 'Selected account is not available for this branch'], 422);
        }

        $totalPayment = array_sum(array_column($data['items'], 'amount'));

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $payment = EmployeeSalaryPayment::create([
                    'employee_id' => $data['employee_id'],
                    'salary_type' => $item['salary_type'],
                    'payment_method' => $item['payment_method'],
                    'amount' => $item['amount'],
                    'date' => $data['date'],
                ]);

                EmployeeAccountLedger::create([
                    'ref_type' => 'salary_payment',
                    'ref_id' => $payment->id,
                    'direction' => 'out',
                    'amount' => $item['amount'],
                ]);

                $balance = EmployeeCurrentBalance::firstOrCreate(
                    ['employee_id' => $data['employee_id']],
                    ['current_balance' => 0, 'version' => 0]
                );
                $balance->current_balance = $balance->current_balance - $item['amount'];
                $balance->version = ($balance->version ?? 0) + 1;
                $balance->save();

                $journal = JournalEntry::create([
                    'voucher_no' => generateVoucherNo('PAYMENT'),
                    'voucher_type_id' => VoucherType::idByCode('PAYMENT'),
                    'branch_id' => $branchId,
                    'fiscal_year_id' => currentFiscalYear()->id,
                    'source_id' => $payment->id,
                    'source_type' => EmployeeSalaryPayment::class,
                    'entry_date' => $data['date'],
                    'narration' => 'Salary payment',
                    'created_by' => auth()->id(),
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $data['account_id'],
                    'branch_id' => $branchId,
                    'debit' => 0,
                    'credit' => $item['amount'],
                ]);
            }

            DB::commit();
            return response()->json(['ok' => true, 'msg' => 'Employee salary payment recorded successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $payment = EmployeeSalaryPayment::findOrFail($id);
        DB::beginTransaction();
        try {
            EmployeeAccountLedger::where('ref_type', 'salary_payment')
                ->where('ref_id', $payment->id)
                ->delete();

            $journalEntries = JournalEntry::where('source_type', EmployeeSalaryPayment::class)
                ->where('source_id', $payment->id)
                ->get();
            foreach ($journalEntries as $je) {
                JournalEntryLine::where('journal_entry_id', $je->id)->delete();
                $je->delete();
            }

            $balance = EmployeeCurrentBalance::where('employee_id', $payment->employee_id)->first();
            if ($balance) {
                $balance->current_balance = $balance->current_balance + $payment->amount;
                $balance->version = ($balance->version ?? 0) + 1;
                $balance->save();
            }

            $payment->delete();
            DB::commit();
            return response()->json(['ok' => true, 'msg' => 'Payment entry deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}