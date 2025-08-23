@props(['brands'])

@if(isset($brands) && $brands->count() > 0)
<section class="brand-carousel-section" data-aos="fade-up">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="section-title-decorated">Đối Tác</h3>
        </div>

        <div class="swiper brand-swiper-component">
            <div class="swiper-wrapper align-items-center">
                @foreach($brands as $brand)
                    <div class="swiper-slide text-center">
                        <a href="{{ $brand->website ?? '#' }}" target="_blank" title="{{ $brand->name }}" class="d-inline-block">
                            {{-- THAY ĐỔI DUY NHẤT Ở ĐÂY: Dùng logo_url từ Model --}}
                            <img src="{{ $brand->logo_url }}"
                                 alt="{{ $brand->name }}"
                                 class="img-fluid brand-logo">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Đảm bảo CSS và JS chỉ được thêm vào một lần duy nhất --}}
@once
    @push('styles')
    <style>
        .brand-carousel-section {
            padding-top: 4rem;
            padding-bottom: 4rem;
            background-color: var(--sand, #F6E9EC);
            border-top: 1px solid rgba(0,0,0, .06);
            border-bottom: 1px solid rgba(0,0,0, .06);
        }

        .section-title-decorated {
            font-weight: 700;
            text-transform: uppercase;
            color: var(--brand, #A20E38);
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
            margin-bottom: 0;
        }

        .section-title-decorated::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            height: 3px;
            width: 60px;
            background-color: var(--brand, #A20E38);
            border-radius: 2px;
        }

        .brand-logo {
            max-height: 80px;
            max-width: 150px;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.65;
            transition: all 0.3s cubic-bezier(.2, .9, .3, 1);
        }
        .swiper-slide a:hover .brand-logo {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.1);
        }
    </style>
    @endpush

    @push('scripts-page')
    <script>
        if (document.querySelector('.brand-swiper-component')) {
            const brandSwiper = new Swiper('.brand-swiper-component', {
                loop: true,
                spaceBetween: 30,
                speed: 1500,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                slidesPerView: 2,
                breakpoints: {
                    576: { slidesPerView: 3, spaceBetween: 30, },
                    768: { slidesPerView: 4, spaceBetween: 40, },
                    992: { slidesPerView: 6, spaceBetween: 50, }
                }
            });
        }
        </script>
    @endpush
@endonce