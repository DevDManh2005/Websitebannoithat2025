@extends('admins.layouts.app')
@section('title','Thêm ánh xạ Route ↔ Quyền')

@section('content')
<h1 class="h4 mb-3">Thêm ánh xạ Route ↔ Quyền</h1>

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
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.route-permissions.store') }}" class="card card-body">
  @csrf
  @include('admins.route_permissions._form')
</form>
@endsection
