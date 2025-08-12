@extends('layouts.app')

@section('content')

{{-- Banner đầu trang --}}
<div class="search-banner mb-4 rounded overflow-hidden position-relative" style="height: 250px;">
    <img src="https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg"
        class="w-100 h-100 object-fit-cover" alt="Banner">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);">
        <div class="container h-100 d-flex flex-column justify-content-center align-items-start text-white px-4" data-aos="fade-down">
            <h2 class="fw-bold">Tìm kiếm sản phẩm</h2>
            <p class="mb-0">Hiển thị kết quả cho: <em>{{ $query }}</em></p>
        </div>
    </div>
</div>

{{-- Nội dung kết quả tìm kiếm --}}
<div class="container py-4">
    <h4 class="mb-4">Kết quả tìm kiếm cho: <em>{{ $query }}</em></h4>

    @if($products->count())
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="product-hover-wrapper h-100">
                        @include('frontend.components.product-card', ['product' => $product])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    @else
        <p>Không tìm thấy sản phẩm nào phù hợp.</p>
    @endif
</div>

{{-- CSS và AOS --}}
@push('styles')
    <style>
        .product-hover-wrapper {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-hover-wrapper:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .search-banner img {
            object-fit: cover;
        }
    </style>
@endpush

{{-- Thêm AOS --}}
@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
@endpush

@endsection
