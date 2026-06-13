@extends('layouts.app')
@section('title', isset($workplace) ? '所属場所 編集' : '所属場所 登録')
@section('nav_att_workplaces', 'active')
@section('content')
<div class="page-header">
  <h2>{{ isset($workplace) ? '所属場所 編集' : '所属場所 登録' }}</h2>
</div>
<div class="card" style="max-width:420px">
  <div class="card-body p-4">
    <form method="POST" action="{{ isset($workplace) ? route('attendance.workplaces.update', $workplace) : route('attendance.workplaces.store') }}">
      @csrf
      @if(isset($workplace)) @method('PUT') @endif

      <div class="mb-3">
        <label class="form-label fw-500">場所名 <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $workplace->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-4">
        <label class="form-label fw-500">表示順</label>
        <input type="number" name="order" class="form-control" min="0"
               value="{{ old('order', $workplace->order ?? 0) }}">
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ isset($workplace) ? '更新' : '登録' }}</button>
        <a href="{{ route('attendance.workplaces.index') }}" class="btn btn-outline-secondary">戻る</a>
      </div>
    </form>
  </div>
</div>
@endsection
