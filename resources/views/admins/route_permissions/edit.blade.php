@extends('admins.layouts.app')
@section('title','Sửa ánh xạ tuyến ↔ quyền')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0 fw-bold">Sửa ánh xạ tuyến ↔ quyền</h1>
  <a href="{{ route('admin.route-permissions.index') }}" class="btn btn-light">Quay lại</a>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Có lỗi xảy ra:</div>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.route-permissions.update', $routePermission) }}" class="card card-soft">
  @csrf @method('PUT')
  <div class="card-header"><strong>Thông tin ánh xạ</strong></div>
  <div class="card-body">
    @include('admins.route_permissions._form', ['routePermission' => $routePermission])
  </div>
</form>
@endsection
