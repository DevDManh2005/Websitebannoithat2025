@props(['brands'])

<div class="swiper brand-carousel">
    <div class="swiper-wrapper align-items-center">
        @foreach($brands as $brand)
            <div class="swiper-slide text-center">
                <a href="#">
                    <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="img-fluid" style="max-height: 60px; filter: grayscale(100%); opacity: 0.7; transition: .3s;">
                </a>
            </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
    .brand-carousel .swiper-slide img:hover {
        filter: grayscale(0%);
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const brandSwiper = new Swiper('.brand-carousel', {
            loop: true,
            spaceBetween: 30,
            slidesPerView: 2,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                576: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                992: {
                    slidesPerView: 6,
                }
            }
        });
    });
</script>
@endpush
