@extends('layouts.app')
@section('title', 'スタッフ一覧')
@section('nav_att_employees', 'active')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <h2>スタッフ一覧</h2>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
      <i class="bi bi-upload me-1"></i>一括インポート
    </button>
    <a href="{{ route('attendance.employees.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-person-plus me-1"></i>新規登録
    </a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-auto">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="氏名・番号で検索" value="{{ request('search') }}">
      </div>
      <div class="col-auto">
        <select name="workplace_id" class="form-select form-select-sm">
          <option value="">所属：すべて</option>
          @foreach($workplaces as $w)
            <option value="{{ $w->id }}" @selected(request('workplace_id')==$w->id)>{{ $w->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto">
        <select name="is_active" class="form-select form-select-sm">
          <option value="">在籍：すべて</option>
          <option value="1" @selected(request('is_active')==='1')>在籍中</option>
          <option value="0" @selected(request('is_active')==='0')>退職済</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary btn-sm">絞り込み</button>
        <a href="{{ route('attendance.employees.index') }}" class="btn btn-outline-secondary btn-sm ms-1">リセット</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr>
        <th>スタッフ番号</th><th>氏名</th><th>所属</th><th>状態</th><th></th>
      </tr></thead>
      <tbody>
      @forelse($employees as $e)
        <tr>
          <td class="text-muted small">{{ $e->employee_number }}</td>
          <td>{{ $e->name }}</td>
          <td class="text-muted small">{{ $e->workplace?->name ?? '-' }}</td>
          <td>
            @if($e->is_active)
              <span class="text-success small">在籍中</span>
            @else
              <span class="text-muted small">退職済</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('attendance.employees.qr', $e) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">QR</a>
            <a href="{{ route('attendance.employees.edit', $e) }}" class="btn btn-outline-primary btn-sm py-0 px-2 ms-1">編集</a>
            <form method="POST" action="{{ route('attendance.employees.destroy', $e) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm py-0 px-2 ms-1">削除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">スタッフがいません</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($employees->hasPages())
    <div class="card-footer">{{ $employees->links() }}</div>
  @endif
</div>

<div class="modal fade" id="importModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('attendance.employees.import') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">スタッフ一括インポート</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted">1行に「スタッフ番号,氏名」またはタブ区切りで貼り付けてください。</p>
          <textarea name="csv_text" class="form-control font-monospace" rows="8" placeholder="E001,山田太郎&#10;E002,佐藤花子" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">キャンセル</button>
          <button class="btn btn-primary btn-sm">インポート</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
