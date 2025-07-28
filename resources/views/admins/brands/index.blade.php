@extends('admins.layouts.app')

@section('content')
    <h1 class="mb-4">Danh sách Thương hiệu</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary mb-3">Thêm thương hiệu mới</a>

    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Logo</th>
                <th>Trạng thái</th>
                <th width="200">Hành động</th>
            </tr>
        </thead>
        <tbody>
        @forelse($brands as $brand)
            <tr>
                <td>{{ $brand->id }}</td>
                <td>{{ $brand->name }}</td>
                <td>
                    @if($brand->logo_url)
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="80">
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if($brand->is_active)
                        <span class="badge badge-success">Hoạt động</span>
                    @else
                        <span class="badge badge-secondary">Không hoạt động</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-sm btn-info">Xem</a>
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.brands.destroy', $brand) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu này?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Chưa có thương hiệu nào.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $brands->withQueryString()->links() }}
@endsection
