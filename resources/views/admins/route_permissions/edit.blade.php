@extends('admins.layouts.app')
@section('title','Sửa mapping')

@section('content')
<h1 class="h4 mb-3">Sửa mapping</h1>
@include('admins.shared.flash')

<form method="post" action="{{ route('admin.route-permissions.update', $rp) }}" class="card card-body">
  @csrf @method('PUT')
  @include('admins.route_permissions._form', ['rp' => $rp, 'routes' => $routes])
</form>
@endsection
