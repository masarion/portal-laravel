<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function show(string $token)
    {
        $employee = Employee::where('qr_token', $token)->where('is_active', true)->firstOrFail();
        $record   = AttendanceRecord::firstOrCreate(['employee_id' => $employee->id, 'date' => today()]);
        return view('attendance.scan', compact('employee', 'record'));
    }

    public function store(Request $request, string $token)
    {
        $employee = Employee::where('qr_token', $token)->where('is_active', true)->firstOrFail();
        $record   = AttendanceRecord::firstOrCreate(['employee_id' => $employee->id, 'date' => today()]);

        if (!$record->check_in) {
            $record->update(['check_in' => now()->format('H:i:s')]);
            $message = '出勤打刻が完了しました';
        } elseif (!$record->check_out) {
            $record->update(['check_out' => now()->format('H:i:s')]);
            $message = '退勤打刻が完了しました';
        } else {
            $message = '本日の打刻は完了しています';
        }

        return redirect()->route('scan.show', $token)->with('message', $message);
    }
}
