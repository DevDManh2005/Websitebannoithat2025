@extends('admins.layouts.app')
@section('title','Thêm mapping')

@section('content')
<h1 class="h4 mb-3">Thêm mapping</h1>
@include('admins.shared.flash')

<form method="post" action="{{ route('admin.route-permissions.store') }}" class="card card-body">
  @include('admins.route_permissions._form', ['rp' => $rp, 'routes' => $routes])
</form>
@endsection
