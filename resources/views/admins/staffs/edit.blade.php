@extends('admins.layouts.app')
@section('title','Sửa nhân viên')

@section('content')
@php
  $moduleLabels = [
    'orders'=>'Đơn hàng','reviews'=>'Đánh giá','products'=>'Sản phẩm','categories'=>'Danh mục',
    'brands'=>'Thương hiệu','suppliers'=>'Nhà cung cấp','inventories'=>'Kho',
    'vouchers'=>'Voucher','slides'=>'Slide','blogs'=>'Bài viết','blog-categories'=>'Chuyên mục',
    'uploads'=>'Tải lên','users'=>'Tài khoản','permissions'=>'Quyền'
  ];
  $inherited = $staff->role && $staff->role->permissions
    ? $staff->role->permissions->pluck('id')->all()
    : [];
@endphp

<style>
  #staff-page .bar{
    border-radius:16px; padding:12px;
    background: linear-gradient(130deg, rgba(196,111,59,.10), rgba(78,107,82,.08) 70%);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #staff-page .card-soft{ border-radius:14px; border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06) }
  #staff-page .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div id="staff-page" class="container-fluid">
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Sửa nhân viên</h1>
      <span class="text-muted small">#{{ $staff->id }} • vai trò: <strong>{{ $staff->role->name ?? '—' }}</strong></span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.staffs.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
      <button form="staff-edit-form" class="btn btn-primary">
        <i class="bi bi-save2 me-1"></i> Cập nhật
      </button>
    </div>
  </div>

  @includeIf('admins.shared.flash')

  <form id="staff-edit-form" action="{{ route('admin.staffs.update', $staff->id) }}" method="POST" novalidate>
    @csrf @method('PUT')

    <div class="card card-soft mb-3">
      <div class="card-header"><strong>Thông tin cơ bản</strong></div>
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
      </div>
    </div>

    <div class="card card-soft">
      <div class="card-header d-flex align-items-center justify-content-between">
        <strong>Phân quyền trực tiếp</strong>
        <span class="text-muted small">Có thể tick/bỏ tất cả; quyền từ vai trò ({{ $staff->role->name ?? 'không có' }}) vẫn áp dụng song song.</span>
      </div>
      <div class="card-body">
        @include('admins.staffs._permissions_matrix', [
          'modules'      => $modules,
          'assigned'     => $assigned,
          'inherited'    => $inherited,   // chỉ tick sẵn; KHÔNG khoá
          'moduleLabels' => $moduleLabels
        ])
      </div>
      <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.staffs.index') }}" class="btn btn-light">Quay lại</a>
        <button class="btn btn-primary">Cập nhật</button>
      </div>
    </div>
  </form>
</div>
@endsection
