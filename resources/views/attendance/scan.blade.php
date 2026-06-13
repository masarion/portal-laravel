<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>打刻 - {{ $employee->name }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Inter',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .scan-card { max-width:420px; width:100%; border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,.1); }
    .status-badge { font-size:.8rem; padding:.3rem .75rem; border-radius:99px; }
  </style>
</head>
<body>
<div class="card scan-card p-4">
  <div class="text-center mb-4">
    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px">
      <i class="bi bi-person-check text-primary" style="font-size:2rem"></i>
    </div>
    <h4 class="fw-700 mb-1">{{ $employee->name }}</h4>
    @if($employee->workplace)
      <span class="text-muted small">{{ $employee->workplace->name }}</span>
    @endif
  </div>

  @if(session('message'))
    <div class="alert alert-success text-center py-2 small mb-3">
      <i class="bi bi-check-circle me-1"></i>{{ session('message') }}
    </div>
  @endif

  <div class="bg-light rounded-3 p-3 mb-4 text-center">
    <div class="small text-muted mb-1">本日 {{ now()->format('Y年m月d日') }}</div>
    <div class="row g-3 mt-1">
      <div class="col-6">
        <div class="small text-muted">出勤</div>
        <div class="fw-600 fs-5">{{ $record->check_in ? substr($record->check_in,0,5) : '--:--' }}</div>
      </div>
      <div class="col-6">
        <div class="small text-muted">退勤</div>
        <div class="fw-600 fs-5">{{ $record->check_out ? substr($record->check_out,0,5) : '--:--' }}</div>
      </div>
    </div>
  </div>

  @if($record->check_in && $record->check_out)
    <div class="alert alert-secondary text-center mb-0">
      <i class="bi bi-check2-all me-1"></i>本日の打刻は完了しています
    </div>
  @else
    <form method="POST" action="{{ route('scan.store', $employee->qr_token) }}">
      @csrf
      <button class="btn btn-primary w-100 py-3 fw-600" style="border-radius:12px;font-size:1.1rem">
        @if(!$record->check_in)
          <i class="bi bi-box-arrow-in-right me-2"></i>出勤する
        @else
          <i class="bi bi-box-arrow-right me-2"></i>退勤する
        @endif
      </button>
    </form>
  @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
