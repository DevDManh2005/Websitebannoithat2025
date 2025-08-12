@props(['brands'])

@if(isset($brands) && $brands->count() > 0)
<section class="brand-section py-5 bg-light" data-aos="fade-up">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-uppercase" style="color: #E05763; position: relative; display: inline-block; padding-bottom: 10px;">
                Đối Tác
                <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); height: 3px; width: 60px; background-color:#E05763;"></span>
            </h3>
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
        .brand-section {
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }
        .brand-logo {
            max-height: 80px;
            max-width: 150px;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease-in-out;
        }
        .brand-logo:hover {
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