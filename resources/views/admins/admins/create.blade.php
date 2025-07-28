@extends('admins.layouts.app')

@section('content')
<div class="container">
  <h1>Tạo Admin mới</h1>
  <form action="{{ route('admin.admins.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label>Tên</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}">
    </div>
    <div class="mb-3">
      <label>Mật khẩu</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label>Xác nhận mật khẩu</label>
      <input type="password" name="password_confirmation" class="form-control">
    </div>
    <button class="btn btn-success">Tạo mới</button>
  </form>
</div>
@endsection
