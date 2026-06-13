@extends('layouts.app')
@section('title', '勤怠一覧')
@section('nav_att_records', 'active')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <h2>勤怠一覧</h2>
  <a href="{{ route('attendance.records.export', request()->query()) }}" class="btn btn-success btn-sm">
    <i class="bi bi-file-earmark-excel me-1"></i>Excel出力
  </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-auto">
        <label class="form-label small mb-1">日付（開始）</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
      </div>
      <div class="col-auto">
        <label class="form-label small mb-1">日付（終了）</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
      </div>
      <div class="col-auto">
        <label class="form-label small mb-1">所属</label>
        <select name="workplace_id" class="form-select form-select-sm">
          <option value="">すべて</option>
          @foreach($workplaces as $w)
            <option value="{{ $w->id }}" @selected(request('workplace_id')==$w->id)>{{ $w->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label small mb-1">状態</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">すべて</option>
          <option value="completed" @selected(request('status')=='completed')>退勤済み</option>
          <option value="working"   @selected(request('status')=='working')>出勤中</option>
          <option value="absent"    @selected(request('status')=='absent')>未出勤</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary btn-sm">絞り込み</button>
        <a href="{{ route('attendance.records.index') }}" class="btn btn-outline-secondary btn-sm ms-1">リセット</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr>
        <th>日付</th><th>氏名</th><th>所属</th><th>出勤</th><th>退勤</th><th>勤務時間</th><th>状態</th>
      </tr></thead>
      <tbody>
      @forelse($records as $r)
        <tr>
          <td>{{ $r->date }}</td>
          <td>{{ $r->employee->name }}</td>
          <td class="text-muted small">{{ $r->employee->workplace?->name ?? '-' }}</td>
          <td>{{ $r->check_in  ? substr($r->check_in,0,5)  : '-' }}</td>
          <td>{{ $r->check_out ? substr($r->check_out,0,5) : '-' }}</td>
          <td>{{ $r->work_duration }}</td>
          <td>
            @if($r->check_in && $r->check_out)
              <span class="text-success fw-500">退勤済み</span>
            @elseif($r->check_in)
              <span class="text-primary fw-500">出勤中</span>
            @else
              <span class="text-muted">未出勤</span>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted py-4">記録がありません</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($records->hasPages())
    <div class="card-footer">{{ $records->links() }}</div>
  @endif
</div>
@endsection
