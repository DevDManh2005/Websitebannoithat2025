@extends('layouts.app')

@section('content')

{{-- Banner đầu trang --}}
<div class="profile-banner d-flex align-items-center justify-content-center text-white mb-5">
    <div class="container text-center" data-aos="fade-in">
        <h1 class="display-4">Kết Quả Tìm Kiếm</h1>
        <p class="lead text-white-50 mb-4">Hiển thị kết quả cho: <em>{{ $query }}</em></p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
            </ol>
        </nav>
    </div>
</div>


{{-- Nội dung kết quả tìm kiếm --}}
<div class="container py-4">
    <div class="card card-glass rounded-4 p-4 mb-4" data-aos="fade-up">
        <h4 class="mb-0 fw-bold text-brand">Kết quả tìm kiếm cho: <em>"{{ $query }}"</em></h4>
    </div>

    @if($products->count())
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
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
        <div class="card-glass text-center p-5 rounded-4" data-aos="fade-up">
            <div class="card-body">
                <i class="bi bi-search-heart" style="font-size: 3.5rem; color: var(--muted);"></i>
                <h5 class="mt-3">Không tìm thấy sản phẩm nào phù hợp</h5>
                <p class="text-secondary">Vui lòng thử lại với từ khóa khác.</p>
                <a href="{{ route('home') }}" class="btn btn-brand rounded-pill mt-3">Quay lại trang chủ</a>
            </div>
        </div>
    @endif
</div>

{{-- CSS và AOS --}}
@push('styles')
    <style>
        .profile-banner {
            height: 250px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg');
            background-size: cover;
            background-position: center;
        }
        .profile-banner .breadcrumb-item a { color: var(--sand); }
        .profile-banner .breadcrumb-item.active { color: var(--muted); }
        
        .product-hover-wrapper {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-hover-wrapper:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .text-brand {
            color: var(--brand);
        }
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, .82), rgba(255, 255, 255, .95));
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(32, 25, 21, .08);
            border: 1px solid rgba(15, 23, 42, .04);
        }
    </style>
@endpush

@push('scripts-page')
    {{-- Thêm AOS nếu chưa có --}}
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true
            });
        }
    </script>
@endpush

@endsection
