@extends('admins.layouts.app')

@section('content')
<div class="container">
  <h1>Quản lý Admin</h1>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <a href="{{ route('admin.admins.create') }}" class="btn btn-primary mb-3">+ Thêm Admin</a>
  <table class="table table-hover">
    <thead><tr><th>#</th><th>Tên</th><th>Email</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
    <tbody>
      @foreach($admins as $a)
        <tr>
          <td>{{ $a->id }}</td>
          <td>{{ $a->name }}</td>
          <td>{{ $a->email }}</td>
          <td>{{ $a->created_at->format('d/m/Y') }}</td>
          <td>
            <a href="{{ route('admin.admins.edit',$a->id) }}" class="btn btn-sm btn-warning">Sửa</a>
            <form action="{{ route('admin.admins.destroy',$a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa Admin?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Xóa</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  {{ $admins->links() }}
</div>
@endsection
