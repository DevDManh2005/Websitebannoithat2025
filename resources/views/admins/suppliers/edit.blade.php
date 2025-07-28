@extends('admins.layouts.app')

@section('title', 'Sửa nhà cung cấp')

@section('content')
<h1 class="mb-4">Sửa nhà cung cấp #{{ $supplier->id }}</h1>
<form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
  @csrf @method('PUT')
  <div class="mb-3">
    <label class="form-label">Tên</label>
    <input type="text" name="name" class="form-control" value="{{ old('name',$supplier->name) }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Người liên hệ</label>
    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name',$supplier->contact_name) }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Điện thoại</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone',$supplier->phone) }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email',$supplier->email) }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Địa chỉ</label>
    <input type="text" name="address" class="form-control" value="{{ old('address',$supplier->address) }}">
  </div>
  <button class="btn btn-primary">Cập nhật</button>
  <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
