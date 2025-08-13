@extends('admins.layouts.app')
@section('title','Sửa quyền')

@section('content')
<h1 class="h4 mb-3">Sửa quyền</h1>

@include('admins.shared.flash')

<form method="POST" action="{{ route('admin.permissions.update', $permission) }}" class="card card-body">
  @csrf @method('PUT')
  @include('admins.permissions._form', ['permission' => $permission])
</form>
@endsection
