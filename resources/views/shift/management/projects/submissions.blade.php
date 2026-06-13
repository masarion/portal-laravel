@extends('layouts.app')
@section('title', '提出状況')
@section('nav_projects', 'active')

@section('content')
<div class="page-header d-flex align-items-center gap-3">
  <a href="{{ route('shift.projects.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <h2><i class="bi bi-list-check me-2 text-primary"></i>{{ $project->name }} — 提出状況</h2>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th class="ps-4">氏名</th>
          <th>コード</th>
          <th class="text-center">提出</th>
          <th>提出日時</th>
          <th class="pe-4">備考</th>
        </tr>
      </thead>
      <tbody>
        @foreach($project->staffMembers as $staff)
        @php $sub = $project->submissions->firstWhere('staff_id', $staff->id); @endphp
        <tr>
          <td class="ps-4">{{ $staff->name }}</td>
          <td class="text-muted small">{{ $staff->code ?: '-' }}</td>
          <td class="text-center">
            @if($sub && $sub->submitted)
              <span class="text-success fw-semibold small">提出済</span>
            @else
              <span class="text-secondary small">未提出</span>
            @endif
          </td>
          <td class="text-muted small">{{ $sub?->submitted_at?->format('Y/m/d H:i') ?? '-' }}</td>
          <td class="pe-4 text-muted small">{{ $sub?->notes ?: '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
