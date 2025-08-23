<footer class="site-footer">
    <div class="container">
        {{-- Phần đối tác --}}
        <div class="footer-brands">
            <h6 class="footer-brands-title" data-aos="fade-up">Đối tác uy tín của chúng tôi</h6>
            <div data-aos="fade-up" data-aos-delay="100">
                @php
                    $brands = \App\Models\Brand::active()->take(8)->get();
                @endphp

                @if(isset($brands) && $brands->count() > 0)
                    <div class="swiper brand-swiper-component">
                        <div class="swiper-wrapper align-items-center">
                            @foreach($brands as $brand)
                                <div class="swiper-slide text-center">
                                    <a href="{{ $brand->website ?? '#' }}" target="_blank" title="{{ $brand->name }}" class="d-inline-block">
                                        <img src="{{ $brand->logo_url }}"
                                             alt="{{ $brand->name }}"
                                             class="img-fluid brand-logo-footer">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="small text-muted">Đang cập nhật danh sách đối tác.</p>
                @endif
            </div>
        </div>

        <div class="footer-main">
            <div class="row gy-5">
                {{-- Cột 1: Thông tin --}}
                <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="footer-column">
                        <a href="{{ route('home') }}" class="d-inline-block mb-4">
                            @php $logo = $settings['logo_dark'] ?? null; @endphp
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $settings['site_name'] ?? 'EternaHome' }}" style="height: 50px;" loading="lazy">
                            @else
                                <span class="fs-4 fw-bold text-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                            @endif
                        </a>
                        <p class="footer-about-text">
                            {{ $settings['footer_about'] ?? 'Nội thất tinh gọn, tối ưu không gian sống của bạn.' }}
                        </p>
                        <div class="d-flex align-items-center gap-2 mt-4">
                             @php $socials = ['facebook', 'instagram', 'tiktok', 'youtube']; @endphp
                             @foreach($socials as $key)
                                 @if(!empty($settings['social_' . $key]))
                                     <a href="{{ $settings['social_' . $key] }}" target="_blank" class="footer-social-link" aria-label="{{ ucfirst($key) }}"><i class="bi bi-{{ $key }}"></i></a>
                                 @endif
                             @endforeach
                         </div>
                    </div>
                </div>

                {{-- Cột 2: Hỗ trợ & Chính sách --}}
                <div class="col-12 col-md-6 col-lg-2" data-aos="fade-up" data-aos-delay="200">
                    <div class="footer-column">
                        <h6 class="footer-column-title">Thông tin</h6>
                        <ul class="footer-links">
                           <li><a href="#">Về chúng tôi</a></li>
                           <li><a href="{{ route('faq.show') }}">Câu hỏi thường gặp</a></li>
                           <li><a href="#">Liên hệ</a></li>
                           <li><a href="#">Tuyển dụng</a></li>
                        </ul>
                    </div>
                </div>
                
                {{-- Cột 3: Hỗ trợ & Chính sách --}}
                <div class="col-12 col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="footer-column">
                        <h6 class="footer-column-title">Hỗ trợ khách hàng</h6>
                        <ul class="footer-links">
                           <li><a href="{{ route('warranty.show') }}">Chính sách bảo hành</a></li>
                           <li><a href="{{ route('shipping_returns.show') }}">Giao hàng & đổi trả</a></li>
                            <li><a href="{{ route('terms.show') }}">Điều khoản & dịch vụ</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 4: Kết nối --}}
                <div class="col-12 col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                     <div class="footer-column">
                        <h6 class="footer-column-title">Thông tin liên hệ</h6>
                         <ul class="footer-contact-info">
                             <li><i class="bi bi-geo-alt-fill"></i><span>{{ $settings['contact_address'] ?? 'Địa chỉ' }}</span></li>
                             <li><i class="bi bi-telephone-fill"></i><a href="tel:{{ preg_replace('/\s+/', '', $settings['contact_phone'] ?? '') }}">{{ $settings['contact_phone'] ?? 'Số điện thoại' }}</a></li>
                             <li><i class="bi bi-envelope-fill"></i><a href="mailto:{{ $settings['contact_email'] ?? '' }}">{{ $settings['contact_email'] ?? 'Email liên hệ' }}</a></li>
                         </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="small footer-copyright">Bản quyền © {{ now()->year }} thuộc về <strong>{{ $settings['site_name'] ?? 'EternaHome' }}</strong>.</div>
        </div>
    </div>
</footer>

@once
@push('styles')
<style>
    /* =================================== */
    /* == CSS ĐỒNG BỘ CHO FOOTER         == */
    /* =================================== */
    .site-footer {
        background-color: var(--bg); /* Nền màu be nhạt */
        color: var(--muted);
        font-size: 0.95rem;
        border-top: 1px solid var(--sand);
        padding-top: 4rem;
    }
    .footer-brands {
        padding-bottom: 3rem;
        text-align: center;
    }
    .footer-brands-title {
        margin-bottom: 2rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--muted);
    }
    .footer-main { 
        padding-bottom: 4rem; 
        border-top: 1px solid var(--sand);
        padding-top: 4rem;
    }
    .footer-column-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.25rem; 
        color: var(--text);
    }
    .footer-about-text { 
        line-height: 1.7; 
        color: var(--muted);
    }
    .footer-contact-info {
        list-style: none; padding: 0; margin: 0;
        display: flex; flex-direction: column; 
        gap: 1rem;
    }
    .footer-contact-info li { 
        display: flex; 
        align-items: flex-start; /* Căn icon và text theo top */
        gap: 0.75rem; 
    }
    .footer-contact-info i { 
        color: var(--brand); 
        font-size: 1rem;
        margin-top: 4px; /* Căn chỉnh icon cho đẹp hơn */
    }
    .footer-contact-info a,
    .footer-contact-info span { 
        color: var(--muted); 
        text-decoration: none; 
        transition: color .2s; 
    }
    .footer-contact-info a:hover { color: var(--brand); }

    .footer-links { 
        list-style: none; padding: 0; margin: 0; 
        display: flex; flex-direction: column; 
        gap: 0.75rem; /* Tăng khoảng cách giữa các link */
    }
    .footer-links a {
        color: var(--muted); 
        text-decoration: none;
        transition: color .2s, padding-left .2s;
    }
    .footer-links a:hover { 
        color: var(--brand); 
        padding-left: 5px; /* Hiệu ứng thụt vào khi hover */
    }

    .footer-social-link {
        display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; 
        border-radius: 50%;
        background-color: var(--card); 
        border: 1px solid var(--sand);
        color: var(--muted); 
        text-decoration: none; 
        transition: all .2s;
    }
    .footer-social-link:hover {
        background-color: var(--brand); 
        color: #fff;
        border-color: var(--brand); 
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }

    .footer-bottom {
        display: flex; flex-wrap: wrap; 
        justify-content: center; /* Căn giữa trên mobile */
        align-items: center;
        text-align: center;
        gap: 1rem; 
        padding: 1.5rem 0;
        border-top: 1px solid var(--sand);
    }
    @media (min-width: 768px) {
        .footer-bottom {
            justify-content: space-between; /* Căn 2 bên trên desktop */
            text-align: left;
        }
    }

    /* CSS CHO SLIDER ĐỐI TÁC */
    .brand-swiper-component {
        transition-timing-function: linear !important;
    }
    .brand-logo-footer {
        max-height: 45px; /* Giảm nhẹ logo */
        max-width: 120px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.6;
        transition: all 0.3s ease;
    }
    .swiper-slide a:hover .brand-logo-footer {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts-page')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (document.querySelector('.brand-swiper-component')) {
            const brandSwiper = new Swiper('.brand-swiper-component', {
                loop: true,
                spaceBetween: 30,
                speed: 4000,
                autoplay: {
                    delay: 1,
                    disableOnInteraction: false,
                },
                slidesPerView: 2,
                allowTouchMove: false,
                breakpoints: {
                    576: { slidesPerView: 3, spaceBetween: 30, },
                    768: { slidesPerView: 4, spaceBetween: 40, },
                    992: { slidesPerView: 6, spaceBetween: 50, }
                }
            });
        }
    });
</script>
@endpush
@endonce