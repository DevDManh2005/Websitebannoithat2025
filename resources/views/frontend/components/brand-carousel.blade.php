@props(['brands'])

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
@endif

@once
    @push('styles')
    <style>
        .brand-swiper-component {
            /* Thêm thuộc tính này để Swiper JS chạy mượt với autoplay tốc độ cao */
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
                    speed: 4000, // Tăng speed để chuyển động mượt hơn
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