@extends('admins.layouts.app')

@section('title', 'Quản lý nhà cung cấp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Nhà cung cấp</h1>
  <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Thêm mới</a>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>Tên</th>
      <th>Liên hệ</th>
      <th>Điện thoại</th>
      <th>Email</th>
      <th>Địa chỉ</th>
      <th>Hành động</th>
    </tr>
  </thead>
  <tbody>
    @foreach($suppliers as $s)
    <tr>
      <td>{{ $s->id }}</td>
      <td>{{ $s->name }}</td>
      <td>{{ $s->contact_name ?? '—' }}</td>
      <td>{{ $s->phone   ?? '—' }}</td>
      <td>{{ $s->email   ?? '—' }}</td>
      <td>{{ $s->address ?? '—' }}</td>
      <td class="d-flex gap-1">
        <a href="{{ route('admin.suppliers.show',   $s->id) }}" class="btn btn-sm btn-primary">Xem</a>
        <a href="{{ route('admin.suppliers.edit',   $s->id) }}" class="btn btn-sm btn-warning">Sửa</a>
        <form action="{{ route('admin.suppliers.destroy', $s->id) }}" method="POST" class="m-0">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa nhà cung cấp này?')">Xóa</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center">
  {{ $suppliers->links() }}
</div>
@endsection
