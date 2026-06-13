<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\AttendanceRecord;

class AttendanceDashboardController extends Controller
{
    public function index()
    {
        $today       = today();
        $total       = Employee::where('is_active', true)->count();
        $checkedIn   = AttendanceRecord::whereDate('date', $today)->whereNotNull('check_in')->whereNull('check_out')->count();
        $completed   = AttendanceRecord::whereDate('date', $today)->whereNotNull('check_in')->whereNotNull('check_out')->count();
        $notYet      = $total - $checkedIn - $completed;
        $recentRecords = AttendanceRecord::with('employee.workplace')
            ->whereDate('date', $today)->orderByDesc('updated_at')->limit(20)->get();
        return view('attendance.management.dashboard', compact('total', 'checkedIn', 'completed', 'notYet', 'recentRecords', 'today'));
    }
}
