<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $project->name }} - シフト提出</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Inter',sans-serif; background:#f8fafc; }
    .header { background:linear-gradient(135deg,#1e40af,#2563eb); color:#fff; padding:1.25rem 1.5rem; }
    .day-cell { border:1px solid #e2e8f0; border-radius:8px; padding:.5rem; margin-bottom:.5rem; background:#fff; }
    .day-label { font-size:.75rem; font-weight:600; color:#64748b; margin-bottom:.35rem; }
    .shift-select { width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:.35rem .5rem; font-size:.85rem; }
    .shift-select:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
  </style>
</head>
<body>
<div class="header">
  <h1 class="fw-bold mb-0" style="font-size:1.1rem;"><i class="bi bi-calendar3 me-2"></i>{{ $project->name }}</h1>
  @if($project->info_message)
    <p class="mb-0 mt-1 small opacity-75">{{ $project->info_message }}</p>
  @endif
</div>

<div class="container-fluid" style="max-width:600px;padding:1.5rem;">

  @if(session('success'))
    <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</div>
  @endif

  @if($isPastDeadline)
    <div class="alert alert-warning small">提出期限（{{ $project->deadline->format('Y/m/d H:i') }}）が過ぎています。</div>
  @endif

  <form method="POST" action="{{ route('shift.save', $token) }}" id="shiftForm">
    @csrf
    <input type="hidden" name="shift_data" id="shiftDataInput">

    {{-- 名前入力モード --}}
    @if($project->isNameMode())
    <div class="card mb-3">
      <div class="card-body py-3">
        <label class="form-label fw-semibold small">お名前</label>
        <input type="text" name="staff_name" class="form-control" value="{{ $staffName ?? '' }}"
          placeholder="例：山田 太郎" {{ $staff ? 'readonly' : '' }}>
        @if(!$staff && ($staffName ?? ''))
          <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>名前が一致するスタッフが見つかりません</div>
        @endif
      </div>
    </div>
    @else
    <div class="mb-3 small text-muted"><i class="bi bi-person-check me-1"></i>{{ $staff->name ?? '' }} さん</div>
    @endif

    {{-- カレンダー --}}
    @if($project->start_date && $project->end_date)
    <div class="card mb-3">
      <div class="card-header">シフト希望入力</div>
      <div class="card-body">
        @if($project->copy_guide_message)
          <p class="small text-muted mb-3">{{ $project->copy_guide_message }}</p>
        @endif
        @php
          $shiftData = $submission?->shift_data ?? [];
          $days = \Carbon\CarbonPeriod::create($project->start_date, $project->end_date);
          $dayNames = ['日','月','火','水','木','金','土'];
        @endphp
        @foreach($days as $day)
        @php
          $key = $day->format('Y-m-d');
          $dow = (int)$day->format('w');
          $color = $dow === 0 ? 'text-danger' : ($dow === 6 ? 'text-primary' : '');
        @endphp
        <div class="day-cell">
          <div class="day-label {{ $color }}">
            {{ $day->format('m/d') }}（{{ $dayNames[$dow] }}）
          </div>
          <select class="shift-select" data-date="{{ $key }}" onchange="updateShiftData()">
            <option value="">-- 選択 --</option>
            <option value="出勤" {{ ($shiftData[$key] ?? '') === '出勤' ? 'selected' : '' }}>出勤</option>
            <option value="休み" {{ ($shiftData[$key] ?? '') === '休み' ? 'selected' : '' }}>休み</option>
            <option value="午前" {{ ($shiftData[$key] ?? '') === '午前' ? 'selected' : '' }}>午前</option>
            <option value="午後" {{ ($shiftData[$key] ?? '') === '午後' ? 'selected' : '' }}>午後</option>
          </select>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- 備考 --}}
    <div class="card mb-3">
      <div class="card-body py-3">
        <label class="form-label fw-semibold small">備考（任意）</label>
        <textarea name="notes" class="form-control form-control-sm" rows="3"
          placeholder="連絡事項があれば入力してください">{{ $submission?->notes ?? '' }}</textarea>
      </div>
    </div>

    @if($project->confirm_message)
      <p class="small text-muted mb-3">{{ $project->confirm_message }}</p>
    @endif

    <button type="submit" class="btn btn-primary w-100 py-2" {{ $isPastDeadline ? 'disabled' : '' }}>
      <i class="bi bi-send me-1"></i>シフトを提出する
    </button>
  </form>
</div>

<script>
let shiftData = @json($submission?->shift_data ?? []);

function updateShiftData() {
  document.querySelectorAll('.shift-select').forEach(sel => {
    shiftData[sel.dataset.date] = sel.value;
  });
  document.getElementById('shiftDataInput').value = JSON.stringify(shiftData);
}
updateShiftData();
</script>
</body>
</html>
