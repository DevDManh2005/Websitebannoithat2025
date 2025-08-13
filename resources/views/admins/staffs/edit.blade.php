@extends('admins.layouts.app')
@section('title','Sửa nhân viên')

@section('content')
<h1 class="h4 mb-3">Sửa nhân viên</h1>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
    <ul class="mb-0">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('admin.staffs.update', $staff->id) }}" method="POST" class="card">
  @csrf @method('PUT')
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}" required>
      </div>
    </div>

    <hr class="my-4">

    <div class="d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Phân quyền trực tiếp</h6>
      {{-- Quyền từ vai trò (tham khảo) --}}
      @if($staff->role && $staff->role->permissions->count())
        <div class="small text-muted">
          Quyền từ vai trò <strong>{{ $staff->role->name }}</strong>:
          {{ $staff->role->permissions->map(fn($p)=>$p->module_name.'.'.$p->action)->implode(', ') }}
        </div>
      @endif
    </div>

    @include('admins.staffs._permissions_matrix', ['modules' => $modules, 'assigned' => $assigned])
  </div>

  <div class="card-footer d-flex gap-2">
    <a href="{{ route('admin.staffs.index') }}" class="btn btn-light">Quay lại</a>
    <button class="btn btn-primary">Cập nhật</button>
  </div>
</form>
@endsection
