@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
    {{-- =================== HERO / BANNER =================== --}}
    <section class="wishlist-hero position-relative overflow-hidden mb-5">
        <img src="https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg"
             alt="Wishlist Banner" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="container position-relative" data-aos="fade-down">
            <div class="row align-items-center" style="min-height: 240px;">
                <div class="col-12 text-center text-lg-start">
                    <h1 class="display-6 fw-bold mb-1 text-white">Danh sách yêu thích</h1>
                    <p class="mb-0 text-white-50">
                        Lưu lại những món nội thất bạn thích để xem và mua sau.
                    </p>
                </div>
            </div>
        </div>
        <div class="hero-bottom wave-sep"></div>
    </section>

    {{-- =================== CONTENT =================== --}}
    <div class="container my-5">
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3 mb-4" data-aos="fade-up">
            <div class="text-center text-lg-start">
                <h2 class="h4 fw-bold mb-1 text-brand">Sản phẩm yêu thích</h2>
                <div class="text-muted small">
                    @if($wishlistProducts->isNotEmpty())
                        Hiển thị {{ $wishlistProducts->count() }} mục trong trang này
                    @else
                        Danh sách của bạn đang trống
                    @endif
                </div>
            </div>

            {{-- Thanh action nhỏ (chỉ UI, không đổi chức năng hiện có) --}}
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-brand btn-sm rounded-pill">
                    <i class="bi bi-shop me-1"></i> Tiếp tục mua sắm
                </a>
            </div>
        </div>

        @if($wishlistProducts->isNotEmpty())
            <div class="row g-3 g-md-4" data-aos="fade-up" data-aos-delay="50">
                @foreach($wishlistProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3 product-card-wrapper">
                        {{-- GIỮ NGUYÊN COMPONENT --}}
                        <div class="card-hover-raise h-100">
                            @include('frontend.components.product-card', ['product' => $product])
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-5" data-aos="fade-up" data-aos-delay="100">
                {{ $wishlistProducts->links() }}
            </div>
        @else
            <div class="card card-glass text-center py-5 rounded-4" data-aos="fade-in">
                <div class="card-body">
                    <div class="empty-icon mb-3">
                        <i class="bi bi-heart"></i>
                    </div>
                    <h5 class="fw-bold">Danh sách yêu thích của bạn đang trống</h5>
                    <p class="text-muted mb-3">Khám phá các thiết kế nội thất mới nhất và lưu lại những món bạn ưng ý.</p>
                    <a href="{{ route('home') }}" class="btn btn-brand rounded-pill px-4">
                        Bắt đầu mua sắm
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
/* =================== CSS Variables =================== */
:root {
    --bg: #fff;
    --brand: #A20E38;
    --brand-600: #8b0c30;
    --muted: #6c757d;
}

/* =================== HERO =================== */
.wishlist-hero {
    background: var(--bg);
}
.wishlist-hero .hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.03);
    filter: brightness(0.65);
}
.wishlist-hero .hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.35), rgba(0,0,0,0.35));
}
.wave-sep {
    position: absolute;
    left: 0;
    right: 0;
    bottom: -1px;
    height: 32px;
    background: radial-gradient(40px 12px at 50% 0, var(--bg) 98%, transparent 100%) repeat-x;
    background-size: 40px 20px;
}

/* =================== Cards hover =================== */
.card-hover-raise {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card-hover-raise:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

/* =================== Empty state =================== */
.empty-icon i {
    font-size: 3.25rem;
    line-height: 1;
    color: var(--muted);
}
.text-brand {
    color: var(--brand);
}
.btn-brand {
    background-color: var(--brand);
    border-color: var(--brand);
    color: #fff;
}
.btn-brand:hover {
    background-color: var(--brand-600);
    border-color: var(--brand-600);
}
.btn-outline-brand {
    color: var(--brand);
    border-color: var(--brand);
}
.btn-outline-brand:hover {
    background-color: var(--brand);
    color: #fff;
}
.card-glass {
    background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,255,255,0.98));
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(32,25,21,0.08);
    border: 1px solid rgba(15,23,42,0.04);
}
.rounded-4 {
    border-radius: 1rem !important;
}

/* =================== Responsive Design =================== */
@media (max-width: 991px) {
    .wishlist-hero .display-6 {
        font-size: 2rem;
    }
    .wishlist-hero .row {
        min-height: 200px;
    }
    .product-card-wrapper {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 767px) {
    .wishlist-hero .display-6 {
        font-size: 1.8rem;
    }
    .wishlist-hero .row {
        min-height: 180px;
    }
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    .card-glass {
        padding: 2rem 1rem;
    }
    .empty-icon i {
        font-size: 2.5rem;
    }
}

@media (max-width: 575px) {
    .wishlist-hero .display-6 {
        font-size: 1.6rem;
    }
    .wishlist-hero .row {
        min-height: 160px;
    }
    .product-card-wrapper {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush