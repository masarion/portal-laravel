<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Workplace;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('workplace')->orderBy('employee_number');
        if ($request->filled('workplace_id')) $query->where('workplace_id', $request->workplace_id);
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('name', 'like', '%'.$request->search.'%')->orWhere('employee_number', 'like', '%'.$request->search.'%'));
        }
        if ($request->filled('is_active')) $query->where('is_active', $request->is_active);
        $employees  = $query->paginate(30)->withQueryString();
        $workplaces = Workplace::orderBy('order')->orderBy('name')->get();
        return view('attendance.management.employees.index', compact('employees', 'workplaces'));
    }

    public function create()
    {
        $workplaces = Workplace::orderBy('order')->orderBy('name')->get();
        return view('attendance.management.employees.form', compact('workplaces'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:100','employee_number'=>'required|string|max:20|unique:employees','workplace_id'=>'nullable|exists:workplaces,id','is_active'=>'boolean']);
        Employee::create($data);
        return redirect()->route('attendance.employees.index')->with('success', 'スタッフを登録しました');
    }

    public function edit(Employee $employee)
    {
        $workplaces = Workplace::orderBy('order')->orderBy('name')->get();
        return view('attendance.management.employees.form', compact('employee', 'workplaces'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate(['name'=>'required|string|max:100','employee_number'=>'required|string|max:20|unique:employees,employee_number,'.$employee->id,'workplace_id'=>'nullable|exists:workplaces,id','is_active'=>'boolean']);
        $employee->update($data);
        return redirect()->route('attendance.employees.index')->with('success', 'スタッフ情報を更新しました');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('attendance.employees.index')->with('success', 'スタッフを削除しました');
    }

    public function show(Employee $employee)
    {
        return redirect()->route('attendance.employees.edit', $employee);
    }

    public function qr(Employee $employee)
    {
        $scanUrl = route('scan.show', $employee->qr_token);
        return view('attendance.management.employees.qr', compact('employee', 'scanUrl'));
    }

    public function import(Request $request)
    {
        $request->validate(['csv_text' => 'required|string']);
        $lines = preg_split('/\r\n|\r|\n/', trim($request->csv_text));
        $added = 0;
        foreach ($lines as $line) {
            $line = trim($line); if (!$line) continue;
            $delim = str_contains($line, "\t") ? "\t" : ',';
            $parts = explode($delim, $line);
            $number = trim($parts[0] ?? ''); $name = trim($parts[1] ?? $number);
            if (!$name || Employee::where('employee_number', $number)->exists()) continue;
            Employee::create(['name'=>$name,'employee_number'=>$number]); $added++;
        }
        return redirect()->route('attendance.employees.index')->with('success', "{$added}名をインポートしました");
    }
}
