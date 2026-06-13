@extends('layouts.app')
@section('title', '所属場所')
@section('nav_att_workplaces', 'active')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <h2>所属場所</h2>
  <a href="{{ route('attendance.workplaces.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i>新規登録
  </a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>場所名</th><th>表示順</th><th>スタッフ数</th><th></th></tr></thead>
      <tbody>
      @forelse($workplaces as $w)
        <tr>
          <td>{{ $w->name }}</td>
          <td class="text-muted">{{ $w->order }}</td>
          <td>{{ $w->employees_count }}名</td>
          <td class="text-end">
            <a href="{{ route('attendance.workplaces.edit', $w) }}" class="btn btn-outline-primary btn-sm py-0 px-2">編集</a>
            <form method="POST" action="{{ route('attendance.workplaces.destroy', $w) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm py-0 px-2 ms-1">削除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-center text-muted py-4">所属場所がありません</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
