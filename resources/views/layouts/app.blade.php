<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'ポータル') - 業務ポータル</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --primary:#2563eb; --surface:#fff; --border:#e2e8f0; --radius:12px; }
    body { font-family:'Inter',sans-serif; background:#f8fafc; color:#1e293b; }
    .sidebar { width:230px; min-height:100vh; background:#1e293b; position:fixed; top:0; left:0; z-index:100; display:flex; flex-direction:column; }
    .sidebar-brand { padding:1.25rem 1.25rem 0.75rem; color:#fff; font-weight:700; font-size:1rem; border-bottom:1px solid rgba(255,255,255,.08); }
    .sidebar-brand span { color:#60a5fa; }
    .nav-link { color:rgba(255,255,255,.7); border-radius:8px; margin:2px 8px; padding:.5rem .85rem; font-size:.85rem; display:flex; align-items:center; gap:.55rem; transition:background .15s,color .15s; }
    .nav-link:hover, .nav-link.active { background:rgba(255,255,255,.1); color:#fff; }
    .nav-link i { font-size:.95rem; width:18px; text-align:center; }
    .nav-section { font-size:.68rem; color:rgba(255,255,255,.35); padding:.75rem 1.25rem .2rem; text-transform:uppercase; letter-spacing:.06em; }
    .nav-divider { border-top:1px solid rgba(255,255,255,.08); margin:.5rem .75rem; }
    .main { margin-left:230px; padding:2rem; }
    .page-header { margin-bottom:1.5rem; }
    .page-header h2 { font-size:1.25rem; font-weight:700; margin:0; }
    .card { border:1px solid var(--border); border-radius:var(--radius); background:var(--surface); }
    .card-header { padding:.85rem 1.25rem; font-weight:600; font-size:.875rem; background:transparent; border-bottom:1px solid var(--border); }
    .table { font-size:.875rem; }
    .table th { font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; color:#64748b; border-bottom:2px solid var(--border); }
    @yield('extra_style')
  </style>
</head>
<body>
<div class="sidebar">
  <div class="sidebar-brand">
    <a href="{{ route('home') }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
      <i class="bi bi-grid-3x3-gap"></i>業務<span>ポータル</span>
    </a>
  </div>
  <nav class="mt-2 flex-grow-1">
    <div class="nav-section">シフト管理</div>
    <a href="{{ route('shift.dashboard') }}" class="nav-link @yield('nav_shift_dashboard')">
      <i class="bi bi-speedometer2"></i>ダッシュボード
    </a>
    <a href="{{ route('shift.projects.index') }}" class="nav-link @yield('nav_projects')">
      <i class="bi bi-calendar3"></i>案件一覧
    </a>
    <a href="{{ route('shift.projects.create') }}" class="nav-link @yield('nav_create')">
      <i class="bi bi-plus-circle"></i>新規案件
    </a>

    <div class="nav-divider"></div>
    <div class="nav-section">勤怠管理</div>
    <a href="{{ route('attendance.dashboard') }}" class="nav-link @yield('nav_att_dashboard')">
      <i class="bi bi-clipboard-pulse"></i>ダッシュボード
    </a>
    <a href="{{ route('attendance.records.index') }}" class="nav-link @yield('nav_att_records')">
      <i class="bi bi-table"></i>勤怠一覧
    </a>
    <a href="{{ route('attendance.employees.index') }}" class="nav-link @yield('nav_att_employees')">
      <i class="bi bi-people"></i>スタッフ一覧
    </a>
    <a href="{{ route('attendance.employees.create') }}" class="nav-link @yield('nav_att_employee_create')">
      <i class="bi bi-person-plus"></i>スタッフ登録
    </a>
    <a href="{{ route('attendance.workplaces.index') }}" class="nav-link @yield('nav_att_workplaces')">
      <i class="bi bi-building"></i>所属場所
    </a>
  </nav>
  <div class="p-3" style="border-top:1px solid rgba(255,255,255,.08)">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="nav-link w-100 border-0 bg-transparent text-start">
        <i class="bi bi-box-arrow-left"></i>ログアウト
      </button>
    </form>
  </div>
</div>
<div class="main">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 small mb-3">
      <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2 small mb-3">
      <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
