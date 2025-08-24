@extends('layouts.app')

@section('title', 'Tất cả sản phẩm')

@section('content')
    {{-- ========= BANNER ========= --}}
    <section class="product-hero d-flex align-items-center mb-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10" data-aos="fade-up">
                    <h1 class="display-5 fw-bold text-white mb-3">Cửa Hàng Nội Thất</h1>
                    <p class="lead text-white-50 mb-4">Khám phá những thiết kế tinh gọn, nâng tầm không gian sống của bạn.</p>

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center product-breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <span class="hero-overlay"></span>
    </section>
    
    <div class="container mb-5">
        <div class="row g-4">
            {{-- ========= SIDEBAR LỌC ========= --}}
            <aside class="col-lg-3">
                <div class="filter-sidebar-wrapper position-sticky top-sticky">
                    <div class="card card-glass rounded-4" data-aos="fade-right">
                        <div class="p-3 p-lg-4">
                            @include('frontend.components.filter-sidebar', [
                                'categories' => $categories,
                                'brands' => $brands,
                                'max_price' => $max_price
                            ])
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ========= DANH SÁCH SẢN PHẨM ========= --}}
            <section class="col-lg-9">
                {{-- Thanh công cụ --}}
                <div class="card card-glass mb-4 rounded-4" data-aos="fade-up">
                    <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-brand d-lg-none rounded-pill px-3"
                                    type="button"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#filterOffcanvas"
                                    aria-controls="filterOffcanvas">
                                <i class="bi bi-funnel-fill me-1"></i>Lọc
                            </button>
                            <span class="text-muted small">
                                Hiển thị <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
                                trong <strong>{{ $products->total() }}</strong> sản phẩm
                            </span>
                            @if(request('on_sale'))
                                <span class="badge badge-soft-brand">Đang khuyến mãi</span>
                            @endif
                        </div>
                        <form action="{{ url()->current() }}" method="GET" id="sort-form" class="d-flex align-items-center gap-2">
                            @foreach(request()->except(['sort', 'page']) as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label for="sort-select" class="form-label small mb-0 text-nowrap">Sắp xếp:</label>
                            <select class="form-select form-select-sm sort-select rounded-pill"
                                    name="sort"
                                    id="sort-select"
                                    onchange="this.form.submit();">
                                <option value="latest"      @selected(request('sort','latest')=='latest')>Mới nhất</option>
                                <option value="bestseller"  @selected(request('sort')=='bestseller')>Bán chạy</option>
                                <option value="price_asc"   @selected(request('sort')=='price_asc')>Giá: Thấp đến cao</option>
                                <option value="price_desc"  @selected(request('sort')=='price_desc')>Giá: Cao đến thấp</option>
                            </select>
                            @php $isSale = request('on_sale'); @endphp
                            <a href="{{ $isSale
                                    ? request()->fullUrlWithQuery(['on_sale' => null, 'page'=>null])
                                    : request()->fullUrlWithQuery(['on_sale' => 1, 'page'=>null]) }}"
                               class="btn btn-sm {{ $isSale ? 'btn-brand' : 'btn-outline-brand' }} rounded-pill">
                                <i class="bi bi-tags me-1"></i> Khuyến mãi
                            </a>
                        </form>
                    </div>
                </div>
                @if($products->isNotEmpty())
                    <div class="row g-3 g-md-4" data-aos="fade-up" data-aos-delay="75">
                        @foreach($products as $product)
                            <div class="col-6 col-md-6 col-lg-4">
                                <div class="product-hover-wrapper h-100">
                                    @include('frontend.components.product-card', ['product' => $product])
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="card card-glass text-center py-5 rounded-4 empty-product-list" data-aos="fade-up">
                        <div class="card-body">
                            <i class="bi bi-search-heart" style="font-size: 3.5rem;"></i>
                            <h5 class="mt-3">Không tìm thấy sản phẩm nào</h5>
                            <p class="text-muted">Vui lòng thử lại với các tiêu chí lọc khác.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill px-3">Xóa tất cả bộ lọc</a>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
    <div class="offcanvas offcanvas-start rounded-end-4 overflow-hidden"
            tabindex="-1"
            id="filterOffcanvas"
            aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-semibold" id="filterOffcanvasLabel">Bộ lọc sản phẩm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body">
            @include('frontend.components.filter-sidebar', [
                'categories' => $categories,
                'brands' => $brands,
                'max_price' => $max_price
            ])
        </div>
    </div>
@endsection

@push('styles')
<style>
/* =================== Banner =================== */
.product-hero {
    position: relative;
    min-height: 320px;
    background-image: linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35)),
        url('https://images.unsplash.com/photo-1501045661006-fcebe0257c3f?q=80&w=1600&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    border-radius: 0 0 32px 32px;
    overflow: hidden;
}
.product-hero .hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(60% 60% at 50% 0%, rgba(var(--brand-rgb), 0.2) 0%, rgba(var(--brand-rgb), 0) 60%);
    pointer-events: none;
}
.product-breadcrumb {
    --bs-breadcrumb-divider: '›';
}
.product-breadcrumb .breadcrumb-item a {
    color: var(--sand);
    text-decoration: none;
    transition: color 0.2s ease;
}
.product-breadcrumb .breadcrumb-item a:hover {
    color: var(--brand);
}
.product-breadcrumb .breadcrumb-item.active {
    color: var(--muted);
}

/* =================== Card and Product Wrapper =================== */
.card-glass {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid rgba(15, 23, 42, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card-glass:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
.rounded-4 {
    border-radius: 1rem !important;
}
.product-hover-wrapper {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.product-hover-wrapper:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
.empty-product-list {
    background: transparent;
}

/* =================== Form and Button Styles =================== */
.form-check-input:checked {
    background-color: var(--brand);
    border-color: var(--brand);
}
.form-check-input:focus {
    border-color: rgba(var(--brand-rgb), 0.5);
    box-shadow: 0 0 0 0.2rem var(--ring);
}
.form-range::-webkit-slider-thumb {
    background: var(--brand);
    border: 1px solid var(--brand-600);
    box-shadow: 0 0 0 4px var(--ring);
}
.form-range::-moz-range-thumb {
    background: var(--brand);
    border: 1px solid var(--brand-600);
    box-shadow: 0 0 0 4px var(--ring);
}
.form-range::-webkit-slider-runnable-track {
    background: var(--sand);
}
.form-range::-moz-range-track {
    background: var(--sand);
}
.form-range:focus::-webkit-slider-thumb {
    box-shadow: 0 0 0 4px var(--ring);
}
.form-range:focus::-moz-range-thumb {
    box-shadow: 0 0 0 4px var(--ring);
}
.form-check {
    display: flex;
    align-items: center;
}
.form-check-input {
    margin-top: 0;
}
.form-check-label {
    margin-left: 0.5rem;
    transition: color 0.2s ease;
}
.form-check-label:hover {
    color: var(--brand);
}
.form-select.sort-select {
    width: 190px;
    padding-left: 0.9rem;
    padding-right: 2rem;
    background-color: var(--card);
    border: 1px solid rgba(10, 37, 64, 0.12);
}
.form-select.sort-select:focus {
    box-shadow: 0 0 0 0.2rem var(--ring);
    border-color: var(--brand);
}
.btn-brand {
    background-color: var(--brand);
    border-color: var(--brand);
    color: #fff;
    padding: 0.5rem 1rem;
    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}
.btn-brand:hover {
    background-color: var(--brand-600);
    border-color: var(--brand-600);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}
.btn-outline-brand {
    color: var(--brand);
    border-color: var(--brand);
    padding: 0.45rem 0.9rem;
    transition: background 0.15s ease, color 0.15s ease;
}
.btn-outline-brand:hover {
    background-color: var(--brand);
    border-color: var(--brand);
    color: #fff;
}
.text-muted {
    color: var(--muted);
}
.badge-soft-brand {
    background: rgba(var(--brand-rgb), 0.1);
    color: var(--brand);
}

/* =================== Links =================== */
a {
    color: var(--brand);
    text-decoration: none;
}
a:hover {
    color: var(--brand-600);
}

/* =================== Sidebar and Offcanvas =================== */
.top-sticky {
    top: 96px;
}
.filter-sidebar-wrapper {
    display: block;
}
@media (max-width: 991.98px) {
    .filter-sidebar-wrapper {
        display: none;
    }
}
.offcanvas-start {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
}

/* =================== Responsive Design =================== */
@media (max-width: 991px) {
    .product-hero {
        min-height: 280px;
    }
    .product-hero .display-5 {
        font-size: 2rem;
    }
    .product-hero .lead {
        font-size: 1rem;
    }
    .col-lg-3, .col-lg-9 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .form-select.sort-select {
        width: 160px;
    }
    .card {
        padding: 1.5rem;
    }
}

@media (max-width: 767px) {
    .product-hero {
        min-height: 240px;
        border-radius: 0 0 24px 24px;
    }
    .product-hero .display-5 {
        font-size: 1.8rem;
    }
    .product-hero .lead {
        font-size: 0.9rem;
    }
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    .card {
        padding: 1rem;
    }
    .card-body {
        padding: 1rem;
    }
    .btn-brand {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
    .btn-outline-brand {
        padding: 0.35rem 0.7rem;
        font-size: 0.85rem;
    }
    .form-select.sort-select {
        width: 140px;
        font-size: 0.9rem;
    }
    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    .bi-search-heart {
        font-size: 3rem;
    }
}

@media (max-width: 575px) {
    .product-hero {
        min-height: 200px;
        border-radius: 0 0 16px 16px;
    }
    .product-hero .display-5 {
        font-size: 1.6rem;
    }
    .product-hero .lead {
        font-size: 0.85rem;
    }
    .product-breadcrumb {
        font-size: 0.85rem;
    }
    .card {
        padding: 0.75rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    .btn-brand {
        padding: 0.35rem 0.7rem;
        font-size: 0.85rem;
    }
    .btn-outline-brand {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
    .form-select.sort-select {
        width: 120px;
        font-size: 0.85rem;
    }
    .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .bi-search-heart {
        font-size: 2.5rem;
    }
}
</style>
@endpush