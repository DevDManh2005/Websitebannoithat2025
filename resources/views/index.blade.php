@extends('layouts.app')

@section('title', 'Trang Chủ - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')

    {{-- 1. Phần Hero Banner Slider --}}
    <section class="hero-section">
        <div class="swiper hero-slider">
            <div class="swiper-wrapper">
                @forelse($slides as $slide)
                    <div class="swiper-slide hero-slide"
                         style="background-image: url('{{ asset('storage/' . $slide->image) }}')">
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
                <div class="col-6 col-lg-3 mb-4">
                    <i class="bi bi-tag" style="font-size: 5.5rem;"></i>
                    <h6 class="mt-3 fw-bold">Giá Trị Tuyệt Vời Mỗi Ngày</h6>
                    <p class="text-muted small">Giá cả phù hợp với ngân sách của bạn</p>
                </div>
                <div class="col-6 col-lg-3 mb-4">
                    <i class="bi bi-truck" style="font-size: 5.5rem;"></i>
                    <h6 class="mt-3 fw-bold">Miễn Phí Vận Chuyển</h6>
                    <p class="text-muted small">Giao hàng phổ biến trong 1 - 2 ngày</p>
                </div>
                <div class="col-6 col-lg-3 mb-4">
                    <i class="bi bi-award" style="font-size: 5.5rem;"></i>
                    <h6 class="mt-3 fw-bold">Dịch Vụ Khách Hàng Chuyên Nghiệp</h6>
                    <p class="text-muted small">Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ 24/7</p>
                </div>
                <div class="col-6 col-lg-3 mb-4">
                    <i class="bi bi-hand-thumbs-up" style="font-size: 5.5rem;"></i>
                    <h6 class="mt-3 fw-bold">Lựa Chọn Không Thể Đánh Bại</h6>
                    <p class="text-muted small">Mọi thứ trong nhà đều ở cùng một nơi</p>
                </div>
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
                        @foreach ([['1600', 'Sản phẩm hoàn thiện'], ['180', 'Mẫu mã đa dạng'], ['38', 'Đối tác uy tín toàn quốc']] as [$num, $label])
                            <div class="col-4">
                                <div class="p-3 rounded shadow-sm bg-light hover-shadow">
                                    <h4 class="text-danger fw-bold counter" data-target="{{ $num }}">0</h4>
                                    <p class="small text-muted mb-0">{{ $label }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0 d-flex justify-content-center">
                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716482/img_mobile_about_xpylyh.jpg"
                         alt="Nội thất" class="about-img img-fluid" data-aos="flip-left" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Phần hiển thị đối tác --}}
    @include('frontend.components.brand-carousel', ['brands' => \App\Models\Brand::active()->take(6)->get()])
<br>
    {{-- 5. Phần hiển thị danh mục sản phẩm --}}
    <section class="home-category">
        <div class="container">
            <div class="row row-margin">
                <div class="col-lg-3 col-md-3 col-12 col-padding d-md-block d-none" data-aos="fade-right">
                    <div class="category-item category-item-large">
                        <a class="category-thumb" href="/san-pham" title="Sản phẩm mới">
                            <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716780/img_banner_1_pnlot5.png"
                                 alt="Sản phẩm mới" loading="lazy">
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
                            <div class="category-item">
                                <a class="category-thumb" href="/danh-muc/van-phong" title="Văn phòng">
                                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716814/img_banner_2_b3l33z.png"
                                         alt="Văn phòng" loading="lazy">
                                    <div class="category-caption">
                                        <h3>Văn phòng</h3>
                                        <p>Ghế, bàn làm việc, tủ sách, đèn….</p>
                                        <span>Xem tất cả →</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7 col-7 col-padding" data-aos="fade-up">
                            <div class="category-item">
                                <a class="category-thumb" href="/danh-muc/phong-khach" title="Phòng khách">
                                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716849/img_banner_3_x2xjjr.png"
                                         alt="Phòng khách" loading="lazy">
                                    <div class="category-caption">
                                        <h3>Phòng Khách</h3>
                                        <p>Bàn, sofa, ghế đôn, đèn, tủ tivi, bàn bên…</p>
                                        <span>Xem tất cả →</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row row-margin">
                        <div class="col-lg-7 col-md-7 col-7 col-padding" data-aos="fade-up">
                            <div class="category-item">
                                <a class="category-thumb" href="/danh-muc/phong-ngu" title="Phòng ngủ">
                                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716879/img_banner_4_oz4dkk.png"
                                         alt="Phòng ngủ" loading="lazy">
                                    <div class="category-caption">
                                        <h3>Phòng ngủ</h3>
                                        <p>Giường, tủ quần áo, gương, tủ đầu giường…</p>
                                        <span>Xem tất cả →</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-5 col-padding" data-aos="fade-up">
                            <div class="category-item">
                                <a class="category-thumb" href="/danh-muc/phong-bep" title="Phòng bếp">
                                    <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716907/img_banner_5_czta5t.png"
                                         alt="Phòng bếp" loading="lazy">
                                    <div class="category-caption">
                                        <h3>Phòng bếp</h3>
                                        <p>Bàn ăn, ghế, kệ…</p>
                                        <span>Xem tất cả →</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

{{-- 6. Phần ưu đãi đặc biệt với đồng hồ đếm ngược --}}
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
                        <div class="time-block" id="days-block">
                            <span class="time-value fw-bold" id="days">00</span>
                            <span class="time-label">Ngày</span>
                        </div>
                        <div class="time-block">
                            <span class="time-value fw-bold" id="hours">00</span>
                        </div>
                        <span class="separator">:</span>
                        <div class="time-block">
                            <span class="time-value fw-bold" id="minutes">00</span>
                        </div>
                        <span class="separator">:</span>
                        <div class="time-block">
                            <span class="time-value fw-bold" id="seconds">00</span>
                        </div>
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

    {{-- 7. Phần mã giảm giá (voucher) --}}
    <section class="voucher-section py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0 text-danger" data-aos="fade-right">🎁 Mã Giảm Giá Dành Riêng Cho Bạn</h2>
            </div>
            @if($vouchers->isNotEmpty())
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach($vouchers as $voucher)
                        <div class="col" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                            @include('frontend.components.voucher-card', ['voucher' => $voucher])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-ticket-perforated fs-1"></i>
                    <h5 class="mt-3">Hiện chưa có mã giảm giá nào.</h5>
                </div>
            @endif
        </div>
    </section>

    {{-- 8. Phần sản phẩm bán chạy theo danh mục --}}
    <section class="py-5 bg-white best-seller-section" data-aos="fade-up">
        <div class="container">
            <h2 class="text-center fw-bold mb-4 text-primary-custom">🔥 Sản phẩm bán chạy theo danh mục</h2>
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
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-{{ $category->id }}"
                         role="tabpanel">
                        <div class="row">
                            @forelse($category->products as $product)
                                <div class="col-6 col-md-4 col-lg-3 mb-4">
                                    @include('frontend.components.product-card', ['product' => $product])
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-4">
                                    Không có sản phẩm nào trong danh mục này.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 9. Phần quy trình làm việc --}}
    <section class="py-5 bg-white" data-aos="fade-up">
        <div class="container text-center">
            <h4 class="text-danger fw-bold mb-2" data-aos="fade-up" data-aos-delay="100">Quy trình làm việc</h4>
            <h2 class="fw-semibold mb-5" data-aos="fade-up" data-aos-delay="200">Cam kết chất lượng từ <span
                    class="text-danger">Eterna Home</span></h2>
            <div class="row justify-content-center">
                @php
                    $steps = [
                        [ 'icon' => 'bi-house-door', 'title' => 'Tư vấn và chọn sản phẩm', 'desc' => 'Tư vấn chi tiết mẫu, giúp lựa chọn sản phẩm nội thất phù hợp với nhu cầu và không gian.' ],
                        [ 'icon' => 'bi-file-earmark-check', 'title' => 'Đặt hàng và xác nhận', 'desc' => 'Xác nhận đơn hàng, kiểm tra thông tin và gửi báo giá chi tiết.' ],
                        [ 'icon' => 'bi-truck', 'title' => 'Sản xuất và giao hàng', 'desc' => 'Sản xuất sản phẩm theo đơn hàng và giao tận nơi cho khách hàng.' ],
                        [ 'icon' => 'bi-clipboard-check', 'title' => 'Kiểm tra và hỗ trợ', 'desc' => 'Kiểm tra chất lượng sản phẩm khi giao, hỗ trợ lắp đặt và bảo hành.' ],
                    ];
                @endphp
                @foreach ($steps as $index => $step)
                    <div class="col-6 col-md-3 mb-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="border rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 96px; height: 96px; border: 2px solid #A20E38;">
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
                    <p class="section-desc">
                        Eterna Home cam kết chất lượng, thẩm mỹ và sự hài lòng. Với đội ngũ tư vấn giàu kinh nghiệm,
                        sản phẩm thiết kế đạt cao cùng với hỗ trợ tận tâm, chúng tôi mang đến giải pháp nội thất hoàn hảo
                        cho không gian của bạn.
                    </p>
                    <div class="feature-box" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon-circle text-primary-custom">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div>
                            <h6 class="feature-title">Chất lượng và thẩm mỹ vượt trội</h6>
                            <p class="feature-text">
                                Thiết kế tinh tế và sang trọng, đảm bảo sự hài hòa và đẳng cấp với chất liệu chọn lọc, bền
                                đẹp và phù hợp với mọi không gian.
                            </p>
                        </div>
                    </div>
                    <div class="feature-box" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-circle text-primary-custom">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div>
                            <h6 class="feature-title">Dịch vụ chuyên nghiệp, tận tâm</h6>
                            <p class="feature-text">
                                Từ tư vấn đến lắp đặt, Eterna luôn phục vụ khách hàng với sự tận tâm, đảm bảo trải nghiệm
                                mua sắm hoàn hảo.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="zoom-in-left" data-aos-delay="300">
                    <div class="image-wrapper position-relative">
                        <img src="https://res.cloudinary.com/dfoxknyho/image/upload/v1754716983/img_banner_whychoose_ebpgk2.jpg"
                             alt="Eterna Home" loading="lazy" class="img-fluid rounded shadow"/>
                        <div class="image-border"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 11. Phần câu hỏi thường gặp (FAQ) --}}
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6" data-aos="fade-right">
                    <h5 class="text-danger fw-bold">Faq's</h5>
                    <h3 class="fw-bold mb-4">Câu hỏi thường gặp?</h3>
                    <p>
                        Tại <strong>Eterna Home</strong>, chúng tôi chuyên cung cấp các sản phẩm nội thất cao cấp, giúp
                        khách hàng tạo nên không gian sống và làm việc lý tưởng. Với đội ngũ tư vấn giàu kinh nghiệm, chúng
                        tôi cam kết mang đến sản phẩm chất lượng cao và dịch vụ hỗ trợ tận tâm.
                    </p>
                    <p>
                        <strong>Eterna Home</strong> cung cấp đa dạng sản phẩm nội thất, từ hiện đại, tối giản đến tân cổ
                        điển, phù hợp với nhiều phong cách và nhu cầu của khách hàng.
                    </p>
                    <p>
                        Nếu bạn có bất kỳ thắc mắc nào về sản phẩm, quy trình mua sắm, giá cả, hoặc chính sách bảo hành, hãy
                        xem ngay phần Câu hỏi thường gặp để biết thêm chi tiết hoặc liên hệ với chúng tôi để được tư vấn
                        trực tiếp! 🚀
                    </p>
                    <a href="#" class="btn btn-danger mt-3 fw-bold">GỬI CÂU HỎI ?</a>
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1-heading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
                                    <i class="fas fa-question-circle text-danger me-2"></i>
                                    Eterna Home cung cấp những sản phẩm nội thất nào?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" aria-labelledby="faq1-heading"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <strong>Eterna Home</strong> cung cấp các sản phẩm nội thất cao cấp như bàn ghế, giường,
                                    tủ, và kệ cho nhà ở, văn phòng.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2-heading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                    <i class="fas fa-question-circle text-danger me-2"></i>
                                    Eterna Home có hỗ trợ tư vấn chọn sản phẩm không?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faq2-heading"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Có, <strong>Eterna Home</strong> hỗ trợ tư vấn chọn sản phẩm tận tình.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3-heading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                    <i class="fas fa-question-circle text-danger me-2"></i>
                                    Thời gian giao hàng mất bao lâu?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faq3-heading"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Thời gian giao hàng từ 7 - 20 ngày, tùy đơn hàng và địa điểm.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4-heading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                    <i class="fas fa-question-circle text-danger me-2"></i>
                                    Chính sách bảo hành sản phẩm như thế nào?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faq4-heading"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Bảo hành sản phẩm từ 12 – 24 tháng, hỗ trợ bảo trì dài hạn.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 {{-- ====================================================================== --}}
{{-- ============ SECTION 12: ĐÁNH GIÁ TỪ KHÁCH HÀNG ============ --}}
{{-- ====================================================================== --}}

<section class="py-5 bg-light" data-aos="fade-up">
    <div class="container">
        <h2 class="text-center fw-bold mb-4 text-primary-custom">Khách hàng nói gì về Eterna Home</h2>
        
        {{-- Cấu trúc Swiper Slider --}}
        <div class="swiper review-swiper">
            <div class="swiper-wrapper">
                
                {{-- LƯU Ý: Mỗi card đánh giá phải nằm trong một <div class="swiper-slide"> riêng biệt để responsive --}}
                
                <div class="swiper-slide">
                    @include('frontend.components.review-card', [
                        'name' => 'Phan Thu Hoài',
                        'content' => 'Chiếc sofa màu be thực sự là điểm nhấn cho phòng khách nhà mình. Chất vải mềm mịn, form dáng hiện đại. Rất ưng ý!',
                        'avatar' => 'https://i.pravatar.cc/100?img=1'
                    ])
                </div>

                <div class="swiper-slide">
                    @include('frontend.components.review-card', [
                        'name' => 'Trần Minh Quang',
                        'content' => 'Lúc đầu cũng hơi ngại mua bàn ăn giá trị cao online, nhưng các bạn nhân viên tư vấn rất kiên nhẫn, gửi ảnh thật chi tiết. Nhận hàng còn đẹp hơn mong đợi.',
                        'avatar' => 'https://i.pravatar.cc/100?img=2'
                    ])
                </div>

                <div class="swiper-slide">
                    @include('frontend.components.review-card', [
                        'name' => 'Lê Thị Hồng Nhung',
                        'content' => 'Giường ngủ chắc chắn, nằm rất êm. Từ ngày có giường mới cả nhà mình ngủ ngon hơn hẳn. Giao hàng và lắp đặt tận nơi nên mình không phải lo gì cả.',
                        'avatar' => 'https://i.pravatar.cc/100?img=3'
                    ])
                </div>

                <div class="swiper-slide">
                    @include('frontend.components.review-card', [
                        'name' => 'Đặng Quốc Tuấn',
                        'content' => 'Mọi thứ rất chuyên nghiệp, từ khâu xác nhận đơn hàng đến việc giao và lắp đặt. Các bạn đến đúng hẹn, làm việc nhanh gọn. Rất hài lòng với dịch vụ của Eterna.',
                        'avatar' => 'https://i.pravatar.cc/100?img=4'
                    ])
                </div>

                <div class="swiper-slide">
                    @include('frontend.components.review-card', [
                        'name' => 'Hoàng Mai Anh',
                        'content' => 'Tìm mãi mới được chiếc kệ tivi phong cách tối giản hợp ý. Lắp lên phòng khách trông gọn gàng và sang trọng hơn hẳn. Chất gỗ sờ rất thích tay.',
                        'avatar' => 'https://i.pravatar.cc/100?img=5'
                    ])
                </div>

                <div class="swiper-slide">
                     @include('frontend.components.review-card', [
                        'name' => 'Vũ Tiến Dũng',
                        'content' => 'Đã mua hàng ở đây 2 lần. Lần nào cũng hài lòng tuyệt đối. Sản phẩm dùng bền, sau một năm vẫn như mới. Sẽ tiếp tục ủng hộ shop.',
                        'avatar' => 'https://i.pravatar.cc/100?img=6'
                    ])
                </div>

            </div>
            
            {{-- Dấu chấm phân trang --}}
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
                        @php
                            $thumb = $post->thumbnail
                                ? asset('storage/'.$post->thumbnail)
                                : 'https://picsum.photos/seed/blog'.$post->id.'/640/400';
                            $url   = route('blog.show', $post->slug ?? $post->id);
                            $date  = optional($post->published_at ?? $post->created_at)->format('d/m/Y');
                            $excerpt = \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->content ?? ''), 110);
                        @endphp

                        <div class="swiper-slide">
                            <article class="blog-card h-100">
                                <a href="{{ $url }}" class="blog-thumb d-block">
                                    <img src="{{ $thumb }}" alt="{{ $post->title }}" loading="lazy">
                                </a>
                                <div class="p-3">
                                    <div class="blog-meta small text-muted mb-1">
                                        <i class="bi bi-calendar-check me-1"></i> {{ $date ?? '' }}
                                    </div>
                                    <h5 class="fw-bold blog-title">
                                        <a href="{{ $url }}" class="text-dark text-decoration-none">
                                            {{ $post->title }}
                                        </a>
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
    :root { --main-color: #A20E38; }
    /* Hero Section */
    .hero-section { position: relative; height: 600px; }
    .hero-slide { height: 600px; background-size: cover; background-position: center; position: relative; }
    .hero-overlay { background: rgba(0, 0, 0, 0.4); width: 100%; height: 100%; }
    .hero-title { font-size: clamp(2rem, 5vw, 3.5rem); line-height: 1.2; }
    .swiper-button-next, .swiper-button-prev {
        width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.9); border-radius: 50%;
        color: #ff6f61; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: background-color 0.2s ease;
    }
    .swiper-button-next:hover, .swiper-button-prev:hover { background-color: #fff; }
    .swiper-button-next:after, .swiper-button-prev:after { font-size: 1rem; font-weight: bold; }

    /* Features Section */
    .features-section i { font-size: 4.5rem; color: var(--main-color); }
    .hover-shadow { transition: all 0.3s ease; cursor: pointer; }
    .hover-shadow:hover {
        background-color: #ffe6e6; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px); border: 1px solid #ffcccc;
    }

    /* About Section */
    .about-img { width: 100%; max-height: 500px; object-fit: cover; border-radius: 1rem; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; }
    .about-img:hover { transform: scale(1.03); box-shadow: 0 0 25px rgba(0, 0, 0, 0.25); }
    .about-section .text-muted { font-size: clamp(1rem, 2.5vw, 1.1rem); }

    /* Category Section */
    .home-category .category-item { position: relative; overflow: hidden; border-radius: 12px; transition: transform 0.3s ease, box-shadow 0.3s ease; margin: 8px; }
    .home-category .category-item:hover { transform: translateY(-6px); box-shadow: 0 10px 24px rgba(0, 0, 0, 0.15); }
    .category-thumb img { width: 100%; height: auto; object-fit: cover; border-radius: 12px; }
    .category-caption {
        position: absolute; top: 0; left: 0; padding: 1rem; width: 100%; height: 100%;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.6)); color: #fff;
        display: flex; flex-direction: column; justify-content: flex-end; border-radius: 12px;
        transition: background 0.3s;
    }
    .category-caption h3 { font-size: clamp(1rem, 2.5vw, 1.2rem); font-weight: bold; margin-bottom: 0.25rem; text-transform: capitalize; }
    .category-caption p { margin-bottom: 0.5rem; font-size: clamp(0.8rem, 2vw, 0.9rem); }
    .category-caption span { font-weight: 600; font-size: clamp(0.75rem, 2vw, 0.85rem); color: #ff7205; transition: text-decoration 0.3s; }
    .category-caption span:hover { text-decoration: underline; }

    /* Special Offer Section */
    .special-offer-section-wrapper {
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://res.cloudinary.com/dfoxknyho/image/upload/v1754716939/pngtree-empty-wooden-table-top-on-a-blurred-background-of-a-modern-image_16376700_iwce5y.jpg');
        background-size: cover; background-position: center; background-attachment: fixed; padding: 3rem 0;
    }
    .special-offer-content {
        background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 16px;
        padding: 1.5rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border-top: 3px solid #ff9900;
    }
    .special-offer-section-wrapper .offer-header {
        background-color: #fffaf0; border: 1px solid #eee; border-radius: 12px; padding: 8px;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;
    }
    .special-offer-section-wrapper .offer-timer-wrapper {
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
    }
    .special-offer-section-wrapper .flash-label {
        background: linear-gradient(45deg, #ffc107, #ff9900); color: #000; padding: 8px 16px;
        border-radius: 50px; display: flex; align-items: center; gap: 0.5rem; font-weight: 700; flex-shrink: 0;
        animation: pulse-orange 2s infinite;
    }
    .special-offer-section-wrapper .flash-label i { font-size: 1.2rem; }
    .special-offer-section-wrapper .flash-label div { display: flex; flex-direction: column; line-height: 1.1; }
    .special-offer-section-wrapper .flash-label small { font-size: 0.65rem; font-weight: 500; opacity: 0.8; }
    .special-offer-section-wrapper .countdown { display: flex; align-items: center; gap: 0.5rem; }
    .special-offer-section-wrapper .time-block {
        background: linear-gradient(145deg, #e53935, #b71c1c); color: white; padding: 6px 12px;
        border-radius: 6px; text-align: center; min-width: 48px; line-height: 1;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
    }
    .special-offer-section-wrapper .time-block .time-value {
        font-size: clamp(1.2rem, 3vw, 1.5rem); font-weight: 900; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }
    .special-offer-section-wrapper .time-block .time-label {
        font-size: clamp(0.5rem, 1.5vw, 0.6rem); display: block; text-transform: uppercase; opacity: 0.8;
    }
    .special-offer-section-wrapper .separator { font-size: clamp(1.2rem, 3vw, 1.5rem); color: #333; font-weight: 700; }
    .special-offer-section-wrapper .offer-title {
        font-weight: 800; font-size: clamp(1.2rem, 3vw, 1.5rem); margin: 0; padding-right: 1rem;
        background: linear-gradient(45deg, #b71c1c, #e53935); -webkit-background-clip: text;
        background-clip: text; text-fill-color: transparent;
    }

    /* Best Seller Section */
    .best-seller-section .nav-pills .nav-link {
        color: #555; border: 1px solid var(--main-color); background-color: transparent; margin: 0 4px;
        border-radius: 50px; transition: all 0.3s ease; font-size: clamp(0.9rem, 2vw, 1rem);
    }
    .best-seller-section .nav-pills .nav-link:hover { background-color: #f8e8ec; color: var(--main-color); }
    .best-seller-section .nav-pills .nav-link.active { background-color: var(--main-color); color: #fff; }

    /* Review Section */
    .review-swiper .swiper-slide { display: flex; flex-direction: column; }
    .review-card { background-color: #fffaf5; border-radius: 10px; height: 100%; }

    /* Blog Section */
    .blog-card { border: 1px solid #eee; border-radius: 14px; overflow: hidden; background: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s; }
    .blog-card:hover { transform: translateY(-4px); box-shadow: 0 10px 26px rgba(0,0,0,.10); }
    .blog-thumb { aspect-ratio: 16/10; overflow: hidden; }
    .blog-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
    .blog-card:hover .blog-thumb img { transform: scale(1.05); }
    .blog-title { line-height: 1.25; font-size: clamp(1rem, 2.5vw, 1.1rem); }
    .blog-section .swiper { overflow: visible; }
    .blog-section .swiper-button-prev, .blog-section .swiper-button-next {
        width: 40px; height: 40px; background: #fff; border-radius: 50%;
        box-shadow: 0 4px 14px rgba(0,0,0,.12); z-index: 5;
    }
    .blog-section .swiper-button-prev:after, .blog-section .swiper-button-next:after { font-size: 0.9rem; color: #333; }
    .blog-section .swiper-pagination-bullet { opacity: 0.5; }
    .blog-section .swiper-pagination-bullet-active { background: var(--main-color); opacity: 1; }

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        /* General */
        body { font-size: 15px; }
        h1, .h1 { font-size: clamp(1.8rem, 4vw, 2rem); }
        h2, .h2 { font-size: clamp(1.5rem, 3.5vw, 1.75rem); }
        h3, .h3 { font-size: clamp(1.2rem, 3vw, 1.4rem); }
        h4, .h4 { font-size: clamp(1rem, 2.5vw, 1.2rem); }
        .py-5 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }

        /* Hero Section */
        .hero-section, .hero-slide { height: 500px; }
        .hero-title { font-size: clamp(1.8rem, 4vw, 2.5rem); }
        .hero-slide-content .btn { font-size: 0.9rem; padding: 0.5rem 1rem; }

        /* Features Section */
        .features-section i { font-size: 3.5rem; }
        .features-section .col-6 { margin-bottom: 1.5rem; }

        /* About Section */
        .about-section .col-4 { flex: 0 0 33.333%; max-width: 33.333%; margin-bottom: 1rem; }
        .about-img { max-height: 400px; }

        /* Category Section */
        .home-category .row-margin { margin-left: -8px; margin-right: -8px; }
        .home-category .col-padding { padding-left: 8px; padding-right: 8px; }
        .home-category .category-item { margin: 8px 0; }
        .category-caption h3 { font-size: 1rem; }
        .category-caption p { font-size: 0.85rem; }

        /* Special Offer Section */
        .special-offer-section-wrapper { padding: 2rem 0; }
        .special-offer-content { padding: 1rem; }
        .special-offer-section-wrapper .offer-header { flex-direction: column; align-items: stretch; text-align: center; }
        .special-offer-section-wrapper .offer-timer-wrapper { flex-direction: column; gap: 0.75rem; }
        .special-offer-section-wrapper .offer-title { margin: 0.5rem 0 0; padding: 0; }

        /* Best Seller Section */
        .best-seller-section .nav-pills { flex-wrap: nowrap; overflow-x: auto; justify-content: flex-start; padding-bottom: 8px; scrollbar-width: none; }
        .best-seller-section .nav-pills::-webkit-scrollbar { display: none; }
        .best-seller-section .nav-pills .nav-link { font-size: 0.9rem; padding: 0.5rem 1rem; }

        /* FAQ Section */
        .g-3 { --bs-gutter-x: 1rem; }
    }

    @media (max-width: 767.98px) {
        /* General */
        .py-5 { padding-top: 2rem !important; padding-bottom: 2rem !important; }
        h1, .h1 { font-size: clamp(1.5rem, 4vw, 1.8rem); }
        h2, .h2 { font-size: clamp(1.3rem, 3.5vw, 1.5rem); }
        h3, .h3 { font-size: clamp(1rem, 3vw, 1.2rem); }
        h4, .h4 { font-size: clamp(0.9rem, 2.5vw, 1rem); }

        /* Hero Section */
        .hero-section, .hero-slide { height: 50vh; min-height: 360px; }
        .hero-title { font-size: clamp(1.5rem, 6vw, 2rem); }
        .hero-slide-content .small { font-size: 0.8rem; }
        .hero-slide-content .btn { font-size: 0.8rem; padding: 0.4rem 0.8rem; }
        .swiper-button-next, .swiper-button-prev { display: none; }

        /* Features Section */
        .features-section i { font-size: 3rem; }
        .features-section .small { font-size: 0.75rem; }

        /* About Section */
        .about-section .col-4 { flex: 0 0 100%; max-width: 100%; }
        .about-section .text-muted { font-size: 0.9rem; }
        .about-img { max-height: 300px; }

        /* Category Section */
        .home-category .col-lg-9 .col-6 { flex: 0 0 50%; max-width: 50%; }
        .category-caption p { display: none; }
        .category-caption h3 { font-size: 0.9rem; }
        .category-caption span { font-size: 0.75rem; }

        /* Special Offer Section */
        .special-offer-section-wrapper .flash-label { width: 100%; justify-content: center; padding: 6px 12px; }
        .special-offer-section-wrapper .time-block { min-width: 40px; padding: 5px 8px; }
        .special-offer-section-wrapper .time-block .time-value { font-size: 1.2rem; }
        .special-offer-section-wrapper .separator { font-size: 1.2rem; }

        /* Voucher Section */
        .voucher-section .row-cols-1 { --bs-gutter-x: 1rem; }
        .voucher-section .row-cols-1 > .col { margin-bottom: 1rem; }

        /* Best Seller Section */
        .best-seller-section .row-cols-2 { --bs-gutter-x: 1rem; }
        .best-seller-section .nav-pills .nav-link { font-size: 0.8rem; padding: 0.4rem 0.8rem; }

        /* Process Section */
        .row.justify-content-center .col-6.col-md-3 { flex: 0 0 50%; max-width: 50%; }
        .step-icon { width: 80px; height: 80px; }
        .step-icon i { font-size: 1.5rem; }

        /* FAQ Section */
        .g-3 { --bs-gutter-x: 0.75rem; }
        .accordion-button { font-size: 0.9rem; }
        .accordion-body { font-size: 0.85rem; }

        /* Review Section */
        .review-card { padding: 1rem; }
        .review-card .fs-5 { font-size: 1rem !important; }

        /* Blog Section */
        .blog-title { font-size: 0.9rem; }
        .blog-meta, .blog-card p { font-size: 0.75rem; }
        .blog-card .btn { font-size: 0.8rem; }
    }

    @media (max-width: 575.98px) {
        /* General */
        body { font-size: 14px; }
        h1, .h1 { font-size: clamp(1.3rem, 4vw, 1.5rem); }
        h2, .h2 { font-size: clamp(1.2rem, 3.5vw, 1.3rem); }
        h3, .h3 { font-size: clamp(0.9rem, 3vw, 1rem); }
        h4, .h4 { font-size: clamp(0.8rem, 2.5vw, 0.9rem); }
        .py-5 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }

        /* Hero Section */
        .hero-section, .hero-slide { height: 45vh; min-height: 320px; }
        .hero-title { font-size: clamp(1.2rem, 5vw, 1.5rem); }
        .hero-slide-content .small { font-size: 0.7rem; }
        .hero-slide-content .btn { font-size: 0.75rem; padding: 0.3rem 0.6rem; }

        /* Features Section */
        .features-section i { font-size: 2.5rem; }
        .features-section .small { font-size: 0.7rem; }

        /* About Section */
        .about-img { max-height: 250px; }

        /* Category Section */
        .home-category .row-margin { margin-left: -5px; margin-right: -5px; }
        .home-category .col-padding { padding-left: 5px; padding-right: 5px; }
        .category-caption h3 { font-size: 0.8rem; }
        .category-caption span { font-size: 0.7rem; }

        /* Special Offer Section */
        .special-offer-section-wrapper .time-block { min-width: 36px; padding: 4px 6px; }
        .special-offer-section-wrapper .time-block .time-value { font-size: 1rem; }
        .special-offer-section-wrapper .time-block .time-label { font-size: 0.5rem; }
        .special-offer-section-wrapper .separator { font-size: 1rem; }

        /* Voucher Section */
        .voucher-section .row-cols-1 { --bs-gutter-x: 0.5rem; }

        /* Process Section */
        .step-icon { width: 60px; height: 60px; }
        .step-icon i { font-size: 1.2rem; }

        /* Review Section */
        .review-card { padding: 0.75rem; }
        .review-card .fs-5 { font-size: 0.9rem !important; }

        /* Blog Section */
        .blog-card .p-3 { padding: 1rem !important; }
        .blog-title { font-size: 0.8rem; }
        .blog-meta, .blog-card p { font-size: 0.7rem; }
        .blog-card .btn { font-size: 0.75rem; }
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

// 4. Đồng hồ đếm ngược (thời gian thực - ĐÃ SỬA LỖI)
const countdownContainer = document.getElementById("countdown-timer");
if (countdownContainer) {
    const daysBlock = document.getElementById("days-block");
    const daysEl = document.getElementById("days");
    const hoursEl = document.getElementById("hours");
    const minutesEl = document.getElementById("minutes");
    const secondsEl = document.getElementById("seconds");

    // --- SỬA LỖI Ở ĐÂY ---
    // Thay vì tạo ngày mới mỗi lần tải trang, chúng ta đặt một mốc thời gian CỐ ĐỊNH.
    // Ví dụ: Đếm ngược đến 23:59:59 ngày 21 tháng 09 năm 2025 (30 ngày kể từ hôm nay).
    // BẠN CÓ THỂ THAY ĐỔI MỐC THỜI GIAN NÀY BẤT CỨ LÚC NÀO.
    const saleEndTime = new Date("2025-09-21T23:59:59").getTime();

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
// 5) Swiper cho Blog (đặt ngoài mọi block khác)
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