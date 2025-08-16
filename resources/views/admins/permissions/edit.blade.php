@extends('admins.layouts.app')
@section('title','Sửa quyền')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Sửa quyền</h1>
  </div>

  <form method="POST" action="{{ route('admin.permissions.update', $permission) }}" class="card card-body">
    @csrf @method('PUT')
    @include('admins.permissions._form', ['permission' => $permission])
  </form>
@endsection
