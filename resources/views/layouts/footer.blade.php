<footer class="site-footer-light">
    <div class="container">
        {{-- Phần đối tác --}}
        <div class="footer-brands">
            <h6 class="footer-brands-title" data-aos="fade-up">Đối tác uy tín</h6>
            <div data-aos="fade-up" data-aos-delay="100">

                {{-- Dữ liệu $brandsForFooter được cung cấp từ AppServiceProvider --}}
                @if(isset($brandsForFooter) && $brandsForFooter->count() > 0)
                    <div class="swiper brand-swiper-component">
                        <div class="swiper-wrapper align-items-center">
                            @foreach($brandsForFooter as $brand)
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
                    <p class="small text-muted">Đang cập nhật danh sách đối tác của chúng tôi.</p>
                @endif

            </div>
        </div>

        <div class="footer-main">
            <div class="row gy-5">
                {{-- Cột 1: Thông tin --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-column">
                        <a href="{{ route('home') }}" class="d-inline-block mb-4">
                            @php $logo = $settings['logo_dark'] ?? null; @endphp
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $settings['site_name'] ?? 'EternaHome' }}" style="height:100px;" loading="lazy">
                            @else
                                <span class="fs-4 fw-bold text-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                            @endif
                        </a>
                        <p class="footer-about-text">
                            {{ $settings['footer_about'] ?? 'Nội thất tinh gọn, tối ưu không gian sống của bạn.' }}
                        </p>
                    </div>
                </div>

                {{-- Cột 2: Danh mục sản phẩm (Accordion) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-column">
                        <h6 class="footer-column-title">Sản phẩm</h6>
                        {{-- Dữ liệu $footerAccordionCategories được cung cấp từ AppServiceProvider --}}
                        @if(isset($footerAccordionCategories) && $footerAccordionCategories->isNotEmpty())
                            <div class="footer-accordion">
                                @foreach($footerAccordionCategories as $category)
                                    <div class="footer-accordion-item">
                                        <button class="footer-accordion-trigger collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#footerCat-{{ $category->id }}">
                                            <span>{{ $category->name }}</span>
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                        <div class="collapse footer-accordion-panel" id="footerCat-{{ $category->id }}">
                                            @if($category->children->isNotEmpty())
                                                <ul class="footer-links">
                                                    @foreach($category->children as $child)
                                                        <li><a href="{{ route('products.index', ['categories[]' => $child->id]) }}">{{ $child->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            <a href="{{ route('products.index', ['categories[]' => $category->id]) }}" class="footer-accordion-view-all">
                                                Xem thêm <i class="bi bi-arrow-right-short"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Cột 3: Hỗ trợ & Chính sách --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-column">
                        <h6 class="footer-column-title">Hỗ trợ & Chính sách</h6>
                        <ul class="footer-links">
                           <li><a href="{{ route('terms.show') }}">Điều khoản & Dịch vụ</a></li>
                           <li><a href="{{ route('warranty.show') }}">Chính sách Bảo hành</a></li>
                           <li><a href="{{ route('shipping_returns.show') }}">Giao hàng & Đổi trả</a></li>
                           <li><a href="{{ route('faq.show') }}">Câu hỏi thường gặp</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 4: Kết nối --}}
                <div class="col-12 col-md-6 col-lg-3">
                     <div class="footer-column">
                        <h6 class="footer-column-title">Kết nối với chúng tôi</h6>
                         <ul class="footer-contact-info">
                             <li><i class="bi bi-telephone"></i><a href="tel:{{ preg_replace('/\s+/', '', $settings['contact_phone'] ?? '') }}">{{ $settings['contact_phone'] ?? 'Số điện thoại' }}</a></li>
                             <li><i class="bi bi-envelope"></i><a href="mailto:{{ $settings['contact_email'] ?? '' }}">{{ $settings['contact_email'] ?? 'Email liên hệ' }}</a></li>
                         </ul>
                         <div class="d-flex align-items-center gap-2 mt-4">
                             @php $socials = ['facebook', 'instagram', 'tiktok']; @endphp
                             @foreach($socials as $key)
                                 @if(!empty($settings['social_' . $key]))
                                     <a href="{{ $settings['social_' . $key] }}" target="_blank" class="footer-social-link" aria-label="{{ ucfirst($key) }}"><i class="bi bi-{{ $key }}"></i></a>
                                 @endif
                             @endforeach
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="small footer-copyright">© {{ now()->year }} {{ $settings['site_name'] ?? 'EternaHome' }} - Dự Án Tốt Nghiệp - Cao đẳng FPT Polytechnic Đà Nẵng</div>
             <div class="footer-contact-info">
                   <i class="bi bi-geo-alt"></i><span>{{ $settings['contact_address'] ?? 'Địa chỉ' }}</span>
             </div>
        </div>
    </div>
</footer>

@once
@push('styles')
<style>
    /* CSS cho layout footer chung */
    .site-footer-light {
        background-color: var(--card, #FFFFFF);
        color: var(--muted, #7D726C);
        font-size: 0.9rem;
        border-top: 1px solid rgba(0,0,0, .07);
    }
    .footer-brands {
        padding: 2.5rem 0;
        border-bottom: 1px solid rgba(0,0,0, .07);
        text-align: center;
    }
    .footer-brands-title {
        margin-bottom: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--muted, #7D726C);
    }
    .footer-main { padding: 4rem 0; }
    .footer-column-title {
        font-weight: 600; font-size: 1.1rem;
        margin-bottom: 1.25rem; color: var(--text, #2B2623);
    }
    .footer-about-text { line-height: 1.6; }
    .footer-contact-info {
        list-style: none; padding: 0; margin: 0;
        display: flex; flex-wrap: wrap; align-items: center;
        gap: 0.5rem 1.5rem;
    }
    .footer-contact-info li { display: flex; align-items: center; gap: 0.5rem; }
    .footer-contact-info i { color: var(--brand); font-size: 1.1rem; }
    .footer-contact-info a { color: var(--muted); text-decoration: none; transition: color .2s; }
    .footer-contact-info a:hover { color: var(--brand); }
    .footer-accordion-item { border-bottom: 1px solid rgba(0,0,0,.07); }
    .footer-accordion-trigger {
        background: none; border: 0; width: 100%;
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.9rem 0; font-weight: 500; color: var(--text);
        text-align: left;
    }
    .footer-accordion-trigger:hover { color: var(--brand); }
    .footer-accordion-trigger i { transition: transform .3s ease; }
    .footer-accordion-trigger:not(.collapsed) i { transform: rotate(180deg); }
    .footer-accordion-panel { padding: 0 0 1rem 0; }
    .footer-accordion-panel .footer-links { gap: 0.5rem; }
    .footer-accordion-view-all {
        display: inline-block;
        margin-top: 0.75rem;
        font-size: 0.85rem; font-weight: 600;
        color: var(--brand); text-decoration: none;
    }
    .footer-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;}
    .footer-links a {
        color: var(--muted); text-decoration: none;
        padding: 0.2rem 0; display: inline-block;
        transition: color .2s;
    }
    .footer-links a:hover { color: var(--brand); }
    .footer-social-link {
        display: inline-flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; border-radius: 50%;
        background-color: #fff; border: 1px solid rgba(0,0,0,.08);
        color: var(--muted); text-decoration: none; transition: all .2s;
    }
    .footer-social-link:hover {
        background-color: var(--brand); color: #fff;
        border-color: var(--brand); transform: translateY(-3px);
    }
    .footer-bottom {
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
        gap: 1.5rem; padding: 1.5rem 0;
        border-top: 1px solid rgba(0,0,0,.07);
    }

    /* CSS CHO SLIDER ĐỐI TÁC */
    .brand-swiper-component {
        transition-timing-function: linear !important;
    }
    .brand-logo-footer {
        max-height: 50px;
        max-width: 120px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.65;
        transition: all 0.3s cubic-bezier(.2, .9, .3, 1);
    }
    .swiper-slide a:hover .brand-logo-footer {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
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