@extends('admins.layouts.app')

@section('content')
<h1>Chi tiết danh mục: {{ $category->name }}</h1>

<table class="table table-striped">
    <tr>
        <th>ID</th>
        <td>{{ $category->id }}</td>
    </tr>
    <tr>
        <th>Tên</th>
        <td>{{ $category->name }}</td>
    </tr>
    <tr>
        <th>Slug</th>
        <td>{{ $category->slug }}</td>
    </tr>
    <tr>
        <th>Danh mục cha</th>
        <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
    </tr>
    <tr>
        <th>Trạng thái</th>
        <td>
            @if($category->is_active)
                <span class="badge bg-success">Hiện</span>
            @else
                <span class="badge bg-secondary">Ẩn</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Vị trí</th>
        <td>{{ $category->position }}</td>
    </tr>
    <tr>
        <th>Ngày tạo</th>
        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
        <th>Ngày cập nhật</th>
        <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
    </tr>
</table>

<a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">Sửa</a>
<a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
@endsection
