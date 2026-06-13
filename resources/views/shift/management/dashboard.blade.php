@extends('layouts.app')
@section('title', 'ダッシュボード')
@section('nav_dashboard', 'active')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
  <h2><i class="bi bi-speedometer2 me-2 text-primary"></i>ダッシュボード</h2>
  <a href="{{ route('shift.projects.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>新規案件</a>
</div>

<div class="card">
  <div class="card-header">案件一覧</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th class="ps-4">案件名</th>
          <th>提出期限</th>
          <th class="text-center">スタッフ</th>
          <th class="text-center">提出済</th>
          <th class="text-center">認証モード</th>
          <th class="pe-4 text-end">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $p)
        <tr>
          <td class="ps-4 fw-semibold">{{ $p->name }}</td>
          <td class="small text-muted">{{ $p->deadline->format('Y/m/d H:i') }}</td>
          <td class="text-center">{{ $p->staff_members_count }}</td>
          <td class="text-center">
            <span class="{{ $p->submissions_count > 0 ? 'text-success fw-semibold' : 'text-muted' }}">
              {{ $p->submissions_count }}
            </span>
          </td>
          <td class="text-center small">
            @if($p->auth_mode === 'name')
              <span class="text-primary">名前入力</span>
            @else
              <span class="text-warning">コード＋PW</span>
            @endif
          </td>
          <td class="pe-4 text-end">
            <a href="{{ route('shift.projects.edit', $p) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
            <a href="{{ route('shift.projects.submissions', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-list-check"></i></a>
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
