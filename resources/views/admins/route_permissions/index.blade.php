@extends('admins.layouts.app')
@section('title','Route ↔ Quyền')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Ánh xạ Route ↔ Quyền</h1>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.route-permissions.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-lg me-1"></i> Thêm mapping
    </a>
  </div>
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
          <th>Route</th>
          <th>Area</th>
          <th>Module</th>
          <th>Action</th>
          <th>Trạng thái</th>
          <th class="text-end">Thao tác</th>
        </tr>
      </thead>
      <tbody>
      @forelse($rows as $rp)
        <tr>
          <td>{{ $rp->id }}</td>
          <td><code>{{ $rp->route_name }}</code></td>
          <td><span class="badge text-bg-{{ $rp->area === 'admin' ? 'dark' : 'secondary' }}">{{ strtoupper($rp->area) }}</span></td>
          <td>{{ $rp->module_name }}</td>
          <td>
            @php
              // Map gọn label action
              $labels = [
                'view' => 'Xem','create' => 'Thêm','update' => 'Cập nhật','delete' => 'Xoá',
                'moderate'=>'Duyệt/Ẩn','ready_to_ship'=>'Sẵn sàng giao','cod_paid'=>'Đã thu COD'
              ];
            @endphp
            <span class="badge text-bg-light">{{ $labels[$rp->action] ?? $rp->action }}</span>
          </td>
          <td>
            @if($rp->is_active)
              <span class="badge text-bg-success">Đang bật</span>
            @else
              <span class="badge text-bg-secondary">Tắt</span>
            @endif
          </td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.route-permissions.edit', $rp) }}">Sửa</a>
            <form action="{{ route('admin.route-permissions.destroy', $rp) }}" method="post" class="d-inline"
                  onsubmit="return confirm('Xóa mapping này?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Xóa</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">Chưa có ánh xạ nào.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    {{ $rows->withQueryString()->links() }}
  </div>
</div>
@endsection
