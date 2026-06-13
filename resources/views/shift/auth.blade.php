<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $project->name }} - ログイン</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1e40af,#2563eb); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .card { border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.2); padding:2rem; width:100%; max-width:380px; border:none; }
    .logo { width:52px;height:52px;background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;margin:0 auto 1rem; }
    .form-control { border-radius:8px; }
    .btn-primary { background:linear-gradient(135deg,#1e40af,#2563eb); border:none; border-radius:8px; font-weight:600; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo"><i class="bi bi-calendar3"></i></div>
    <h1 class="text-center fw-bold mb-1" style="font-size:1.15rem;">{{ $project->name }}</h1>
    <p class="text-center text-muted small mb-4">スタッフコードとパスワードを入力してください</p>
    @if($isPastDeadline)
      <div class="alert alert-warning small">提出期限（{{ $project->deadline->format('Y/m/d H:i') }}）が過ぎています。</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger small py-2">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('shift.auth.store', $token) }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold small">スタッフコード</label>
        <input type="text" name="code" class="form-control" autofocus required>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold small">パスワード</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100 text-white" {{ $isPastDeadline ? 'disabled' : '' }}>
        <i class="bi bi-box-arrow-in-right me-1"></i>シフト入力へ
      </button>
    </form>
  </div>
</body>
</html>
