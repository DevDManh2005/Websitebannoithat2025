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
                <h2 class="h4 fw-bold mb-1">Sản phẩm yêu thích</h2>
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
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-shop me-1"></i> Tiếp tục mua sắm
                </a>
            </div>
        </div>

        @if($wishlistProducts->isNotEmpty())
            <div class="row g-3 g-md-4" data-aos="fade-up" data-aos-delay="50">
                @foreach($wishlistProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
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
            <div class="card border-0 shadow-sm text-center py-5" data-aos="fade-in">
                <div class="card-body">
                    <div class="empty-icon mb-3">
                        <i class="bi bi-heart"></i>
                    </div>
                    <h5 class="fw-bold">Danh sách yêu thích của bạn đang trống</h5>
                    <p class="text-muted mb-3">Khám phá các thiết kế nội thất mới nhất và lưu lại những món bạn ưng ý.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4">
                        Bắt đầu mua sắm
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
/* =================== HERO =================== */
.wishlist-hero { background:#fff; }
.wishlist-hero .hero-bg{
    position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform: scale(1.03);
    filter: brightness(0.65);
}
.wishlist-hero .hero-overlay{
    position:absolute; inset:0; background: linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.35));
}
.wave-sep{
    position:absolute; left:0; right:0; bottom:-1px; height:32px; background:
        radial-gradient(40px 12px at 50% 0, #fff 98%, transparent 100%) repeat-x;
    background-size: 40px 20px;
}

/* =================== Cards hover (không đụng vào component bên trong) =================== */
.card-hover-raise { transition: transform .25s ease, filter .25s ease; }
.card-hover-raise:hover { transform: translateY(-4px); }

.empty-icon i{
    font-size: 3.25rem; line-height:1; color:#dee2e6;
}

/* =================== Banner text tweaks on small =================== */
@media (max-width: 575.98px){
    .wishlist-hero .display-6{ font-size: 1.6rem; }
}
</style>
@endpush
