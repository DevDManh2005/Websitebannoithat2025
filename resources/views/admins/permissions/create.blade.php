@extends('admins.layouts.app')
@section('title','Thêm quyền')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Thêm quyền</h1>
  </div>

  <form method="POST" action="{{ route('admin.permissions.store') }}" class="card card-body">
    @csrf
    @include('admins.permissions._form')
  </form>
@endsection
