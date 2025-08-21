@extends('admins.layouts.app')
@section('title','Thêm quyền')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Thêm quyền</h1>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.permissions.store') }}" class="card card-soft">
    @csrf
    <div class="card-header"><strong>Thông tin quyền</strong></div>
    <div class="card-body">
      @include('admins.permissions._form')
    </div>
  </form>
</div>
@endsection
