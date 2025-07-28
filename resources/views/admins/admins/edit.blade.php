@extends('admins.layouts.app')

@section('content')
<div class="container">
  <h1>Chỉnh sửa Admin</h1>
  <form action="{{ route('admin.admins.update',$admin->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
      <label>Tên</label>
      <input type="text" name="name" class="form-control" value="{{ old('name',$admin->name) }}">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email',$admin->email) }}">
    </div>
    <button class="btn btn-primary">Cập nhật</button>
  </form>
</div>
@endsection
