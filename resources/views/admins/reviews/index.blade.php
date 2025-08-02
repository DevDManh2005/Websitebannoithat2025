@extends('admins.layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý Đánh giá Sản phẩm</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Người đánh giá</th>
                            <th>Xếp hạng</th>
                            <th style="width: 30%;">Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.show', $review->product_id) }}">
                                        {{ $review->product->name }}
                                    </a>
                                </td>
                                <td>{{ $review->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                    </span>
                                </td>
                                <td>
                                    {{ $review->review }}
                                    @if($review->image)
                                        <a href="{{ asset('storage/' . $review->image) }}" target="_blank">[Xem ảnh]</a>
                                    @endif
                                </td>
                                <td>
                                    @if($review->status == 'approved')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <form action="{{ route('admin.reviews.toggleStatus', $review) }}" method="POST" class="me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $review->status == 'approved' ? 'btn-secondary' : 'btn-success' }}">
                                                {{ $review->status == 'approved' ? 'Ẩn' : 'Duyệt' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có đánh giá nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection