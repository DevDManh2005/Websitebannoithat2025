@extends('admins.layouts.app')
@section('title','Route → Permission')

@section('content')
<h1 class="h4 mb-3">Route → Permission</h1>

@include('admins.shared.flash')

<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex" method="get">
    <input class="form-control me-2" type="text" name="q" value="{{ $q }}" placeholder="Tìm theo route/module/action">
    <button class="btn btn-outline-secondary">Tìm</button>
  </form>
  <a href="{{ route('admin.route-permissions.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Thêm mapping
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Route name</th>
          <th>Module</th>
          <th>Action</th>
          <th class="text-end">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($perms as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td><code>{{ $p->route_name }}</code></td>
            <td><code>{{ $p->module_name }}</code></td>
            <td><code>{{ $p->action }}</code></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.route-permissions.edit', $p) }}">Sửa</a>
              <form action="{{ route('admin.route-permissions.destroy', $p) }}" method="post" class="d-inline"
                    onsubmit="return confirm('Xóa mapping này?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Xóa</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">Chưa có mapping nào.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $perms->withQueryString()->links() }}</div>
</div>
@endsection
