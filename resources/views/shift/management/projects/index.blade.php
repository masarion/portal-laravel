@extends('layouts.app')
@section('title', '案件一覧')
@section('nav_projects', 'active')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
  <h2><i class="bi bi-calendar3 me-2 text-primary"></i>案件一覧</h2>
  <a href="{{ route('shift.projects.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>新規案件</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th class="ps-4">案件名</th>
          <th>対象期間</th>
          <th>提出期限</th>
          <th class="text-center">スタッフ</th>
          <th class="text-center">提出済</th>
          <th class="pe-4 text-end">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $p)
        <tr>
          <td class="ps-4 fw-semibold">{{ $p->name }}</td>
          <td class="small text-muted">
            {{ $p->start_date ? $p->start_date->format('Y/m/d') : '-' }}
            〜
            {{ $p->end_date ? $p->end_date->format('Y/m/d') : '-' }}
          </td>
          <td class="small {{ $p->isPastDeadline() ? 'text-danger' : 'text-muted' }}">
            {{ $p->deadline->format('Y/m/d H:i') }}
          </td>
          <td class="text-center">{{ $p->staff_members_count }}</td>
          <td class="text-center">{{ $p->submissions_count }}</td>
          <td class="pe-4 text-end">
            <a href="{{ route('shift.projects.edit', $p) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
            <a href="{{ route('shift.projects.submissions', $p) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-list-check"></i></a>
            <form method="POST" action="{{ route('shift.projects.destroy', $p) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">案件が登録されていません</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
