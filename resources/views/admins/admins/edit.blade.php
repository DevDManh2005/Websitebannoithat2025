{{-- resources/views/admins/admins/edit.blade.php --}}
@extends('admins.layouts.app')

@section('title','Chỉnh sửa Admin')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 fw-bold mb-0">Chỉnh sửa Admin</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Danh sách
      </a>
    </div>
  </div>

  <form action="{{ route('admin.admins.update',$admin->id) }}" method="POST" class="row g-4">
    @csrf @method('PUT')

    <div class="col-lg-8">
      <div class="card card-soft">
        <div class="card-header">
          <strong>Thông tin tài khoản</strong>
        </div>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
              </ul>
            </div>
          @endif

          <div class="mb-3">
            <label class="form-label">Tên <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name',$admin->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email',$admin->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
          <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
            <i class="bi bi-x-lg me-1"></i> Hủy
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Cập nhật
          </button>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card card-soft">
        <div class="card-header">
          <strong>Thiết lập</strong>
        </div>
        <div class="card-body">
          {{-- Tuỳ DB có cột is_active hay không; controller hiện tại không xử lý field này, gửi cũng không ảnh hưởng --}}
          <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $admin->is_active ?? true))>
            <label class="form-check-label" for="is_active">Kích hoạt</label>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
