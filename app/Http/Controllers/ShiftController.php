<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Submission;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function show(Request $request, string $token)
    {
        $project = Project::where('submit_token', $token)->firstOrFail();
        $isPastDeadline = $project->isPastDeadline();

        // コードモードは認証画面へ
        if ($project->auth_mode === Project::AUTH_CODE) {
            $staffId = session("shift_auth_{$token}");
            if (!$staffId) return redirect()->route('shift.auth', $token);
            $staff = Staff::find($staffId);
            if (!$staff) return redirect()->route('shift.auth', $token);
            $submission = Submission::firstOrCreate(['project_id' => $project->id, 'staff_id' => $staff->id]);
            return view('shift.submit', compact('project', 'staff', 'submission', 'isPastDeadline', 'token'));
        }

        // 名前モード：セッションから名前を取得
        $staffName = session("shift_name_{$token}", '');
        $staff = null;
        $submission = null;

        if ($staffName) {
            $normalized = Staff::normalizeName($staffName);
            $staff = $project->staffMembers->first(function ($s) use ($normalized) {
                return Staff::normalizeName($s->name) === $normalized;
            });
            if ($staff) {
                $submission = Submission::firstOrCreate(['project_id' => $project->id, 'staff_id' => $staff->id]);
            }
        }

        return view('shift.submit', compact('project', 'staff', 'submission', 'isPastDeadline', 'token', 'staffName'));
    }

    public function authForm(string $token)
    {
        $project = Project::where('submit_token', $token)->firstOrFail();
        $isPastDeadline = $project->isPastDeadline();
        return view('shift.auth', compact('project', 'token', 'isPastDeadline'));
    }

    public function authStore(Request $request, string $token)
    {
        $project = Project::where('submit_token', $token)->firstOrFail();
        $code     = trim($request->input('code', ''));
        $password = trim($request->input('password', ''));

        $staff = $project->staffMembers()
            ->where('code', $code)
            ->where('password', $password)
            ->first();

        if (!$staff) {
            return back()->with('error', 'コードまたはパスワードが正しくありません');
        }

        session(["shift_auth_{$token}" => $staff->id]);
        return redirect()->route('shift.submit', $token);
    }

    public function save(Request $request, string $token)
    {
        $project = Project::where('submit_token', $token)->firstOrFail();

        if ($project->auth_mode === Project::AUTH_CODE) {
            $staffId = session("shift_auth_{$token}");
            $staff   = Staff::find($staffId);
        } else {
            $staffName = trim($request->input('staff_name', ''));
            session(["shift_name_{$token}" => $staffName]);
            $normalized = Staff::normalizeName($staffName);
            $staff = $project->staffMembers->first(fn($s) => Staff::normalizeName($s->name) === $normalized);
        }

        if (!$staff) {
            return back()->with('error', 'スタッフが見つかりません。名前を確認してください。');
        }

        $shiftData = $request->input('shift_data', '{}');
        $shiftArr  = json_decode($shiftData, true) ?? [];

        // 全日付が入力済みかチェック
        if ($project->start_date && $project->end_date) {
            $start = $project->start_date->copy();
            $end   = $project->end_date->copy();
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                if (!isset($shiftArr[$key]) || $shiftArr[$key] === '') {
                    return back()->with('error', 'すべての日付のシフトを入力してから送信してください。')->withInput();
                }
            }
        }

        $submission = Submission::updateOrCreate(
            ['project_id' => $project->id, 'staff_id' => $staff->id],
            ['submitted' => true, 'submitted_at' => now(), 'shift_data' => $shiftArr, 'notes' => $request->input('notes', '')]
        );

        return redirect()->route('shift.submit', $token)->with('success', 'シフトを提出しました。ありがとうございます。');
    }
}
