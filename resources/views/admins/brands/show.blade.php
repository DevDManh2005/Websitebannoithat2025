@extends('admins.layouts.app')

@section('content')
    <h1 class="mb-4">Chi tiết Thương hiệu</h1>

    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <td>{{ $brand->id }}</td>
        </tr>
        <tr>
            <th>Tên</th>
            <td>{{ $brand->name }}</td>
        </tr>
        <tr>
            <th>Slug</th>
            <td>{{ $brand->slug }}</td>
        </tr>
        <tr>
            <th>Logo</th>
            <td>
                @if($brand->logo_url)
                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="150">
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>
                @if($brand->is_active)
                    <span class="badge badge-success">Hoạt động</span>
                @else
                    <span class="badge badge-secondary">Không hoạt động</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Ngày tạo</th>
            <td>{{ $brand->created_at }}</td>
        </tr>
        <tr>
            <th>Ngày cập nhật</th>
            <td>{{ $brand->updated_at }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Quay lại</a>
    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary">Sửa</a>
@endsection
