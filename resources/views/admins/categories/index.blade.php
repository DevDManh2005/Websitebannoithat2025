@extends('admins.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Danh mục</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tạo mới</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục cha</th>
            <th>Trạng thái</th>
            <th>Vị trí</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @forelse($categories as $cat)
        <tr>
            <td>{{ $cat->id }}</td>
            <td>{{ $cat->name }}</td>
            <td>{{ $cat->slug }}</td>
            <td>{{ $cat->parent ? $cat->parent->name : '-' }}</td>
            <td>
                @if($cat->is_active)
                    <span class="badge bg-success">Hiện</span>
                @else
                    <span class="badge bg-secondary">Ẩn</span>
                @endif
            </td>
            <td>{{ $cat->position }}</td>
            <td>{{ $cat->created_at->format('d/m/Y') }}</td>
            <td>
                <a href="{{ route('admin.categories.show', $cat) }}" class="btn btn-sm btn-info">Xem</a>
                <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">Chưa có danh mục nào.</td>
        </tr>
    @endforelse
    </tbody>
</table>

{{ $categories->links() }}
@endsection
