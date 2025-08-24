@extends('admins.layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('content')
@php
    // Tổng số để hiển thị: LengthAwarePaginator có total()
    $totalReviews = method_exists($reviews,'total') ? $reviews->total() : $reviews->count();
@endphp

<style>
    .card-soft { border-radius: 16px; border: 1px solid rgba(32,25,21,.08); }
    .card-soft .card-header { background: transparent; border-bottom: 1px dashed rgba(32,25,21,.12); }
    .filter-bar {
        border-radius: 16px; padding: 12px;
        background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), #fff;
        border: 1px solid rgba(32,25,21,.08);
    }
    .table td, .table th { vertical-align: middle; }
    .badge.bg-success-soft { background: #e5f7ed; color: #1e6b3a; }
    .badge.bg-warning-soft { background: #fff4d8; color: #7a5600; }
    .badge.bg-secondary-soft { background: #f0f0f0; color: #555; }
    .review-snippet {
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; max-width: 100%;
    }
    .star { color: #f5b300; }
    .product-link { font-weight: 600; text-decoration: none; }
    .thumb-mini {
        width: 44px; height: 44px; object-fit: cover; border-radius: 8px;
        border: 1px solid rgba(32,25,21,.08); background: #faf6f0; cursor: zoom-in;
    }

    /* Responsive styles */
    @media (max-width: 991px) {
        .filter-bar { padding: 10px; }
        .thumb-mini { width: 40px; height: 40px; }
        .table th, .table td { font-size: 0.8rem; }
        .badge { font-size: 0.75rem; }
        .h1, .h5 { font-size: 1.25rem; }
        .review-snippet { -webkit-line-clamp: 2; }
    }
    @media (max-width: 767px) {
        .filter-bar .d-flex.align-items-center { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .filter-bar form .col-12 { margin-bottom: 0.5rem; }
        .thumb-mini { width: 36px; height: 36px; }
        .table th, .table td { font-size: 0.75rem; padding: 0.5rem; }
        .badge { font-size: 0.7rem; }
        .review-snippet { -webkit-line-clamp: 1; }
    }
    @media (max-width: 575px) {
        .filter-bar { padding: 8px; }
        .thumb-mini { width: 32px; height: 32px; }
        .table th, .table td { font-size: 0.7rem; padding: 0.4rem; }
        .badge { font-size: 0.65rem; }
        .h1, .h5 { font-size: 1.1rem; }
        .btn { font-size: 0.8rem; padding: 0.3rem 0.6rem; }
    }
</style>

<div class="container-fluid">
    {{-- Thanh tiêu đề + hành động nhanh / lọc --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Đánh giá sản phẩm</h1>
                <span class="text-muted small">({{ number_format($totalReviews) }} mục)</span>
            </div>
            {{-- Chừa chỗ nếu muốn thêm nút xuất Excel... --}}
        </div>

        {{-- Lọc nhanh (GET) --}}
        <form class="row g-2 mt-2" method="get" action="{{ route('admin.reviews.index') }}">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo sản phẩm / người đánh giá…">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Chờ duyệt</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="rating" class="form-select">
                    <option value="">-- Xếp hạng --</option>
                    @for($i=5;$i>=1;$i--)
                        <option value="{{ $i }}" {{ (string)request('rating')===(string)$i ? 'selected' : '' }}>{{ $i }} sao</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-1 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Lọc</button>
            </div>
        </form>
    </div>

    <div class="card card-soft shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách đánh giá</strong>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%; min-width: 150px;">Sản phẩm</th>
                            <th style="width: 15%; min-width: 120px;">Người đánh giá</th>
                            <th class="text-center" style="width: 15%; min-width: 100px;">Xếp hạng</th>
                            <th style="width: 35%; min-width: 200px;">Nội dung</th>
                            <th class="text-center" style="width: 10%; min-width: 80px;">Trạng thái</th>
                            <th class="text-end" style="width: 15%; min-width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td>
                                    <a class="product-link" href="{{ route('admin.products.show', $review->product_id) }}" title="Xem sản phẩm">
                                        {{ optional($review->product)->name ?? ('SP #'.$review->product_id) }}
                                    </a>
                                </td>
                                <td>
                                    {{ optional($review->user)->name ?? '—' }}<br>
                                    <small class="text-muted">{{ optional($review->user)->email ?? '' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="star">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $review->rating }}/5</small>
                                </td>
                                <td>
                                    <div class="review-snippet mb-1">{{ $review->review }}</div>
                                    @if($review->image)
                                        <img src="{{ asset('storage/' . $review->image) }}" class="thumb-mini review-image" 
                                             data-full="{{ asset('storage/' . $review->image) }}" alt="Ảnh đánh giá" loading="lazy">
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($review->status == 'approved')
                                        <span class="badge bg-success-soft">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-warning-soft">Chờ duyệt</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-none d-md-inline-flex gap-1">
                                        <form action="{{ route('admin.reviews.toggleStatus', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $review->status == 'approved' ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                <i class="bi {{ $review->status == 'approved' ? 'bi-eye-slash' : 'bi-check2-circle' }} me-1"></i>
                                                {{ $review->status == 'approved' ? 'Ẩn' : 'Duyệt' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Xóa đánh giá này?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i>Xóa
                                            </button>
                                        </form>
                                    </div>
                                    {{-- Mobile --}}
                                    <div class="dropdown d-inline d-md-none">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            Hành động
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form action="{{ route('admin.reviews.toggleStatus', $review) }}" method="POST" class="px-3 py-1">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi {{ $review->status == 'approved' ? 'bi-eye-slash' : 'bi-check2-circle' }} me-2"></i>
                                                        {{ $review->status == 'approved' ? 'Ẩn' : 'Duyệt' }}
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="px-3 py-1"
                                                      onsubmit="return confirm('Xóa đánh giá này?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có đánh giá nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="mt-3">
                {{ method_exists($reviews,'withQueryString') ? $reviews->withQueryString()->links() : $reviews->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal xem ảnh lớn (dùng Bootstrap) --}}
<div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <img src="" id="imgModalSrc" class="w-100" alt="Ảnh đánh giá" style="border-radius: .5rem;">
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Xử lý click vào ảnh để mở modal
    const thumbs = document.querySelectorAll('.review-image');
    const modalEl = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalSrc');

    thumbs.forEach(t => {
        t.addEventListener('click', () => {
            const full = t.getAttribute('data-full');
            if(full){
                modalImg.src = full;
                const m = new bootstrap.Modal(modalEl);
                m.show();
            }
        });
    });
});
</script>
@endpush
@endsection