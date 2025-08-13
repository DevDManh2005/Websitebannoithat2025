@extends('admins.layouts.app')
@section('title','Thêm nhân viên')

@section('content')
<h1 class="h4 mb-3">Thêm nhân viên</h1>

@if($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
    <ul class="mb-0">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('admin.staffs.store') }}" method="POST" class="card">
  @csrf
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nhập lại mật khẩu <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
    </div>

    <hr class="my-4">

    <h6 class="mb-2">Phân quyền trực tiếp</h6>
    @include('admins.staffs._permissions_matrix', ['modules' => $modules, 'assigned' => []])
  </div>

  <div class="card-footer d-flex gap-2">
    <a href="{{ route('admin.staffs.index') }}" class="btn btn-light">Hủy</a>
    <button class="btn btn-primary">Lưu</button>
  </div>
</form>
@endsection
