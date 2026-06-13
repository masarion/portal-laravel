<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Workplace;
use Illuminate\Http\Request;

class WorkplaceController extends Controller
{
    public function index()
    {
        $workplaces = Workplace::withCount('employees')->orderBy('order')->orderBy('name')->get();
        return view('attendance.management.workplaces.index', compact('workplaces'));
    }

    public function create()
    {
        return view('attendance.management.workplaces.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:100','order'=>'integer|min:0']);
        Workplace::create($data);
        return redirect()->route('attendance.workplaces.index')->with('success', '所属場所を登録しました');
    }

    public function edit(Workplace $workplace)
    {
        return view('attendance.management.workplaces.form', compact('workplace'));
    }

    public function update(Request $request, Workplace $workplace)
    {
        $data = $request->validate(['name'=>'required|string|max:100','order'=>'integer|min:0']);
        $workplace->update($data);
        return redirect()->route('attendance.workplaces.index')->with('success', '所属場所を更新しました');
    }

    public function destroy(Workplace $workplace)
    {
        $workplace->delete();
        return redirect()->route('attendance.workplaces.index')->with('success', '所属場所を削除しました');
    }

    public function show(Workplace $workplace)
    {
        return redirect()->route('attendance.workplaces.edit', $workplace);
    }
}
