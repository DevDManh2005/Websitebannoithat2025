@extends('admins.layouts.app')

@section('title', 'Quản lý Slide')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Slide Trang chủ</h1>
        <a href="{{ route('admin.slides.create') }}" class="btn btn-primary">Tạo mới</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Vị trí</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slides as $slide)
                        <tr>
                            <td>
                                @if($slide->image)
                                    <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}" width="150">
                                @endif
                            </td>
                            <td>{{ $slide->title }}</td>
                            <td>{{ $slide->position }}</td>
                            <td>
                                @if($slide->is_active) 
                                    <span class="badge bg-success">Hoạt động</span> 
                                @else 
                                    <span class="badge bg-danger">Không hoạt động</span> 
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.slides.edit', $slide) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('admin.slides.destroy', $slide) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa slide này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Chưa có slide nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
