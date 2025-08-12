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
                    <div class="card border-0 shadow-sm rounded-4" data-aos="fade-right">
                        <div class="card-body p-3 p-lg-4">
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
<div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
  <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-center">

    <div class="d-flex align-items-center gap-2">
      {{-- Nút mở Offcanvas (mobile) --}}
      <button class="btn btn-outline-secondary d-lg-none rounded-pill px-3"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#filterOffcanvas"
              aria-controls="filterOffcanvas">
        <i class="bi bi-funnel-fill me-1"></i>Lọc
      </button>

      <span class="text-secondary small">
        Hiển thị <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
        trong <strong>{{ $products->total() }}</strong> sản phẩm
      </span>

      {{-- Chip trạng thái khuyến mãi (nếu đang bật) --}}
      @if(request('on_sale'))
        <span class="badge rounded-pill text-bg-danger ms-2">Đang khuyến mãi</span>
      @endif
    </div>

    <form action="{{ url()->current() }}" method="GET" id="sort-form" class="d-flex align-items-center gap-2">
      {{-- Giữ lại các tham số lọc hiện tại khi sắp xếp --}}
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


      {{-- Toggle khuyến mãi nhanh --}}
      @php $isSale = request('on_sale'); @endphp
      <a href="{{ $isSale
                  ? request()->fullUrlWithQuery(['on_sale' => null, 'page'=>null])
                  : request()->fullUrlWithQuery(['on_sale' => 1, 'page'=>null]) }}"
         class="btn btn-sm {{ $isSale ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill">
        <i class="bi bi-tags me-1"></i> Khuyến mãi
      </a>
    </form>
  </div>
</div>


                {{-- Lưới sản phẩm --}}
                @if($products->isNotEmpty())
                    <div class="row g-3 g-md-4" data-aos="fade-up" data-aos-delay="75">
                        @foreach($products as $product)
                            <div class="col-6 col-md-6 col-lg-4">
                                {{-- component card của bạn đã có hiệu ứng, giữ nguyên để không ảnh hưởng logic --}}
                                @include('frontend.components.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>

                    {{-- Phân trang --}}
                    <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5 empty-product-list" data-aos="fade-up">
                        <div class="card-body">
                            <i class="bi bi-search-heart" style="font-size: 3.5rem; color: #ced4da;"></i>
                            <h5 class="mt-3">Không tìm thấy sản phẩm nào</h5>
                            <p class="text-secondary">Vui lòng thử lại với các tiêu chí lọc khác.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-3">Xóa tất cả bộ lọc</a>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- ========= OFFCANVAS LỌC (MOBILE) ========= --}}
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
/* ====== HERO ====== */
.product-hero{
    position: relative;
    min-height: 320px;
    background-image:
        linear-gradient(to bottom, rgba(0,0,0,.35), rgba(0,0,0,.35)),
        url('https://images.unsplash.com/photo-1501045661006-fcebe0257c3f?q=80&w=1600&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    border-radius: 0 0 32px 32px;
    overflow: hidden;
}
.product-hero .hero-overlay{
    position:absolute; inset:0;
    background: radial-gradient(60% 60% at 50% 0%, rgba(13,110,253,.2) 0%, rgba(13,110,253,0) 60%);
    pointer-events:none;
}
.product-breadcrumb{
    --bs-breadcrumb-divider: '›';
}
.product-breadcrumb .breadcrumb-item a{
    color:#f8f9fa; text-decoration:none;
}
.product-breadcrumb .breadcrumb-item a:hover{ text-decoration:underline; }
.product-breadcrumb .breadcrumb-item.active{ color:#e9ecef; }

/* ====== LAYOUT ====== */
.top-sticky{ top: 96px; }
.filter-sidebar-wrapper{ display:block; }
@media (max-width: 991.98px){
    .filter-sidebar-wrapper{ display:none; }
}

/* ====== TOOLBAR ====== */
.sort-select{
    width: 190px;
    padding-left: .9rem;
    padding-right: 2rem;
    background-color:#f8f9fa;
    border:1px solid rgba(10,37,64,.12);
}
.sort-select:focus{ box-shadow:none; border-color:#86b7fe; }

/* ====== EMPTY LIST ====== */
.empty-product-list{ background:#f8f9fa; }

/* ====== Small polish ====== */
.card.rounded-4{ border-radius: 1rem; }
.btn.rounded-pill{ border-radius: 999px; }
</style>
@endpush
