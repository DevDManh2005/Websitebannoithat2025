{{-- resources/views/admins/admins/create.blade.php --}}
@extends('admins.layouts.app')

@section('title','Thêm Admin')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 fw-bold mb-0">Thêm Admin</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Danh sách
      </a>
    </div>
  </div>

  <form action="{{ route('admin.admins.store') }}" method="POST" class="row g-4">
    @csrf

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
                   value="{{ old('name') }}" placeholder="Nguyễn Văn A" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="admin@example.com" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                     minlength="6" placeholder="••••••" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('password', this)">
                <i class="bi bi-eye"></i>
              </button>
              @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-text">Tối thiểu 6 ký tự.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                     minlength="6" placeholder="••••••" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('password_confirmation', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
          <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
            <i class="bi bi-x-lg me-1"></i> Hủy
          </a>
          <button class="btn btn-success">
            <i class="bi bi-save me-1"></i> Tạo mới
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
          <div class="mb-3 form-check form-switch">
            {{-- Tuỳ DB có cột is_active hay không; gửi cũng không ảnh hưởng controller hiện tại --}}
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label" for="is_active">Kích hoạt ngay</label>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
  function togglePwd(id, btn){
    const inp = document.getElementById(id);
    const icon = btn.querySelector('i');
    if(inp.type === 'password'){ inp.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
    else{ inp.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
  }
</script>
@endpush
@endsection
