@extends('layouts.app')

@section('title', 'Trang Chủ - ' . ($settings['site_name'] ?? config('app.name')))

@php
    // --- DỮ LIỆU TĨNH CHO VIEW ---
    // Dữ liệu cho section #2 (Đặc điểm nổi bật)
    $features = [
        ['icon' => 'bi-tag', 'title' => 'Giá Trị Tuyệt Vời Mỗi Ngày', 'desc' => 'Giá cả phù hợp với ngân sách của bạn'],
        ['icon' => 'bi-truck', 'title' => 'Miễn Phí Vận Chuyển', 'desc' => 'Giao hàng phổ biến trong 1 - 2 ngày'],
        ['icon' => 'bi-award', 'title' => 'Dịch Vụ Khách Hàng Chuyên Nghiệp', 'desc' => 'Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ 24/7'],
        ['icon' => 'bi-hand-thumbs-up', 'title' => 'Lựa Chọn Không Thể Đánh Bại', 'desc' => 'Mọi thứ trong nhà đều ở cùng một nơi'],
    ];

    // Dữ liệu cho section #3 (Thống kê Về Chúng Tôi)
    $aboutStats = [
        ['num' => '1600', 'label' => 'Sản phẩm hoàn thiện'],
        ['num' => '180', 'label' => 'Mẫu mã đa dạng'],
        ['num' => '38', 'label' => 'Đối tác uy tín toàn quốc'],
    ];

    // Dữ liệu cho section #9 (Quy trình làm việc)
    $workProcessSteps = [
        ['icon' => 'bi-house-door', 'title' => 'Tư vấn và chọn sản phẩm', 'desc' => 'Tư vấn chi tiết mẫu, giúp lựa chọn sản phẩm nội thất phù hợp với nhu cầu và không gian.'],
        ['icon' => 'bi-file-earmark-check', 'title' => 'Đặt hàng và xác nhận', 'desc' => 'Xác nhận đơn hàng, kiểm tra thông tin và gửi báo giá chi tiết.'],
        ['icon' => 'bi-truck', 'title' => 'Sản xuất và giao hàng', 'desc' => 'Sản xuất sản phẩm theo đơn hàng và giao tận nơi cho khách hàng.'],
        ['icon' => 'bi-clipboard-check', 'title' => 'Kiểm tra và hỗ trợ', 'desc' => 'Kiểm tra chất lượng sản phẩm khi giao, hỗ trợ lắp đặt và bảo hành.'],
    ];

    // Dữ liệu cho section #10 (Lý do chọn Eterna Home)
    $whyChooseUsFeatures = [
        ['icon' => 'fas fa-gem', 'title' => 'Chất lượng và thẩm mỹ vượt trội', 'desc' => 'Thiết kế tinh tế và sang trọng, đảm bảo sự hài hòa và đẳng cấp với chất liệu chọn lọc, bền đẹp và phù hợp với mọi không gian.'],
        ['icon' => 'fas fa-hand-holding-heart', 'title' => 'Dịch vụ chuyên nghiệp, tận tâm', 'desc' => 'Từ tư vấn đến lắp đặt, Eterna luôn phục vụ khách hàng với sự tận tâm, đảm bảo trải nghiệm mua sắm hoàn hảo.'],
    ];

    // Dữ liệu cho section #12 (Đánh giá) - Giữ nguyên để dễ chỉnh sửa
    $reviews = [
        ['name' => 'Phan Thu Hoài', 'content' => 'Chiếc sofa màu be thực sự là điểm nhấn cho phòng khách nhà mình. Chất vải mềm mịn, form dáng hiện đại. Rất ưng ý!', 'avatar' => 'https://i.pravatar.cc/100?img=1'],
        ['name' => 'Trần Minh Quang', 'content' => 'Lúc đầu cũng hơi ngại mua bàn ăn giá trị cao online, nhưng các bạn nhân viên tư vấn rất kiên nhẫn, gửi ảnh thật chi tiết. Nhận hàng còn đẹp hơn mong đợi.', 'avatar' => 'https://i.pravatar.cc/100?img=2'],
        ['name' => 'Lê Thị Hồng Nhung', 'content' => 'Giường ngủ chắc chắn, nằm rất êm. Từ ngày có giường mới cả nhà mình ngủ ngon hơn hẳn. Giao hàng và lắp đặt tận nơi nên mình không phải lo gì cả.', 'avatar' => 'https://i.pravatar.cc/100?img=3'],
        ['name' => 'Đặng Quốc Tuấn', 'content' => 'Mọi thứ rất chuyên nghiệp, từ khâu xác nhận đơn hàng đến việc giao và lắp đặt. Các bạn đến đúng hẹn, làm việc nhanh gọn. Rất hài lòng với dịch vụ của Eterna.', 'avatar' => 'https://i.pravatar.cc/100?img=4'],
        ['name' => 'Hoàng Mai Anh', 'content' => 'Tìm mãi mới được chiếc kệ tivi phong cách tối giản hợp ý. Lắp lên phòng khách trông gọn gàng và sang trọng hơn hẳn. Chất gỗ sờ rất thích tay.', 'avatar' => 'https://i.pravatar.cc/100?img=5'],
        ['name' => 'Vũ Tiến Dũng', 'content' => 'Đã mua hàng ở đây 2 lần. Lần nào cũng hài lòng tuyệt đối. Sản phẩm dùng bền, sau một năm vẫn như mới. Sẽ tiếp tục ủng hộ shop.', 'avatar' => 'https://i.pravatar.cc/100?img=6'],
    ];
@endphp

@section('content')

    {{-- 1. Phần Hero Banner Slider --}}
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
                    <div class="swiper-slide hero-slide" style="background-image: url('https://res.cloudinary.com/dfoxknyho/image/upload/v1754716939/pngtree-empty-wooden-table-top-on-a-blurred-background-of-a-modern-image_16376700_iwce5y.jpg')">
                        <div class="hero-overlay">
                            <div class="container h-100 d-flex justify-content-center align-items-center text-center">
                                <div class="hero-slide-content text-white">
                                    <h1 class="hero-title display-4 fw-bold" data-aos="zoom-in">Chào Mừng Đến Eterna Home</h1>
                                    <a href="{{ route('products.index') }}" class="btn btn-light mt-4" data-aos="fade-up">Xem Sản Phẩm</a>
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

    <br>

    {{-- 2. Phần giới thiệu các đặc điểm nổi bật --}}
    <section class="features-section py-0 border-bottom" data-aos="fade-up">
        <div class="container">
            <div class="row text-center">
                @foreach($features as $feature)
                    <div class="col-6 col-lg-3 mb-4">
                        <i class="bi {{ $feature['icon'] }}" style="font-size: 5.5rem;"></i>
                        <h6 class="mt-3 fw-bold">{{ $feature['title'] }}</h6>
                        <p class="text-muted small">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 3. Phần giới thiệu Về Chúng Tôi --}}
    <section class="about-section py-5" data-aos="fade-up">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h4 class="text-uppercase text-danger fw-bold mb-3">Về Chúng Tôi</h4>
                    <h1 class="fw-bold mb-4">Giải pháp nội thất hoàn hảo cho không gian của bạn</h1>
                    <p class="text-muted mb-5" style="font-size: 20px">
                        ND Interior là đơn vị chuyên cung cấp các sản phẩm nội thất cao cấp dành cho nhà ở, biệt thự, căn hộ, văn phòng và showroom. Với sứ mệnh mang đến không gian sống và làm việc đẳng cấp, chúng tôi cam kết mang lại những sản phẩm chất lượng cao, thiết kế tinh tế, phù hợp với phong cách cá nhân của mỗi khách hàng.
                    </p>
                    <div class="row text-center">
                        @foreach ($aboutStats as $stat)
                            <div class="col-4">
                                <div class="p-3 rounded shadow-sm bg-light hover-shadow">
                                    <h4 class="text-danger fw-bold counter" data-target="{{ $stat['num'] }}">0</h4>
                                    <p class="small text-muted mb-0">{{ $stat['label'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0 d-flex justify-content-center">
                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716482/img_mobile_about_xpylyh.jpg" alt="Nội thất" class="about-img img-fluid" data-aos="flip-left" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Phần hiển thị đối tác --}}
    @include('frontend.components.brand-carousel', ['brands' => $brands ?? \App\Models\Brand::active()->take(6)->get()])
    <br>

    {{-- 5. Phần hiển thị danh mục sản phẩm (giữ nguyên cấu trúc grid đặc thù) --}}
    <section class="home-category">
        <div class="container">
            <div class="row row-margin">
                <div class="col-lg-3 col-md-3 col-12 col-padding d-md-block d-none" data-aos="fade-right">
                    <div class="category-item category-item-large">
                        <a class="category-thumb" href="/san-pham" title="Sản phẩm mới">
                            <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716780/img_banner_1_pnlot5.png" alt="Sản phẩm mới" loading="lazy">
                            <div class="category-caption">
                                <h3>SẢN PHẨM MỚI</h3>
                                <p>20+ sản phẩm mới giá khuyến mãi</p>
                                <span>Xem tất cả →</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-12 col-padding">
                    <div class="row row-margin">
                        <div class="col-lg-5 col-md-5 col-5 col-padding" data-aos="fade-up">
                            <div class="category-item"><a class="category-thumb" href="/danh-muc/van-phong" title="Văn phòng"><img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716814/img_banner_2_b3l33z.png" alt="Văn phòng" loading="lazy"><div class="category-caption"><h3>Văn phòng</h3><p>Ghế, bàn làm việc, tủ sách, đèn….</p><span>Xem tất cả →</span></div></a></div>
                        </div>
                        <div class="col-lg-7 col-md-7 col-7 col-padding" data-aos="fade-up">
                            <div class="category-item"><a class="category-thumb" href="/danh-muc/phong-khach" title="Phòng khách"><img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716849/img_banner_3_x2xjjr.png" alt="Phòng khách" loading="lazy"><div class="category-caption"><h3>Phòng Khách</h3><p>Bàn, sofa, ghế đôn, đèn, tủ tivi, bàn bên…</p><span>Xem tất cả →</span></div></a></div>
                        </div>
                    </div>
                    <div class="row row-margin">
                        <div class="col-lg-7 col-md-7 col-7 col-padding" data-aos="fade-up">
                            <div class="category-item"><a class="category-thumb" href="/danh-muc/phong-ngu" title="Phòng ngủ"><img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716879/img_banner_4_oz4dkk.png" alt="Phòng ngủ" loading="lazy"><div class="category-caption"><h3>Phòng ngủ</h3><p>Giường, tủ quần áo, gương, tủ đầu giường…</p><span>Xem tất cả →</span></div></a></div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-5 col-padding" data-aos="fade-up">
                            <div class="category-item"><a class="category-thumb" href="/danh-muc/phong-bep" title="Phòng bếp"><img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716907/img_banner_5_czta5t.png" alt="Phòng bếp" loading="lazy"><div class="category-caption"><h3>Phòng bếp</h3><p>Bàn ăn, ghế, kệ…</p><span>Xem tất cả →</span></div></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Phần ưu đãi đặc biệt --}}
    <section class="special-offer-section-wrapper py-5">
        {{-- (Nội dung giữ nguyên) --}}
    </section>

    {{-- 7. Phần mã giảm giá (voucher) --}}
    <section class="voucher-section py-5 bg-light">
        {{-- (Nội dung giữ nguyên) --}}
    </section>

    {{-- 8. Phần sản phẩm bán chạy theo danh mục --}}
    <section class="py-5 bg-white best-seller-section" data-aos="fade-up">
        {{-- (Nội dung giữ nguyên) --}}
    </section>

    {{-- 9. Phần quy trình làm việc --}}
    <section class="py-5 bg-white" data-aos="fade-up">
        <div class="container text-center">
            <h4 class="text-danger fw-bold mb-2" data-aos="fade-up" data-aos-delay="100">Quy trình làm việc</h4>
            <h2 class="fw-semibold mb-5" data-aos="fade-up" data-aos-delay="200">Cam kết chất lượng từ <span class="text-danger">Eterna Home</span></h2>
            <div class="row justify-content-center">
                @foreach ($workProcessSteps as $index => $step)
                    <div class="col-6 col-md-3 mb-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="border rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 96px; height: 96px; border: 2px solid #A20E38;">
                            <i class="bi {{ $step['icon'] }} fs-1 text-danger"></i>
                        </div>
                        <h6 class="fw-bold">{{ $step['title'] }}</h6>
                        <p class="text-muted small">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 10. Phần lý do chọn Eterna Home --}}
    <section class="py-5 why-choose-section bg-white" data-aos="fade-up">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h5 class="subtitle text-primary-custom">Vì sao chọn Eterna Home?</h5>
                    <h2 class="section-title">Eterna Home luôn ưu tiên sự hài lòng khách hàng</h2>
                    <p class="section-desc">Eterna Home cam kết chất lượng, thẩm mỹ và sự hài lòng. Với đội ngũ tư vấn giàu kinh nghiệm, sản phẩm thiết kế đạt cao cùng với hỗ trợ tận tâm, chúng tôi mang đến giải pháp nội thất hoàn hảo cho không gian của bạn.</p>
                    @foreach($whyChooseUsFeatures as $index => $feature)
                        <div class="feature-box" data-aos="fade-up" data-aos-delay="{{ 100 + $index * 100 }}">
                            <div class="icon-circle text-primary-custom">
                                <i class="fas {{ $feature['icon'] }}"></i>
                            </div>
                            <div>
                                <h6 class="feature-title">{{ $feature['title'] }}</h6>
                                <p class="feature-text">{{ $feature['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-lg-6" data-aos="zoom-in-left" data-aos-delay="300">
                    <div class="image-wrapper position-relative">
                        <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716983/img_banner_whychoose_ebpgk2.jpg" alt="Eterna Home" loading="lazy" class="img-fluid rounded shadow"/>
                        <div class="image-border"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 11. Phần câu hỏi thường gặp (FAQ) --}}
    <section class="py-5 bg-white">
        {{-- (Nội dung giữ nguyên) --}}
    </section>

    {{-- 12. Phần đánh giá từ khách hàng --}}
    <section class="py-5 bg-light" data-aos="fade-up">
        <div class="container">
            <h2 class="text-center fw-bold mb-4 text-primary-custom">Khách hàng nói gì về Eterna Home</h2>
            <div class="swiper review-swiper">
                <div class="swiper-wrapper">
                    @foreach($reviews as $review)
                        <div class="swiper-slide">
                            @include('frontend.components.review-card', $review)
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination mt-4"></div>
            </div>
        </div>
    </section>

    {{-- 13. Bài viết mới (Blog) --}}
    <section class="py-5 bg-white blog-section" data-aos="fade-up">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="fw-bold mb-0 text-primary-custom">📰 Bài viết mới</h2>
                <a href="{{ route('blog.index') }}" class="text-danger fw-semibold">Xem tất cả →</a>
            </div>
            @if(!empty($latestPosts) && $latestPosts->count())
                <div class="swiper blog-swiper">
                    <div class="swiper-wrapper">
                        @foreach($latestPosts as $post)
                            <div class="swiper-slide">
                                <article class="blog-card h-100">
                                    <a href="{{ $post->url }}" class="blog-thumb d-block">
                                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="lazy">
                                    </a>
                                    <div class="p-3">
                                        <div class="blog-meta small text-muted mb-1">
                                            <i class="bi bi-calendar-check me-1"></i> {{ $post->formatted_date }}
                                        </div>
                                        <h5 class="fw-bold blog-title">
                                            <a href="{{ $post->url }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                                        </h5>
                                        <p class="text-muted mb-3">{{ $post->excerpt }}</p>
                                        <a href="{{ $post->url }}" class="btn btn-sm btn-outline-danger">Đọc tiếp</a>
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

{{-- ======================================================= --}}
{{-- =================== STYLES & SCRIPTS ==================== --}}
{{-- ======================================================= --}}

@push('styles')
<style>
    /* --- CSS ĐÃ ĐƯỢC TỔ CHỨC VÀ BỔ SUNG RESPONSIVE --- */
    :root { --main-color: #A20E38; }

    /* --- 1. Hero Section --- */
    .hero-section { position: relative; height: 700px; }
    .hero-slide { height: 100%; background-size: cover; background-position: center; position: relative; }
    .hero-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.4); }
    .hero-title { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: bold; line-height: 1.2; }
    .hero-section .swiper-button-next, 
    .hero-section .swiper-button-prev {
        width: 44px; height: 44px; background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%; color: var(--main-color); box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: background-color 0.2s ease;
    }
    .hero-section .swiper-button-next:hover, 
    .hero-section .swiper-button-prev:hover { background-color: #fff; }
    .hero-section .swiper-button-next::after, 
    .hero-section .swiper-button-prev::after { font-size: 1.2rem; font-weight: bold; }

    /* --- 2. Features Section --- */
    .features-icon { font-size: 5.5rem; color: var(--main-color); }
    
    /* --- 3. About Section --- */
    .about-img { width: 500px; height: 600px; object-fit: cover; border-radius: 1rem; box-shadow: 0 0 10px rgba(0,0,0,0.1); transition: all 0.3s ease; }
    .about-img:hover { transform: scale(1.03); box-shadow: 0 0 25px rgba(0,0,0,0.25); }
    .hover-shadow { transition: all 0.3s ease; cursor: pointer; }
    .hover-shadow:hover { background-color: #ffe6e6; box-shadow: 0 4px 20px rgba(0,0,0,0.2); transform: translateY(-3px); border: 1px solid #ffcccc; }

    /* --- 5. Category Section --- */
    .home-category .category-item { position: relative; overflow: hidden; border-radius: 12px; transition: transform 0.3s ease, box-shadow 0.3s ease; margin: 10px; }
    .home-category .category-item:hover { transform: translateY(-6px); box-shadow: 0 10px 24px rgba(0,0,0,0.15); }
    .category-thumb img { width: 100%; height: 100%; display: block; object-fit: cover; border-radius: 12px; }
    .category-caption { /* ... */ }

    /* --- 6 & 7. Special Offer & Voucher --- */
    /* (Giữ nguyên các style phức tạp và animations) */
    .special-offer-section-wrapper { /* ... */ }
    /* ... */

    /* --- 8. Best Seller Section --- */
    .best-seller-section .nav-pills .nav-link { color: #555; border: 1px solid var(--main-color); background-color: transparent; margin: 0 4px; border-radius: 50px; transition: all 0.3s ease; }
    .best-seller-section .nav-pills .nav-link:hover { background-color: #f8e8ec; color: var(--main-color); }
    .best-seller-section .nav-pills .nav-link.active { background-color: var(--main-color); color: #fff; }

    /* --- 10. Why Choose Us --- */
    .why-choose-section .feature-box { /* ... */ }

    /* --- 12. Review Section --- */
    .review-swiper { padding-bottom: 40px; }
    .review-swiper .swiper-slide { height: auto; display: flex; }
    .review-swiper .review-card { flex-grow: 1; background-color: #fff; border: 1px solid #eee; }
    
    /* --- 13. Blog Section --- */
    .blog-card { border:1px solid #eee; border-radius:14px; overflow:hidden; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,.06); transition:transform .25s, box-shadow .25s; }
    .blog-card:hover { transform:translateY(-4px); box-shadow:0 10px 26px rgba(0,0,0,.10); }
    .blog-thumb { aspect-ratio: 16/10; overflow:hidden; }
    .blog-thumb img { width:100%; height:100%; object-fit:cover; transition:transform .4s; display:block; }
    .blog-card:hover .blog-thumb img { transform:scale(1.05); }
    .blog-title { line-height:1.25; }
    .blog-section .swiper { overflow:visible; }
    .blog-section .swiper-button-prev, .blog-section .swiper-button-next { width:44px; height:44px; background:#fff; border-radius:50%; box-shadow:0 4px 14px rgba(0,0,0,.12); z-index:5; }
    .blog-section .swiper-button-prev::after, .blog-section .swiper-button-next::after { font-size:1rem; color:#333; }
    .blog-section .swiper-pagination-bullet { opacity:.5; }
    .blog-section .swiper-pagination-bullet-active { background: var(--main-color); opacity:1; }

    /* =================== RESPONSIVE CSS ==================== */
    @media (max-width: 991.98px) { /* Tablet */
        .hero-section, .hero-slide { height: 600px; }
        .about-img { width: 100%; height: 500px; margin-top: 2rem; }
    }

    @media (max-width: 767.98px) { /* Mobile */
        .hero-section, .hero-slide { height: 550px; }
        .hero-section .swiper-button-next, .hero-section .swiper-button-prev { display: none; }
        .hero-title { font-size: clamp(2rem, 8vw, 2.8rem); }
        .features-icon { font-size: 4rem !important; }
        .about-img { height: 450px; }
        .about-section .text-muted { font-size: 16px !important; }
        .home-category .category-item-large { display: none !important; }
        .home-category .col-lg-9.col-md-9 { width: 100%; flex: 0 0 100%; max-width: 100%; }
        .category-caption h3 { font-size: 1.1rem; }
        .category-caption p { font-size: 0.85rem; }
        /* (Các style responsive khác cho Special Offer,...) */
    }

    @media (max-width: 575.98px) { /* Small Mobile */
        .hero-section, .hero-slide { height: 500px; }
        .best-seller-section .nav-pills { flex-wrap: nowrap; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 0.5rem; }
        .best-seller-section .nav-pills::-webkit-scrollbar { display: none; }
        .best-seller-section .nav-pills .nav-link { white-space: nowrap; }
    }
</style>
@endpush

@push('scripts-page')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Hero Slider
        if (document.querySelector('.hero-slider')) {
            new Swiper('.hero-slider', {
                loop: true, speed: 800, autoplay: { delay: 5000, disableOnInteraction: false },
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            });
        }

        // 2. Review Slider
        if (document.querySelector('.review-swiper')) {
            new Swiper(".review-swiper", {
                loop: true, slidesPerView: 1, spaceBetween: 20,
                pagination: { el: ".swiper-pagination", clickable: true },
                autoplay: { delay: 5500, disableOnInteraction: false },
                breakpoints: { 768: { slidesPerView: 2, spaceBetween: 25 }, 992: { slidesPerView: 3, spaceBetween: 30 } }
            });
        }

        // 3. Blog Slider
        if (document.querySelector('.blog-swiper')) {
            new Swiper('.blog-swiper', {
                loop: false, speed: 600, spaceBetween: 24, slidesPerView: 1,
                autoplay: { delay: 6000, disableOnInteraction: false },
                pagination: { el: '.blog-pagination', clickable: true },
                navigation: { nextEl: '.blog-next', prevEl: '.blog-prev' },
                breakpoints: { 768: { slidesPerView: 2 }, 1200:{ slidesPerView: 3 } }
            });
        }

        // 4. Counter effect
        const counters = document.querySelectorAll('.counter');
        if (counters.length > 0) {
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const target = parseInt(el.dataset.target, 10);
                        const duration = 1500;
                        let start = 0;
                        const stepTime = Math.abs(Math.floor(duration / target)) || 1;
                        const timer = setInterval(() => {
                            start += 1;
                            el.innerText = start.toLocaleString();
                            if (start >= target) {
                                el.innerText = target.toLocaleString();
                                clearInterval(timer);
                            }
                        }, stepTime);
                        observer.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(c => observer.observe(c));
        }

        // 5. Countdown timer
        const countdownContainer = document.getElementById("countdown-timer");
        if (countdownContainer) {
            const daysEl = document.getElementById("days"), hoursEl = document.getElementById("hours");
            const minutesEl = document.getElementById("minutes"), secondsEl = document.getElementById("seconds");
            const saleEndTime = new Date("{{ date('Y-m-d', strtotime('+30 days')) }}T23:59:59").getTime();

            const updateCountdown = () => {
                const now = new Date().getTime();
                const distance = saleEndTime - now;
                if (distance <= 0) {
                    document.querySelector('.special-offer-section-wrapper')?.remove();
                    clearInterval(countdownInterval);
                    return;
                }
                daysEl.innerText = String(Math.floor(distance / (1000*60*60*24))).padStart(2,'0');
                hoursEl.innerText = String(Math.floor((distance % (1000*60*60*24))/(1000*60*60))).padStart(2,'0');
                minutesEl.innerText = String(Math.floor((distance % (1000*60*60))/(1000*60))).padStart(2,'0');
                secondsEl.innerText = String(Math.floor((distance % (1000*60))/1000)).padStart(2,'0');
            };
            const countdownInterval = setInterval(updateCountdown, 1000);
            updateCountdown();
        }
    });
</script>
@endpush