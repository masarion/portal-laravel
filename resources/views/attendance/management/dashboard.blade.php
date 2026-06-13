@extends('layouts.app')
@section('title', '勤怠ダッシュボード')
@section('nav_att_dashboard', 'active')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2>勤怠ダッシュボード</h2>
    <p class="text-muted small mb-0">{{ $today->format('Y年m月d日 (D)') }}</p>
  </div>
</div>

<div class="row g-3 mb-4">
  @foreach([
    ['label'=>'全スタッフ','value'=>$total,'icon'=>'people','color'=>'primary'],
    ['label'=>'出勤中','value'=>$checkedIn,'icon'=>'person-check','color'=>'success'],
    ['label'=>'退勤済み','value'=>$completed,'icon'=>'check2-all','color'=>'info'],
    ['label'=>'未出勤','value'=>$notYet,'icon'=>'person-dash','color'=>'warning'],
  ] as $s)
  <div class="col-6 col-md-3">
    <div class="card p-3">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-{{ $s['color'] }} bg-opacity-10 text-{{ $s['color'] }} d-flex align-items-center justify-content-center" style="width:48px;height:48px;flex-shrink:0">
          <i class="bi bi-{{ $s['icon'] }}" style="font-size:1.4rem"></i>
        </div>
        <div>
          <div class="small text-muted">{{ $s['label'] }}</div>
          <div class="fw-700 fs-4">{{ $s['value'] }}</div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-clock-history me-2"></i>最近の打刻</span>
    <a href="{{ route('attendance.records.index') }}" class="btn btn-sm btn-outline-secondary">一覧へ</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr>
        <th>氏名</th><th>所属</th><th>出勤</th><th>退勤</th><th>状態</th>
      </tr></thead>
      <tbody>
      @forelse($recentRecords as $r)
        <tr>
          <td>{{ $r->employee->name }}</td>
          <td class="text-muted">{{ $r->employee->workplace?->name ?? '-' }}</td>
          <td>{{ $r->check_in  ? substr($r->check_in,0,5)  : '-' }}</td>
          <td>{{ $r->check_out ? substr($r->check_out,0,5) : '-' }}</td>
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
        <tr><td colspan="5" class="text-center text-muted py-4">本日の打刻記録はありません</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
