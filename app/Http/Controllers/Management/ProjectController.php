<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount(['staffMembers', 'submissions' => fn($q) => $q->where('submitted', true)])
            ->orderByDesc('created_at')->get();
        return view('shift.management.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('shift.management.projects.form');
    }

    public function store(Request $request)
    {
        $project = $this->saveProject(new Project(), $request);
        $this->syncStaff($project, $request);
        return redirect()->route('shift.projects.edit', $project)->with('success', '案件を作成しました');
    }

    public function edit(Project $project)
    {
        $project->load('staffMembers');
        $submitUrl = route('shift.submit', $project->submit_token);
        return view('shift.management.projects.form', compact('project', 'submitUrl'));
    }

    public function update(Request $request, Project $project)
    {
        $this->saveProject($project, $request);
        $this->syncStaff($project, $request);
        return redirect()->route('shift.projects.edit', $project)->with('success', '保存しました');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('shift.projects.index')->with('success', '案件を削除しました');
    }

    public function show(Project $project)
    {
        return redirect()->route('shift.projects.edit', $project);
    }

    public function submissions(Project $project)
    {
        $project->load(['submissions.staff', 'staffMembers']);
        return view('shift.management.projects.submissions', compact('project'));
    }

    private function saveProject(Project $project, Request $request): Project
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'auth_mode'           => 'required|in:name,code',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date',
            'deadline'            => 'required|date',
            'info_message'        => 'nullable|string',
            'copy_guide_message'  => 'nullable|string',
            'confirm_message'     => 'nullable|string',
        ]);
        $project->fill($data);
        $project->save();
        return $project;
    }

    private function syncStaff(Project $project, Request $request): void
    {
        $staffJson = $request->input('staff_json', '[]');
        $staffList = json_decode($staffJson, true) ?? [];

        $existingIds = [];
        foreach ($staffList as $s) {
            $name = trim($s['name'] ?? '');
            if (!$name) continue;

            if (!empty($s['id'])) {
                $staff = Staff::find($s['id']);
                if ($staff && $staff->project_id === $project->id) {
                    $staff->update(['name' => $name, 'code' => $s['code'] ?? '', 'password' => $s['password'] ?? '']);
                    $existingIds[] = $staff->id;
                    continue;
                }
            }
            $new = Staff::create(['project_id' => $project->id, 'name' => $name, 'code' => $s['code'] ?? '', 'password' => $s['password'] ?? '']);
            $existingIds[] = $new->id;
        }

        // 削除されたスタッフを除去
        $project->staffMembers()->whereNotIn('id', $existingIds)->delete();
    }
}
