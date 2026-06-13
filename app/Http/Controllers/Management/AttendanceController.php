<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Workplace;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceRecord::with('employee.workplace')->orderByDesc('date')->orderBy('employee_id');
        if ($request->filled('date_from'))    $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))      $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('workplace_id')) $query->whereHas('employee', fn($q) => $q->where('workplace_id', $request->workplace_id));
        if ($request->filled('status')) {
            match($request->status) {
                'completed' => $query->whereNotNull('check_in')->whereNotNull('check_out'),
                'working'   => $query->whereNotNull('check_in')->whereNull('check_out'),
                'absent'    => $query->whereNull('check_in'),
                default     => null,
            };
        }
        $records    = $query->paginate(50)->withQueryString();
        $workplaces = Workplace::orderBy('order')->get();
        return view('attendance.management.attendance.index', compact('records', 'workplaces'));
    }

    public function export(Request $request)
    {
        $query = AttendanceRecord::with('employee.workplace')->orderByDesc('date')->orderBy('employee_id');
        if ($request->filled('date_from'))    $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))      $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('workplace_id')) $query->whereHas('employee', fn($q) => $q->where('workplace_id', $request->workplace_id));
        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['日付','氏名','スタッフ番号','所属','出勤','退勤','勤務時間','状態']], null, 'A1');
        foreach ($records as $i => $r) {
            $status = $r->check_in && $r->check_out ? '完了' : ($r->check_in ? '出勤中' : '未出勤');
            $sheet->fromArray([[
                $r->date, $r->employee->name, $r->employee->employee_number,
                $r->employee->workplace?->name ?? '-',
                $r->check_in  ? substr($r->check_in,  0, 5) : '-',
                $r->check_out ? substr($r->check_out, 0, 5) : '-',
                $r->work_duration, $status,
            ]], null, 'A'.($i+2));
        }
        $writer   = new Xlsx($spreadsheet);
        $filename = '勤怠一覧_'.now()->format('Ymd_His').'.xlsx';
        return response()->streamDownload(fn() => $writer->save('php://output'), $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
