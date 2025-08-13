@extends('admins.layouts.app')
@section('title','Thêm quyền')

@section('content')
<h1 class="h4 mb-3">Thêm quyền</h1>

@include('admins.shared.flash')

<form method="POST" action="{{ route('admin.permissions.store') }}" class="card card-body">
  @csrf
  @include('admins.permissions._form')
</form>
@endsection
