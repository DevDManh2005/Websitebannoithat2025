@extends('layouts.app')

@section('title', 'Trang Chủ - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')

    {{-- ====================================================== --}}
    {{-- 1. HERO BANNER SLIDER --}}
    {{-- ====================================================== --}}
    <section class="hero-section">
        <div class="swiper hero-slider">
            <div class="swiper-wrapper">
                @forelse($slides as $slide)
                    <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('storage/' . $slide->image) }}')">
                        <div class="hero-overlay">
                            <div class="container h-100 d-flex justify-content-center align-items-center text-center">
                                <div class="hero-slide-content text-white">
                                    @if($slide->subtitle)
                                        <p class="text-uppercase text-light small mb-2" data-aos="fade-down">{{ $slide->subtitle }}</p>
                                    @endif
                                    <h1 class="hero-title display-4 fw-bold" data-aos="zoom-in">{{ $slide->title }}</h1>
                                    @if($slide->button_text && $slide->button_link)
                                        <a href="{{ $slide->button_link }}" class="btn btn-light mt-4" data-aos="fade-up">{{ $slide->button_text }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Fallback slide in case there are no slides from the database --}}
                    <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80')">
                        <div class="hero-overlay">
                            <div class="container h-100 d-flex justify-content-center align-items-center text-center">
                                <div class="hero-slide-content text-white">
                                    <p class="text-uppercase text-light small mb-2" data-aos="fade-down">Chào mừng đến với Eterna Home</p>
                                    <h1 class="hero-title display-4 fw-bold" data-aos="zoom-in">Không Gian Sống Đẳng Cấp</h1>
                                    <a href="/san-pham" class="btn btn-light mt-4" data-aos="fade-up">Khám Phá Ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    {{-- ====================================================== --}}
    {{-- 2. KHÁM PHÁ DANH MỤC (THIẾT KẾ MỚI) --}}
    {{-- ====================================================== --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h5 class="text-danger fw-bold text-uppercase">Không Gian Sống Của Bạn</h5>
                <h2 class="fw-bold">Khám Phá Theo Phong Cách</h2>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-4" data-aos="fade-up" data-aos-delay="200">
                @php
                    $categories_sample = [
                        ['name' => 'Phòng Khách', 'image' => 'https://res.cloudinary.com/dfoxknyho/image/upload/v1754716849/img_banner_3_x2xjjr.png', 'link' => '/danh-muc/phong-khach'],
                        ['name' => 'Phòng Ngủ', 'image' => 'https://res.cloudinary.com/dfoxknyho/image/upload/v1754716879/img_banner_4_oz4dkk.png', 'link' => '/danh-muc/phong-ngu'],
                        ['name' => 'Văn Phòng', 'image' => 'https://res.cloudinary.com/dfoxknyho/image/upload/v1754716814/img_banner_2_b3l33z.png', 'link' => '/danh-muc/van-phong'],
                        ['name' => 'Phòng Bếp', 'image' => 'https://res.cloudinary.com/dfoxknyho/image/upload/v1754716907/img_banner_5_czta5t.png', 'link' => '/danh-muc/phong-bep'],
                    ];
                @endphp
                @foreach($categories_sample as $cat)
                <div class="col">
                    <a href="{{ $cat['link'] }}" class="category-card-new">
                        <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}" loading="lazy">
                        <div class="category-card-new-caption">
                            <h5 class="fw-bold">{{ $cat['name'] }}</h5>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ====================================================== --}}
    {{-- 3. SẢN PHẨM BÁN CHẠY --}}
    {{-- ====================================================== --}}
    <section class="py-5 bg-light best-seller-section" data-aos="fade-up">
        <div class="container">
            <h2 class="text-center fw-bold mb-4 text-primary-custom">🔥 Sản phẩm bán chạy nhất</h2>
            <ul class="nav nav-pills justify-content-center mb-4" id="categoryTabs">
                @foreach($categories as $index => $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $category->id }}"
                                data-bs-toggle="pill" data-bs-target="#content-{{ $category->id }}" type="button" role="tab">
                            {{ $category->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" id="categoryTabContent">
                @foreach($categories as $index => $category)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-{{ $category->id }}" role="tabpanel">
                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                            @forelse($category->products->take(4) as $product) {{-- Chỉ lấy 4 sản phẩm --}}
                                <div class="col">
                                    @include('frontend.components.product-card', ['product' => $product])
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-4">Chưa có sản phẩm bán chạy.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ====================================================== --}}
    {{-- 4. ƯU ĐÃI ĐẶC BIỆT --}}
    {{-- ====================================================== --}}
    <section class="special-offer-section-wrapper py-5">
        <div class="container">
            <div class="special-offer-content" data-aos="fade-up">
                <div class="offer-header mb-4" data-aos="fade-down">
                    <div class="offer-timer-wrapper d-flex align-items-center">
                        <div class="flash-label">
                            <i class="bi bi-lightning-fill"></i>
                            <div>
                                <span>CHỈ CÒN:</span>
                                <small>Nhanh Tay Kẻo Lỡ!</small>
                            </div>
                        </div>
                        <div class="countdown d-flex align-items-center" id="countdown-timer">
                            <div class="time-block" id="days-block"><span class="time-value fw-bold" id="days">00</span><span class="time-label">Ngày</span></div>
                            <div class="time-block"><span class="time-value fw-bold" id="hours">00</span></div>
                            <span class="separator">:</span>
                            <div class="time-block"><span class="time-value fw-bold" id="minutes">00</span></div>
                            <span class="separator">:</span>
                            <div class="time-block"><span class="time-value fw-bold" id="seconds">00</span></div>
                        </div>
                        <h3 class="offer-title ms-auto mb-0">Ưu đãi đặc biệt</h3>
                    </div>
                </div>
                <div class="row row-cols-2 row-cols-md-4 g-4">
                    @foreach($specialOfferProducts as $index => $product)
                        <div class="col" data-aos="zoom-in" data-aos-delay="{{ 100 + $index * 100 }}">
                            @include('frontend.components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ====================================================== --}}
    {{-- 5. VÌ SAO CHỌN ETERNA HOME (GỘP) --}}
    {{-- ====================================================== --}}
    <section class="py-5 why-choose-us-reimagined">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716983/img_banner_whychoose_ebpgk2.jpg" alt="Vì sao chọn Eterna Home" class="img-fluid rounded-3 shadow-lg">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h5 class="text-danger fw-bold text-uppercase">Cam Kết Của Chúng Tôi</h5>
                    <h2 class="fw-bold mb-4">Kiến tạo không gian, nâng tầm cuộc sống</h2>
                    <p class="text-muted mb-4">
                        Tại Eterna Home, chúng tôi không chỉ bán nội thất. Chúng tôi mang đến giải pháp toàn diện, kết hợp giữa thiết kế tinh tế, chất lượng vượt trội và dịch vụ tận tâm để tạo nên không gian sống hoàn hảo cho bạn.
                    </p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="bi bi-gem"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Thiết Kế Đẳng Cấp</h6>
                                <p class="small text-muted">Sản phẩm được chọn lọc, cập nhật xu hướng mới nhất.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-award"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Chất Lượng Hàng Đầu</h6>
                                <p class="small text-muted">Vật liệu cao cấp, độ bền đã được kiểm chứng.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-truck"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Dịch Vụ Tận Tâm</h6>
                                <p class="small text-muted">Giao hàng, lắp đặt và bảo hành chuyên nghiệp.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    {{-- ====================================================== --}}
    {{-- 6. ĐÁNH GIÁ TỪ KHÁCH HÀNG --}}
    {{-- ====================================================== --}}
    <section class="py-5 bg-light" data-aos="fade-up">
        <div class="container">
            <h2 class="text-center fw-bold mb-4 text-primary-custom">Khách hàng nói gì về Eterna Home</h2>
            <div class="swiper review-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">@include('frontend.components.review-card', ['name' => 'Phan Thu Hoài', 'content' => 'Sofa rất ưng ý, form dáng hiện đại, chất vải mềm mịn.', 'avatar' => 'https://i.pravatar.cc/100?img=1'])</div>
                    <div class="swiper-slide">@include('frontend.components.review-card', ['name' => 'Trần Minh Quang', 'content' => 'Nhân viên tư vấn nhiệt tình, giao hàng còn đẹp hơn mong đợi.', 'avatar' => 'https://i.pravatar.cc/100?img=2'])</div>
                    <div class="swiper-slide">@include('frontend.components.review-card', ['name' => 'Lê Thị Hồng Nhung', 'content' => 'Giường ngủ chắc chắn, nằm êm. Dịch vụ lắp đặt tận nơi rất tiện.', 'avatar' => 'https://i.pravatar.cc/100?img=3'])</div>
                    <div class="swiper-slide">@include('frontend.components.review-card', ['name' => 'Đặng Quốc Tuấn', 'content' => 'Chuyên nghiệp từ khâu xác nhận đơn hàng đến lắp đặt. Rất hài lòng.', 'avatar' => 'https://i.pravatar.cc/100?img=4'])</div>
                </div>
                <div class="swiper-pagination mt-4"></div>
            </div>
        </div>
    </section>

    {{-- ====================================================== --}}
    {{-- 7. BÀI VIẾT MỚI --}}
    {{-- ====================================================== --}}
    <section class="py-5 bg-white blog-section" data-aos="fade-up">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="fw-bold mb-0 text-primary-custom">📰 Cẩm Nang Nội Thất</h2>
                <a href="{{ route('blog.index') }}" class="text-danger fw-semibold">Xem tất cả →</a>
            </div>
            @if(!empty($latestPosts) && $latestPosts->count())
                <div class="swiper blog-swiper">
                    <div class="swiper-wrapper">
                        @foreach($latestPosts as $post)
                            <div class="swiper-slide">
                                @php
                                    $thumb = $post->thumbnail ? asset('storage/'.$post->thumbnail) : 'https://picsum.photos/seed/blog'.$post->id.'/640/400';
                                    $url   = route('blog.show', $post->slug ?? $post->id);
                                    $date  = optional($post->published_at ?? $post->created_at)->format('d/m/Y');
                                    $excerpt = \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->content ?? ''), 110);
                                @endphp
                                <article class="blog-card h-100">
                                    <a href="{{ $url }}" class="blog-thumb d-block">
                                        <img src="{{ $thumb }}" alt="{{ $post->title }}" loading="lazy">
                                    </a>
                                    <div class="p-3">
                                        <div class="blog-meta small text-muted mb-1">
                                            <i class="bi bi-calendar-check me-1"></i> {{ $date ?? '' }}
                                        </div>
                                        <h5 class="fw-bold blog-title">
                                            <a href="{{ $url }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                                        </h5>
                                        <p class="text-muted mb-3">{{ $excerpt }}</p>
                                        <a href="{{ $url }}" class="btn btn-sm btn-outline-danger">Đọc tiếp</a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination blog-pagination mt-3"></div>
                    <div class="swiper-button-prev blog-prev d-none d-lg-flex"></div>
                    <div class="swiper-button-next blog-next d-none d-lg-flex"></div>
                </div>
            @else
                <div class="text-center text-muted py-5">Chưa có bài viết nào.</div>
            @endif
        </div>
    </section>

@endsection


@push('styles')
<style>
    /* ============================================ */
    /* == CSS GỐC VÀ CSS CHO CẤU TRÚC MỚI == */
    /* ============================================ */
    :root { 
        --main-color: #A20E38; 
    }

    /* --- Hero Section --- */
    .hero-section { position: relative; height: 700px; }
    .hero-slide { height: 700px; background-size: cover; background-position: center; position: relative; }
    .hero-overlay { background: rgba(0, 0, 0, 0.4); width: 100%; height: 100%; }
    .hero-title { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: bold; line-height: 1.2; }
    .swiper-button-next, .swiper-button-prev {
        width: 44px; height: 44px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        color: #ff6f61;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: background-color 0.2s ease;
    }
    .swiper-button-next:hover, .swiper-button-prev:hover { background-color: #fff; }
    .swiper-button-next:after, .swiper-button-prev:after { font-size: 1.2rem; font-weight: bold; }
    
    /* --- CSS CHO CẤU TRÚC MỚI --- */
    .category-card-new {
        display: block;
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .category-card-new:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    }
    .category-card-new img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        aspect-ratio: 3/4;
        transition: transform 0.4s ease;
    }
    .category-card-new:hover img {
        transform: scale(1.05);
    }
    .category-card-new-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem 1.25rem;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
        color: white;
        text-align: center;
    }
    .why-choose-us-reimagined .feature-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-top: 2rem;
    }
    .why-choose-us-reimagined .feature-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .why-choose-us-reimagined .feature-item i {
        font-size: 2rem;
        color: var(--bs-danger);
        flex-shrink: 0;
    }

    /* --- Best Seller Section --- */
    .best-seller-section .nav-pills .nav-link {
        color: #555; border: 1px solid var(--main-color);
        background-color: transparent; margin: 0 4px;
        border-radius: 50px; transition: all 0.3s ease;
    }
    .best-seller-section .nav-pills .nav-link:hover { background-color: #f8e8ec; color: var(--main-color); }
    .best-seller-section .nav-pills .nav-link.active { background-color: var(--main-color); color: #fff; }

    /* --- Special Offer Section --- */
    .special-offer-section-wrapper {
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://res.cloudinary.com/dfoxknyho/image/upload/v1754716939/pngtree-empty-wooden-table-top-on-a-blurred-background-of-a-modern-image_16376700_iwce5y.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 4rem 0;
        overflow: hidden;
    }
    .special-offer-content {
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 4px 15px rgba(0,0,0,0.1);
        border-top: 4px solid #ff9900;
    }
    .special-offer-section-wrapper .offer-header {
        background-color: #fffaf0;
        border: 1px solid #eee;
        border-radius: 50px;
        padding: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        gap: 1rem;
    }
    .special-offer-section-wrapper .offer-timer-wrapper { display: flex; align-items: center; gap: 1rem; width: 100%; }
    .special-offer-section-wrapper .flash-label {
        background: linear-gradient(45deg, #ffc107, #ff9900);
        color: #000;
        padding: 10px 20px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .special-offer-section-wrapper .flash-label i { font-size: 1.5rem; }
    .special-offer-section-wrapper .flash-label div { display: flex; flex-direction: column; line-height: 1.1; }
    .special-offer-section-wrapper .flash-label small { font-size: 0.7rem; font-weight: 500; opacity: 0.8; }
    .special-offer-section-wrapper .countdown { display: flex; align-items: center; gap: 0.5rem; }
    .special-offer-section-wrapper .time-block {
        background: linear-gradient(145deg, #e53935, #b71c1c);
        color: white;
        padding: 8px 14px;
        border-radius: 8px;
        text-align: center;
        min-width: 55px;
        line-height: 1;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.25), 0 2px 4px rgba(0,0,0,0.2);
    }
    .special-offer-section-wrapper .time-block .time-value { font-size: 2rem; font-weight: 900; text-shadow: 1px 1px 2px rgba(0,0,0,0.2); }
    .special-offer-section-wrapper .time-block .time-label { font-size: 0.6rem; display: block; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.5px; }
    .special-offer-section-wrapper .separator { font-size: 2rem; color: #333; font-weight: 700; }
    .special-offer-section-wrapper .offer-title {
        font-weight: 800;
        font-size: 1.5rem;
        margin: 0 0 0 auto;
        padding-right: 1.5rem;
        white-space: nowrap;
        background: linear-gradient(45deg, #b71c1c, #e53935);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text; text-fill-color: transparent;
    }

    /* --- Review Section --- */
    .review-swiper .swiper-slide { display: flex; flex-direction: column; }
    .review-card { background-color: #fffaf5; border-radius: 10px; height: 100%; }

    /* --- Blog Section --- */
    .blog-card{
        border:1px solid #eee; border-radius:14px; overflow:hidden; background:#fff;
        box-shadow:0 6px 18px rgba(0,0,0,.06); transition:transform .25s, box-shadow .25s;
    }
    .blog-card:hover{ transform:translateY(-4px); box-shadow:0 10px 26px rgba(0,0,0,.10); }
    .blog-thumb{ aspect-ratio: 16/10; overflow:hidden; }
    .blog-thumb img{ width:100%; height:100%; object-fit:cover; transition:transform .4s; display:block; }
    .blog-card:hover .blog-thumb img{ transform:scale(1.05); }
    .blog-title{ line-height:1.25; }
    .blog-section .swiper{ overflow:visible; }
    .blog-section .swiper-button-prev,
    .blog-section .swiper-button-next{
        width:44px; height:44px; background:#fff; border-radius:50%;
        box-shadow:0 4px 14px rgba(0,0,0,.12); z-index:5;
    }
    .blog-section .swiper-button-prev:after,
    .blog-section .swiper-button-next:after{ font-size:1rem; color:#333; }
    .blog-section .swiper-pagination-bullet{ opacity:.5; }
    .blog-section .swiper-pagination-bullet-active{ background: var(--main-color); opacity:1; }


    /*
    ============================================
    == CODE RESPONSIVE TỐI ƯU CHO TABLET & MOBILE
    ============================================
    */

    /* --- Màn hình Tablet (bước đệm) --- */
    @media (max-width: 991.98px) {
        .hero-section, .hero-slide {
            height: 600px;
        }
        .special-offer-section-wrapper .offer-header {
            flex-direction: column;
            border-radius: 12px;
            align-items: stretch;
            text-align: center;
        }
        .special-offer-section-wrapper .offer-timer-wrapper {
            flex-direction: column;
            gap: 1rem;
        }
        .special-offer-section-wrapper .offer-title {
            margin: 0.5rem 0 0 0;
            padding: 0;
        }
    }

    /* --- Màn hình Mobile (Tối ưu chính) --- */
    @media (max-width: 767.98px) {
        /* == CHUNG == */
        body { font-size: 15px; }
        h1, .h1 { font-size: 2rem; }
        h2, .h2 { font-size: 1.75rem; }
        h3, .h3 { font-size: 1.5rem; }
        h4, .h4 { font-size: 1.25rem; }
        .py-5 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }

        /* == 1. Hero Section == */
        .hero-section, .hero-slide { height: 60vh; min-height: 450px; }
        .hero-title { font-size: clamp(2rem, 8vw, 2.5rem); }
        .swiper-button-next, .swiper-button-prev { display: none; }

        /* == 2. Danh mục sản phẩm == */
        .category-card-new img { aspect-ratio: 4/5; }
        
        /* == 4. Ưu đãi đặc biệt == */
        .special-offer-content { padding: 1.5rem 1rem; }
        .special-offer-section-wrapper .flash-label { width: 100%; justify-content: center; }
        .special-offer-section-wrapper .time-block .time-value { font-size: 1.5rem; }
        .special-offer-section-wrapper .time-block { min-width: 45px; padding: 6px 10px; }
        .special-offer-section-wrapper .separator { font-size: 1.5rem; }
        .special-offer-section-wrapper .offer-title { font-size: 1.25rem; }

        /* == 5. Về Chúng Tôi == */
        .why-choose-us-reimagined .feature-item { flex-direction: column; text-align: center; gap: 0.5rem; }

        /* == 8. Sản phẩm bán chạy == */
        .best-seller-section .nav-pills {
            flex-wrap: nowrap;
            overflow-x: auto;
            justify-content: flex-start !important;
            padding-bottom: 10px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .best-seller-section .nav-pills::-webkit-scrollbar { display: none; }
        .best-seller-section .nav-pills .nav-link { white-space: nowrap; }

        /* == 7. Bài viết mới (Blog) == */
        .blog-section .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts-page')
<script>
    // 1. Khởi tạo Swiper cho Hero Slider
    if (document.querySelector('.hero-slider')) {
        const heroSwiper = new Swiper('.hero-slider', {
            loop: true,
            speed: 800,
            autoplay: { delay: 5000, disableOnInteraction: false },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }

    // 2. Khởi tạo Swiper cho Review Section
    if (document.querySelector('.review-swiper')) {
        const reviewSwiper = new Swiper(".review-swiper", {
            loop: true,
            pagination: { el: ".swiper-pagination", clickable: true },
            autoplay: { delay: 5500, disableOnInteraction: false },
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 20 },
                992: { slidesPerView: 3, spaceBetween: 30 }
            }
        });
    }

    // 3. Hiệu ứng đếm số
    const counters = document.querySelectorAll('.counter');
    if (counters.length > 0) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const target = parseInt(el.dataset.target, 10);
                const duration = 1500;
                let start = 0;
                const stepTime = Math.abs(Math.floor(duration / target)) || 1;
                const timer = setInterval(() => {
                    start += 1;
                    el.innerText = start;
                    if (start >= target) {
                        el.innerText = target;
                        clearInterval(timer);
                    }
                }, stepTime);
                observer.unobserve(el);
            });
        }, { threshold: 0.5 });
        counters.forEach(el => observer.observe(el));
    }

    // 4. Đồng hồ đếm ngược
    const countdownContainer = document.getElementById("countdown-timer");
    if (countdownContainer) {
        const daysEl = document.getElementById("days");
        const hoursEl = document.getElementById("hours");
        const minutesEl = document.getElementById("minutes");
        const secondsEl = document.getElementById("seconds");
        const saleEndTime = new Date("2025-12-31T23:59:59").getTime(); // Cập nhật ngày kết thúc sale

        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = saleEndTime - now;

            if (distance <= 0) {
                const offerSection = document.querySelector('.special-offer-section-wrapper');
                if (offerSection) offerSection.style.display = 'none';
                clearInterval(countdownInterval);
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            daysEl.innerText = String(days).padStart(2, '0');
            hoursEl.innerText = String(hours).padStart(2, '0');
            minutesEl.innerText = String(minutes).padStart(2, '0');
            secondsEl.innerText = String(seconds).padStart(2, '0');
        };
        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown();
    }

    // 5. Swiper cho Blog
    if (document.querySelector('.blog-swiper')) {
        const blogSwiper = new Swiper('.blog-swiper', {
            loop: false,
            speed: 600,
            spaceBetween: 24,
            autoplay: { delay: 6000, disableOnInteraction: false },
            pagination: { el: '.blog-pagination', clickable: true },
            navigation: { nextEl: '.blog-next', prevEl: '.blog-prev' },
            slidesPerView: 1,
            breakpoints: {
                576: { slidesPerView: 1 },
                768: { slidesPerView: 2 },
                1200:{ slidesPerView: 3 }
            }
        });
    }
</script>
@endpush