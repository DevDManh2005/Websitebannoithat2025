@extends('layouts.app')

@section('content')

{{-- Banner đầu trang --}}
<div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
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

        <div class="mt-4" data-aos="fade-up">
            {{ $products->withQueryString()->links() }}
        </div>
    @else
        <div class="card card-glass text-center p-5 rounded-4" data-aos="fade-up">
            <div class="card-body">
                <i class="bi bi-search-heart" style="font-size: 3.5rem;"></i>
                <h5 class="mt-3">Không tìm thấy sản phẩm nào phù hợp</h5>
                <p class="text-muted">Vui lòng thử lại với từ khóa khác.</p>
                <a href="{{ route('home') }}" class="btn btn-brand rounded-pill mt-3">Quay lại trang chủ</a>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        /* =================== Banner =================== */
        .support-banner {
            height: 250px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .support-banner .breadcrumb-item a {
            color: var(--sand);
            text-decoration: none;
        }
        .support-banner .breadcrumb-item a:hover {
            color: var(--brand);
        }
        .support-banner .breadcrumb-item.active {
            color: var(--muted);
        }

        /* =================== Card Styles =================== */
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .rounded-4 {
            border-radius: 1rem !important;
        }

        /* =================== Product Hover Wrapper =================== */
        .product-hover-wrapper {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .product-hover-wrapper:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* =================== Button and Text Styles =================== */
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: 0.5rem 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn-brand:hover {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .text-brand {
            color: var(--brand);
        }
        .text-muted {
            color: var(--muted);
        }

        /* =================== Links =================== */
        a {
            color: var(--brand);
            text-decoration: none;
        }
        a:hover {
            color: var(--brand-600);
        }

        /* =================== Responsive Design =================== */
        @media (max-width: 991px) {
            .support-banner {
                height: 220px;
            }
            .support-banner .display-4 {
                font-size: 2rem;
            }
            .support-banner .lead {
                font-size: 1rem;
            }
            .col-lg-3 {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }
            .card-glass {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .support-banner {
                height: 180px;
            }
            .support-banner .display-4 {
                font-size: 1.8rem;
            }
            .support-banner .lead {
                font-size: 0.9rem;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .card-glass {
                padding: 1rem;
            }
            .card-body {
                padding: 1rem;
            }
            .btn-brand {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            .bi-search-heart {
                font-size: 3rem;
            }
        }

        @media (max-width: 575px) {
            .support-banner {
                height: 160px;
            }
            .support-banner .display-4 {
                font-size: 1.6rem;
            }
            .support-banner .lead {
                font-size: 0.85rem;
            }
            .support-banner .breadcrumb {
                font-size: 0.85rem;
            }
            .card-glass {
                padding: 0.75rem;
            }
            .card-body {
                padding: 0.75rem;
            }
            .btn-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .col-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .bi-search-heart {
                font-size: 2.5rem;
            }
        }
    </style>
@endpush

@push('scripts-page')
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                once: true,
                offset: 80
            });
        }
    </script>
@endpush

@endsection