@extends('layouts.app')

@section('title', 'Trang Chủ - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
    {{-- 1. Hero Banner Slider --}}
    <section class="hero-section bg-light">
        <!-- Cấu trúc HTML cho Swiper Slider -->
        <div class="swiper hero-slider">
            <div class="swiper-wrapper">
                {{-- Lặp qua mỗi slide được gửi từ HomeController --}}
                @forelse($slides as $slide)
                <div class="swiper-slide">
                    <div class="container">
                        <div class="row align-items-center">
                            {{-- Cột chứa nội dung chữ --}}
                            <div class="col-lg-5 text-center text-lg-start">
                                <div class="hero-slide-content">
                                    @if($slide->subtitle)
                                        <p class="text-uppercase text-muted small mb-2">{{ $slide->subtitle }}</p>
                                    @endif
                                    <h1 class="hero-title">{{ $slide->title }}</h1>
                                    @if($slide->button_text && $slide->button_link)
                                        <a href="{{ $slide->button_link }}" class="btn btn-outline-dark mt-4">{{ $slide->button_text }}</a>
                                    @endif
                                </div>
                            </div>
                            {{-- Cột chứa hình ảnh --}}
                            <div class="col-lg-7">
                                <img src="{{ asset('storage/' . $slide->image) }}" class="img-fluid hero-slide-image" alt="{{ $slide->title }}">
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                {{-- Slide mặc định nếu không có slide nào trong DB --}}
                <div class="swiper-slide">
                     <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-5 text-center text-lg-start">
                                <div class="hero-slide-content">
                                    <p class="text-uppercase text-muted small mb-2">{{ $settings['hero_subtitle'] ?? 'Khám phá bộ sưu tập nội thất tinh tế.' }}</p>
                                    <h1 class="hero-title">{{ $settings['hero_title'] ?? 'Không Gian Sống Đẳng Cấp' }}</h1>
                                    <a href="{{ $settings['hero_button_link'] ?? '#' }}" class="btn btn-outline-dark mt-4">{{ $settings['hero_button_text'] ?? 'Xem Chi Tiết' }}</a>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <img src="{{ asset('storage/' . ($settings['hero_banner'] ?? '')) }}" class="img-fluid hero-slide-image" alt="Banner">
                            </div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            {{-- Nút điều hướng của Slider --}}
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    {{-- 1.5. Features Section (BỔ SUNG MỚI) --}}
    <section class="features-section py-4 border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-6 col-lg-3 d-flex align-items-center mb-3 mb-lg-0">
                    <i class="bi bi-tag fs-2 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Giá Trị Tuyệt Vời</h6>
                        <p class="mb-0 text-muted small">Giá cả phù hợp với ngân sách</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3 d-flex align-items-center mb-3 mb-lg-0">
                    <i class="bi bi-truck fs-2 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Miễn Phí Vận Chuyển</h6>
                        <p class="mb-0 text-muted small">Cho đơn hàng trên 2 triệu</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3 d-flex align-items-center">
                    <i class="bi bi-award fs-2 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Dịch Vụ Chuyên Nghiệp</h6>
                        <p class="mb-0 text-muted small">Đội ngũ hỗ trợ 24/7</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3 d-flex align-items-center">
                    <i class="bi bi-hand-thumbs-up fs-2 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Lựa Chọn Đẳng Cấp</h6>
                        <p class="mb-0 text-muted small">Mọi thứ trong nhà ở cùng một nơi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Featured Products Section --}}
    <section class="featured-products py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Sản phẩm nổi bật</h2>
                <p class="text-muted">Những thiết kế được yêu thích nhất, dẫn đầu xu hướng.</p>
            </div>
            <div class="row g-4">
                @forelse($featuredProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('frontend.components.product-card', ['product' => $product])
                    </div>
                @empty
                    <p class="text-center">Chưa có sản phẩm nổi bật nào.</p>
                @endforelse
            </div>
        </div>
    </section>
    
    {{-- 3. Latest Products Section --}}
    <section class="latest-products py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Hàng mới về</h2>
                <p class="text-muted">Cập nhật những sản phẩm mới nhất trong bộ sưu tập của chúng tôi.</p>
            </div>
             <div class="row g-4">
                @forelse($latestProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('frontend.components.product-card', ['product' => $product])
                    </div>
                @empty
                    <p class="text-center">Chưa có sản phẩm nào.</p>
                @endforelse
            </div>
            <div class="text-center mt-5">
                <a href="#" class="btn btn-outline-primary">Xem tất cả sản phẩm</a>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    .hero-section {
        padding: 4rem 0;
    }
    .hero-slider .swiper-slide {
        display: flex;
        align-items: center;
        min-height: 60vh;
    }
    .hero-title {
        font-size: clamp(2.5rem, 5vw, 4rem); /* Responsive font size */
        font-weight: bold;
        line-height: 1.2;
    }
    .hero-slide-image {
        max-height: 60vh;
        object-fit: contain;
    }
    .swiper-button-next, .swiper-button-prev {
        width: 44px;
        height: 44px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        color: #ff6f61; /* Màu cam giống trong ảnh */
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: background-color 0.2s ease;
    }
    .swiper-button-next:hover, .swiper-button-prev:hover {
        background-color: #fff;
    }
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Khởi tạo SwiperJS
        const swiper = new Swiper('.hero-slider', {
            loop: true, // Lặp lại slide
            speed: 800, // Tốc độ chuyển slide
            autoplay: {
                delay: 5000, // Tự động chuyển sau 5 giây
                disableOnInteraction: false,
            },
            // Kích hoạt nút điều hướng
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    });
</script>
@endpush
