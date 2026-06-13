@extends('layouts.app')
@section('title', 'QRコード - '.$employee->name)
@section('nav_att_employees', 'active')
@section('content')
<div class="page-header">
  <h2>QRコード</h2>
</div>
<div class="card text-center p-4" style="max-width:400px">
  <h5 class="fw-700 mb-1">{{ $employee->name }}</h5>
  <p class="text-muted small mb-3">{{ $employee->employee_number }}{{ $employee->workplace ? ' · '.$employee->workplace->name : '' }}</p>
  <div class="d-flex justify-content-center mb-3">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($scanUrl) }}" alt="QR" style="border-radius:8px">
  </div>
  <p class="small text-muted text-break mb-3">{{ $scanUrl }}</p>
  <div class="d-flex gap-2 justify-content-center">
    <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer me-1"></i>印刷</button>
    <a href="{{ route('attendance.employees.index') }}" class="btn btn-outline-secondary btn-sm">戻る</a>
  </div>
</div>
@endsection
