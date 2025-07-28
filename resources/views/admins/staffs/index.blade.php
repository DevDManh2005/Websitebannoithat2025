@extends('admins.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Danh sách Nhân viên</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary mb-3">+ Thêm nhân viên</a>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Permissions</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffs as $staff)
                <tr>
                    <td>{{ $staff->id }}</td>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        @foreach($staff->permissions as $perm)
                            <span class="badge bg-secondary">{{ $perm->module_name }}:{{ $perm->action }}</span>
                        @endforeach
                    </td>
                    <td>{{ $staff->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.staffs.edit', $staff->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('admin.staffs.destroy', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Chắc chắn xóa?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $staffs->links() }}
</div>
@endsection
