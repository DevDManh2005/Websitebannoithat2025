@extends('admins.layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Quản lý Người dùng</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th>Tỉnh/Thành</th>
            <th>Quận/Huyện</th>
            <th>Phường/Xã</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->is_active)
                    <span class="badge bg-success">Hoạt động</span>
                @else
                    <span class="badge bg-danger">Khóa</span>
                @endif
            </td>
            <td>{{ optional($user->profile)->province_name ?? '—' }}</td>
            <td>{{ optional($user->profile)->district_name ?? '—' }}</td>
            <td>{{ optional($user->profile)->ward_name ?? '—' }}</td>
            <td>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary">Xem</a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                        {{ $user->is_active ? 'Khóa' : 'Mở khóa' }}
                    </button>
                </form>
                <a href="{{ route('admin.users.logs', $user->id) }}" class="btn btn-sm btn-info">Logs</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $users->links() }}
</div>
@endsection
