@extends('admins::layouts.app')


@section('title', 'Danh sách sản phẩm')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $products */
    $catList = $categories ?? $allCategories ?? collect();

    // Helper: chuyển path tương đối -> URL đầy đủ (ưu tiên link tuyệt đối)
    $toUrl = function($p){
        if(!$p) return null;
        return \Illuminate\Support\Str::startsWith($p, ['http://','https://','//'])
            ? $p
            : asset('storage/'.$p);
    };
@endphp

<style>
    /* Khung lọc mềm */
    .filter-bar {
        border-radius: 16px; padding: 12px;
        background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
        border: 1px solid rgba(32,25,21,.08);
        box-shadow: var(--shadow);
    }
    /* Card mềm */
    .card-soft { border-radius: 16px; border: 1px solid rgba(32,25,21,.08); }
    .card-soft .card-header { background: transparent; border-bottom: 1px dashed rgba(32,25,21,.12); }

    /* Ảnh sản phẩm */
    .prod-thumb {
        width: 56px; height: 56px; flex: 0 0 56px; border-radius: 12px; object-fit: cover;
        background: #f7f2eb; border: 1px solid rgba(32,25,21,.06);
    }

    /* Bảng + sticky head */
    .table td, .table th { vertical-align: middle; }
    .table .text-truncate { max-width: 260px; }
    thead.table-light th { position: sticky; top: 0; z-index: 1; }
    .table th { font-size: 0.9rem; }
    .table td { font-size: 0.85rem; }
    .badge { font-size: 0.8rem; padding: 0.4em 0.6em; }

    /* Soft badges */
    .badge.bg-primary-soft { background: #e8f0ff; color: #0b4a8b; }
    .badge.bg-success-soft { background: #e5f7ed; color: #1e6b3a; }
    .badge.bg-danger-soft { background: #fde7e7; color: #992f2f; }
    .badge.bg-info-soft { background: #e6f7ff; color: #0b5b6d; }
    .badge.bg-secondary-soft { background: #f0f0f0; color: #555; }

    /* Chấm trạng thái nhỏ */
    .status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
    .dot-green { background: #28a745; }
    .dot-gray { background: #6c757d; }

    /* Hành động gọn trên di động */
    .actions .btn { min-width: 34px; }
    .actions .btn-sm { padding: 0.3rem 0.5rem; }

    /* Responsive styles */
    @media (max-width: 991px) {
        .filter-bar { padding: 10px; }
        .prod-thumb { width: 48px; height: 48px; flex: 0 0 48px; }
        .table .text-truncate { max-width: 200px; }
        .table th, .table td { font-size: 0.8rem; }
        .badge { font-size: 0.75rem; }
        .h1, .h5 { font-size: 1.25rem; }
        .input-group, .form-select { font-size: 0.9rem; }
        .btn { font-size: 0.9rem; }
    }
    @media (max-width: 767px) {
        .filter-bar .d-flex.align-items-center { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .filter-bar form .col-12 { margin-bottom: 0.5rem; }
        .prod-thumb { width: 40px; height: 40px; flex: 0 0 40px; }
        .table .text-truncate { max-width: 150px; }
        .table th, .table td { font-size: 0.75rem; padding: 0.5rem; }
        .badge { font-size: 0.7rem; }
        .actions .btn { padding: 0.25rem 0.4rem; }
    }
    @media (max-width: 575px) {
        .filter-bar { padding: 8px; }
        .prod-thumb { width: 36px; height: 36px; flex: 0 0 36px; }
        .table .text-truncate { max-width: 120px; }
        .table th, .table td { font-size: 0.7rem; padding: 0.4rem; }
        .badge { font-size: 0.65rem; }
        .h1, .h5 { font-size: 1.1rem; }
        .btn { font-size: 0.8rem; padding: 0.3rem 0.6rem; }
        .input-group, .form-select { font-size: 0.85rem; }
    }
</style>

<div class="container-fluid">
    {{-- Thanh tiêu đề + hành động --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Danh sách sản phẩm</h1>
                <span class="text-muted small">
                    ({{ number_format(method_exists($products,'total') ? $products->total() : $products->count()) }} mục)
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                </a>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <form class="row g-2 mt-2" method="get" action="{{ route('admin.products.index') }}">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo tên / mã…">
                    @if(request()->hasAny(['q','status','category_id']) && filled(request()->only('q','status','category_id')))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" title="Đặt lại">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>
            @if($catList && count($catList))
            <div class="col-12 col-md-6 col-lg-3">
                <select name="category_id" class="form-select">
                    <option value="">-- Danh mục --</option>
                    @foreach($catList as $c)
                        <option value="{{ $c->id }}" {{ (string)request('category_id')===(string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12 col-md-6 col-lg-1 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Lọc</button>
            </div>
        </form>
    </div>

    {{-- Danh sách --}}
    <div class="card card-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách</strong>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%; min-width: 60px;">#</th>
                            <th style="width: 30%; min-width: 220px;">Sản phẩm</th>
                            <th style="width: 20%; min-width: 150px;">Danh mục</th>
                            <th style="width: 15%; min-width: 120px;">Giá (chính)</th>
                            <th class="text-center" style="width: 10%; min-width: 80px;">Biến thể</th>
                            <th class="text-end" style="width: 10%; min-width: 80px;">Tồn kho</th>
                            <th style="width: 10%; min-width: 80px;">Trạng thái</th>
                            <th class="text-end" style="width: 15%; min-width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                // Thumbnail ưu tiên
                                $thumb = null;
                                try {
                                    if(!empty($product->thumbnail)) {
                                        $thumb = $toUrl($product->thumbnail);
                                    } elseif(!empty($product->image)) {
                                        $thumb = $toUrl($product->image);
                                    } elseif(($product->relationLoaded('images') ? $product->images->first() : optional($product->images)->first())) {
                                        $imgModel = $product->images->first();
                                        $src = $imgModel->image_url ?? $imgModel->url ?? $imgModel->path ?? null;
                                        $thumb = $toUrl($src);
                                    }
                                } catch (\Throwable $e) {}

                                // Biến thể chính + giá
                                $mainVariant = $product->variants->firstWhere('is_main_variant', true) ?? $product->variants->first();

                                // Tổng tồn kho
                                $stockTotal = $product->variants->sum(function($v){
                                    return optional($v->inventory)->quantity ?? 0;
                                });

                                // Gộp danh mục: hiển thị tối đa 3, còn lại gom +n
                                $cats = $product->categories ?? collect();
                                $catShown = $cats->take(3);
                                $catRemain = max(0, $cats->count() - $catShown->count());
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $product->id }}</td>

                                {{-- Sản phẩm: ảnh + tên + mã + (thương hiệu nếu có) --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img
                                            src="{{ $thumb ?? 'https://via.placeholder.com/56x56?text=SP' }}"
                                            alt="{{ $product->name }}"
                                            class="prod-thumb"
                                            loading="lazy"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=SP';"
                                        >
                                        <div class="min-w-0">
                                            <a class="fw-semibold text-decoration-none text-truncate d-block"
                                               href="{{ route('admin.products.edit', $product->id) }}"
                                               title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </a>
                                            <div class="small text-muted text-truncate">
                                                Mã: {{ $product->sku ?? ('SP'.$product->id) }}
                                                @if($product->brand?->name)
                                                    • Thương hiệu: {{ $product->brand->name }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Danh mục: tối đa 3 chip + số còn lại --}}
                                <td>
                                    @forelse($catShown as $category)
                                        <span class="badge bg-primary-soft me-1 mb-1">{{ $category->name }}</span>
                                    @empty
                                        <span class="text-muted small">—</span>
                                    @endforelse
                                    @if($catRemain > 0)
                                        <span class="badge bg-secondary-soft" data-bs-toggle="tooltip"
                                              title="{{ $cats->skip(3)->pluck('name')->implode(' • ') }}">
                                            +{{ $catRemain }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Giá chính / giá KM --}}
                                <td>
                                    @if($mainVariant)
                                        @if(($mainVariant->sale_price ?? 0) > 0 && $mainVariant->sale_price < ($mainVariant->price ?? 0))
                                            <span class="text-danger fw-semibold">{{ number_format($mainVariant->sale_price) }} ₫</span>
                                            <small class="text-muted text-decoration-line-through ms-1">{{ number_format($mainVariant->price) }} ₫</small>
                                        @else
                                            <span class="fw-semibold">{{ number_format($mainVariant->price ?? 0) }} ₫</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                {{-- Số biến thể --}}
                                <td class="text-center">
                                    <span class="badge bg-info-soft">{{ $product->variants->count() }}</span>
                                </td>

                                {{-- Tồn kho tổng --}}
                                <td class="text-end">
                                    <span class="badge {{ $stockTotal > 0 ? 'bg-success-soft' : 'bg-danger-soft' }}">
                                        {{ number_format($stockTotal) }}
                                    </span>
                                </td>

                                {{-- Trạng thái --}}
                                <td>
                                    @if ($product->is_active)
                                        <span class="status-dot dot-green"></span><span>Hiển thị</span>
                                    @else
                                        <span class="status-dot dot-gray"></span><span>Ẩn</span>
                                    @endif
                                </td>

                                {{-- Hành động --}}
                                <td class="text-end actions">
                                    {{-- Desktop --}}
                                    <div class="d-none d-md-inline-flex gap-1">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                           class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Xóa sản phẩm &quot;{{ $product->name }}&quot;?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    {{-- Mobile --}}
                                    <div class="dropdown d-inline d-md-none">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            Hành động
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.products.show', $product->id) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                                      onsubmit="return confirm('Xóa sản phẩm &quot;{{ $product->name }}&quot;?');">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Xóa</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted p-4">
                                    Không có sản phẩm nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="mt-3">
                {{ method_exists($products,'withQueryString') ? $products->withQueryString()->links() : $products->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Bật tooltip Bootstrap nếu có
    if (window.bootstrap) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el)
        })
    }
});
</script>
@endpush
@endsection