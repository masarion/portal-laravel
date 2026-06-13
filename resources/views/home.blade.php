@extends('layouts.app')
@section('title', 'ホーム')
@section('extra_style')
.home-card { border-radius:16px; transition:transform .15s,box-shadow .15s; cursor:pointer; text-decoration:none; color:inherit; }
.home-card:hover { transform:translateY(-4px); box-shadow:0 8px 32px rgba(0,0,0,.12); }
.home-icon { width:64px; height:64px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin-bottom:1rem; }
@endsection
@section('content')
<div class="page-header">
  <h2>業務ポータル</h2>
  <p class="text-muted small mb-0">システムを選択してください</p>
</div>
<div class="row g-4" style="max-width:700px">
  <div class="col-sm-6">
    <a href="{{ route('shift.dashboard') }}" class="card home-card p-4 d-block">
      <div class="home-icon bg-primary bg-opacity-10 text-primary">
        <i class="bi bi-calendar3"></i>
      </div>
      <h5 class="fw-700 mb-1">シフト管理</h5>
      <p class="text-muted small mb-0">案件・シフト提出の管理</p>
    </a>
  </div>
  <div class="col-sm-6">
    <a href="{{ route('attendance.dashboard') }}" class="card home-card p-4 d-block">
      <div class="home-icon bg-success bg-opacity-10 text-success">
        <i class="bi bi-clipboard-pulse"></i>
      </div>
      <h5 class="fw-700 mb-1">勤怠管理</h5>
      <p class="text-muted small mb-0">出退勤・スタッフの管理</p>
    </a>
  </div>
</div>
@endsection
