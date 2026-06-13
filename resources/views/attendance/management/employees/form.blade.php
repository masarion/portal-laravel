@extends('layouts.app')
@section('title', isset($employee) ? 'スタッフ編集' : 'スタッフ登録')
@section('nav_att_employees', 'active')
@section('content')
<div class="page-header">
  <h2>{{ isset($employee) ? 'スタッフ編集' : 'スタッフ登録' }}</h2>
</div>
<div class="card" style="max-width:560px">
  <div class="card-body p-4">
    <form method="POST" action="{{ isset($employee) ? route('attendance.employees.update', $employee) : route('attendance.employees.store') }}">
      @csrf
      @if(isset($employee)) @method('PUT') @endif

      <div class="mb-3">
        <label class="form-label fw-500">氏名 <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $employee->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">スタッフ番号 <span class="text-danger">*</span></label>
        <input type="text" name="employee_number" class="form-control @error('employee_number') is-invalid @enderror"
               value="{{ old('employee_number', $employee->employee_number ?? '') }}" required>
        @error('employee_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-500">所属場所</label>
        <select name="workplace_id" class="form-select">
          <option value="">未設定</option>
          @foreach($workplaces as $w)
            <option value="{{ $w->id }}" @selected(old('workplace_id', $employee->workplace_id ?? '') == $w->id)>{{ $w->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                 @checked(old('is_active', $employee->is_active ?? true))>
          <label class="form-check-label" for="isActive">在籍中</label>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ isset($employee) ? '更新' : '登録' }}</button>
        <a href="{{ route('attendance.employees.index') }}" class="btn btn-outline-secondary">戻る</a>
      </div>
    </form>
  </div>
</div>
@endsection
