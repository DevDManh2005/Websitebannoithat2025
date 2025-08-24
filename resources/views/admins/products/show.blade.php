
@extends('admins::layouts.app')


@section('title', 'Chi tiết sản phẩm: ' . $product->name)

@section('content')
@php
    // Ảnh: ưu tiên ảnh đánh dấu là ảnh chính, sau đó đến ảnh đầu tiên
    $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $coverUrl = $primaryImage
        ? (\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://','https://','//'])
            ? $primaryImage->image_url
            : asset('storage/'.$primaryImage->image_url))
        : 'https://via.placeholder.com/640x400?text=Hinh+san+pham';

    // Biến thể chính (nếu có), fallback biến thể đầu
    $mainVariant = $product->variants->firstWhere('is_main_variant', true) ?? $product->variants->first();

    // Helper hiển thị thuộc tính biến thể
    $attrBadges = function($attributes){
        if (!is_array($attributes) || empty($attributes)) return '<span class="text-muted">—</span>';
        $html = '';
        foreach ($attributes as $k => $v) {
            $html .= '<span class="badge bg-secondary-soft me-1 mb-1">'.e(ucfirst($k)).': '.e($v).'</span>';
        }
        return $html;
    };
@endphp

<style>
    .card-soft { border-radius: 16px; border: 1px solid rgba(32,25,21,.08); }
    .card-soft .card-header { background: transparent; border-bottom: 1px dashed rgba(32,25,21,.12); }
    .badge.bg-success-soft { background: #e5f7ed; color: #1e6b3a; }
    .badge.bg-danger-soft { background: #fde7e7; color: #992f2f; }
    .badge.bg-info-soft { background: #e6f1ff; color: #0b4a8b; }
    .badge.bg-secondary-soft { background: #f0f0f0; color: #555; }
    .prod-cover {
        width: 100%; max-height: 360px; object-fit: cover; border-radius: 14px;
        border: 1px solid rgba(32,25,21,.08); background: #faf6f0;
    }
    .thumb {
        width: 90px; height: 90px; object-fit: cover; border-radius: 12px;
        border: 1px solid rgba(32,25,21,.08); background: #faf6f0; cursor: zoom-in;
    }
    .kv { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .kv .k { min-width: 120px; color: #6c757d; }
    .table-variants td, .table-variants th { vertical-align: middle; }
    .table-variants th { font-size: 0.9rem; }
    .table-variants td { font-size: 0.85rem; }
    .badge { font-size: 0.8rem; padding: 0.4em 0.6em; }

    /* Responsive styles */
    @media (max-width: 991px) {
        .prod-cover { max-height: 300px; }
        .thumb { width: 70px; height: 70px; }
        .kv .k { min-width: 100px; font-size: 0.9rem; }
        .h1, .h4 { font-size: 1.5rem; }
        .card-body { padding: 1rem; }
        .table-variants th, .table-variants td { font-size: 0.8rem; }
        .badge { font-size: 0.75rem; }
    }
    @media (max-width: 767px) {
        .prod-cover { max-height: 250px; }
        .thumb { width: 60px; height: 60px; }
        .kv { flex-direction: column; align-items: flex-start; }
        .kv .k { min-width: unset; font-size: 0.85rem; }
        .d-flex.align-items-center.gap-3 { flex-direction: column; align-items: flex-start; }
        .d-flex.gap-2 { flex-wrap: wrap; justify-content: center; }
        .table-variants { font-size: 0.75rem; }
        .table-variants th, .table-variants td { padding: 0.5rem; }
        .card { margin-bottom: 1rem; }
    }
    @media (max-width: 575px) {
        .prod-cover { max-height: 200px; }
        .thumb { width: 50px; height: 50px; }
        .h1, .h4 { font-size: 1.25rem; }
        .btn { font-size: 0.8rem; padding: 0.4rem 0.8rem; }
        .table-responsive { overflow-x: auto; }
        .table-variants th, .table-variants td { font-size: 0.7rem; }
        .badge { font-size: 0.7rem; }
    }
</style>

<div class="container-fluid">
    {{-- Dẫn đường --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    {{-- Tiêu đề + hành động nhanh --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-3">
            <h1 class="h4 mb-0 fw-bold">{{ $product->name }}</h1>
            @if($product->is_active)
                <span class="badge bg-success-soft">Đang hiển thị</span>
            @else
                <span class="badge bg-secondary">Đã ẩn</span>
            @endif
            @if($product->is_featured)
                <span class="badge bg-info-soft">Nổi bật</span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Sửa
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-light">Quay lại</a>
        </div>
    </div>

    {{-- Thẻ thông tin nhanh --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="kv">
                        <div class="k">Mã sản phẩm</div>
                        <div class="v fw-semibold">{{ $product->sku ?? ('SP'.$product->id) }}</div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Biến thể</div>
                        <div class="v"><span class="badge bg-info-soft">{{ $product->variants->count() }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="k text-muted">Giá hiển thị</div>
                    <div class="mt-1">
                        @if($mainVariant)
                            @if(($mainVariant->sale_price ?? 0) > 0 && $mainVariant->sale_price < ($mainVariant->price ?? 0))
                                <span class="text-danger fw-bold">{{ number_format($mainVariant->sale_price) }} ₫</span>
                                <small class="text-muted text-decoration-line-through ms-1">{{ number_format($mainVariant->price) }} ₫</small>
                            @else
                                <span class="fw-bold">{{ number_format($mainVariant->price ?? 0) }} ₫</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="k text-muted">Danh mục</div>
                    <div class="mt-1 d-flex flex-wrap gap-1">
                        @forelse($product->categories as $category)
                            <span class="badge bg-primary-subtle text-primary-emphasis">{{ $category->name }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="kv">
                        <div class="k">Thương hiệu</div>
                        <div class="v">{{ optional($product->brand)->name ?? '—' }}</div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Nhà cung cấp</div>
                        <div class="v">{{ optional($product->supplier)->name ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2 cột nội dung --}}
    <div class="row g-3">
        {{-- Cột trái --}}
        <div class="col-12 col-lg-8">
            {{-- Ảnh sản phẩm --}}
            <div class="card card-soft shadow-sm mb-3">
                <div class="card-header"><h5 class="card-title mb-0">Hình ảnh</h5></div>
                <div class="card-body">
                    <img src="{{ $coverUrl }}" alt="Ảnh sản phẩm" class="prod-cover mb-3" id="coverImage">
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($product->images as $image)
                            @php
                                $url = \Illuminate\Support\Str::startsWith($image->image_url, ['http://','https://','//'])
                                    ? $image->image_url
                                    : asset('storage/'.$image->image_url);
                            @endphp
                            <img src="{{ $url }}" data-full="{{ $url }}" class="thumb image-thumb" alt="Ảnh sản phẩm">
                        @empty
                            <span class="text-muted">Chưa có hình ảnh.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Thông tin mô tả --}}
            <div class="card card-soft shadow-sm mb-3">
                <div class="card-header"><h5 class="card-title mb-0">Thông tin cơ bản</h5></div>
                <div class="card-body">
                    <div class="kv">
                        <div class="k">Tên sản phẩm</div>
                        <div class="v">{{ $product->name }}</div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Đường dẫn (slug)</div>
                        <div class="v"><code>{{ $product->slug ?? '—' }}</code></div>
                    </div>
                    <div class="mt-3">
                        <div class="k text-muted mb-1">Mô tả</div>
                        <div class="p-3 rounded border bg-white">
                            {!! $product->description ? nl2br(e($product->description)) : '<span class="text-muted">—</span>' !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Biến thể --}}
            <div class="card card-soft shadow-sm mb-3">
                <div class="card-header"><h5 class="card-title mb-0">Biến thể</h5></div>
                <div class="card-body">
                    @if($product->variants->isEmpty())
                        <p class="text-muted mb-0">Chưa có biến thể.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-variants table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%; min-width: 120px;">SKU</th>
                                        <th style="width: 25%;">Thuộc tính</th>
                                        <th class="text-end" style="width: 15%; min-width: 100px;">Giá</th>
                                        <th class="text-end" style="width: 15%; min-width: 100px;">Giá KM</th>
                                        <th class="text-end" style="width: 15%; min-width: 100px;">Tồn kho</th>
                                        <th class="text-center" style="width: 10%; min-width: 80px;">Chính</th>
                                        <th class="text-end" style="width: 15%; min-width: 100px;">Cân nặng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td class="fw-semibold">{{ $variant->sku }}</td>
                                            <td>{!! $attrBadges($variant->attributes) !!}</td>
                                            <td class="text-end">{{ number_format($variant->price) }} ₫</td>
                                            <td class="text-end">
                                                @if($variant->sale_price)
                                                    <span class="text-danger fw-semibold">{{ number_format($variant->sale_price) }} ₫</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $variant->inventory ? number_format($variant->inventory->quantity) : '—' }}</td>
                                            <td class="text-center">
                                                @if($variant->is_main_variant)
                                                    <span class="badge bg-success-soft">Có</span>
                                                @else
                                                    <span class="badge bg-secondary-soft">Không</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($variant->weight) }} g</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cột phải --}}
        <div class="col-12 col-lg-4">
            <div class="card card-soft shadow-sm mb-3">
                <div class="card-header"><h5 class="card-title mb-0">Trạng thái & dán nhãn</h5></div>
                <div class="card-body">
                    <div class="kv">
                        <div class="k">Trạng thái</div>
                        <div class="v">
                            @if($product->is_active)
                                <span class="badge bg-success-soft">Đang hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Đã ẩn</span>
                            @endif
                        </div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Nổi bật</div>
                        <div class="v">
                            @if($product->is_featured)
                                <span class="badge bg-info-soft">Có</span>
                            @else
                                <span class="badge bg-secondary-soft">Không</span>
                            @endif
                        </div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Nhãn</div>
                        <div class="v">{{ $product->label ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="card card-soft shadow-sm">
                <div class="card-header"><h5 class="card-title mb-0">Thông tin liên quan</h5></div>
                <div class="card-body">
                    <div class="kv">
                        <div class="k">Danh mục</div>
                        <div class="v d-flex flex-wrap gap-1">
                            @forelse($product->categories as $category)
                                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $category->name }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Thương hiệu</div>
                        <div class="v">{{ optional($product->brand)->name ?? '—' }}</div>
                    </div>
                    <div class="kv mt-2">
                        <div class="k">Nhà cung cấp</div>
                        <div class="v">{{ optional($product->supplier)->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-square me-1"></i> Sửa sản phẩm
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal xem ảnh lớn (dùng Bootstrap) --}}
<div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <img src="" id="imgModalSrc" class="w-100" alt="Ảnh sản phẩm" style="border-radius: .5rem;">
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const cover = document.getElementById('coverImage');
    const thumbs = document.querySelectorAll('.image-thumb');
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
        t.addEventListener('mouseenter', () => {
            const full = t.getAttribute('data-full');
            if(full) cover.src = full;
        });
    });
});
</script>
@endpush
@endsection