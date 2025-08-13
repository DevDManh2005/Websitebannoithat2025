@extends('admins.layouts.app')
@section('title','Quyền')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Quyền (Permissions)</h1>
  <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Thêm quyền
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Module</th>
          <th>Action</th>
          <th class="text-end">Thao tác</th>
        </tr>
      </thead>
      <tbody>
      @forelse($perms as $p)
        <tr>
          <td>{{ $p->id }}</td>
          <td><code>{{ $p->module_name }}</code></td>
          <td><code>{{ $p->action }}</code></td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.permissions.edit', $p) }}">Sửa</a>
            <form action="{{ route('admin.permissions.destroy', $p) }}" method="post" class="d-inline"
                  onsubmit="return confirm('Xóa quyền này?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Xóa</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center text-muted py-4">Chưa có quyền nào.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    {{ $perms->withQueryString()->links() }}
  </div>
</div>
@endsection