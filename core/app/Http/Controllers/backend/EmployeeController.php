<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('backend.modules.employees.index');
    }

    public function listAjax(Request $request)
    {
        $columns = ['id', 'name', 'emp_id', 'phone', 'email', 'joining_date', 'blood_group', 'created_at'];

        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Employee::query();

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('emp_id', 'like', "%{$searchVal}%")
                    ->orWhere('phone', 'like', "%{$searchVal}%")
                    ->orWhere('email', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $e) {
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="' . route('employee.employees.edit', $e->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-success-focus text-success-main" title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-employee-delete" data-id="' . $e->id . '" data-url="' . route('employee.employees.destroy', $e->id) . '" title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $e->id,
                e($e->name),
                $e->emp_id,
                $e->phone,
                $e->email,
                optional($e->joining_date)->format('Y-m-d'),
                $e->blood_group,
                optional($e->created_at)->format('Y-m-d'),
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

    public function create()
    {
        return view('backend.modules.employees.create');
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'max:150'],
            'emp_id' => ['nullable', 'string', 'max:20', 'unique:employees,emp_id'],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Employee::create($validator->validated());

        return response()->json(['ok' => true, 'message' => 'Employee created successfully']);
    }

    public function edit(Employee $employee)
    {
        return view('backend.modules.employees.edit', compact('employee'));
    }

    public function update(Request $req, Employee $employee)
    {
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'max:150'],
            'emp_id' => ['nullable', 'string', 'max:20', 'unique:employees,emp_id,' . $employee->id],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee->update($validator->validated());

        return response()->json(['ok' => true, 'message' => 'Employee updated successfully']);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['ok' => true, 'message' => 'Employee deleted successfully']);
    }
}