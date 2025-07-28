@extends('admins.layouts.app')

@section('title', 'Thêm nhà cung cấp')

@section('content')
<h1 class="mb-4">Thêm nhà cung cấp</h1>
<form action="{{ route('admin.suppliers.store') }}" method="POST">
  @csrf
  <div class="mb-3">
    <label class="form-label">Tên</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Người liên hệ</label>
    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Điện thoại</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Địa chỉ</label>
    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
  </div>
  <button class="btn btn-primary">Lưu</button>
  <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
